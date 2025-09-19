<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an audit trail entry for record changes
     * Based on Mary Angels Diagnostic Clinic accountability requirements
     */
    public function logAction(
        Model $model,
        string $action,
        ?User $user = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null
    ): AuditTrail {
        $user = $user ?? Auth::user();
        
        // Determine changed fields
        $changedFields = [];
        if ($oldValues && $newValues) {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        }

        // Get patient ID if this is a lab result
        $patientId = null;
        if (method_exists($model, 'patient') && $model->patient) {
            $patientId = $model->patient->patient_id;
        } elseif (isset($model->patient_id)) {
            $patientId = $model->patient_id;
        }

        // Detect status changes for workflow auditing
        $statusFrom = null;
        $statusTo = null;
        if (isset($oldValues['status']) && isset($newValues['status'])) {
            $statusFrom = $oldValues['status'];
            $statusTo = $newValues['status'];
            if ($statusFrom !== $statusTo) {
                $action = 'state_changed';
            }
        }

        $auditData = [
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'action' => $action,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'reason' => $reason,
            'patient_id' => $patientId,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
        ];

        return AuditTrail::create($auditData);
    }

    /**
     * Log record creation
     */
    public function logCreated(Model $model, ?User $user = null, ?string $reason = null): AuditTrail
    {
        return $this->logAction(
            $model,
            'created',
            $user,
            null,
            $model->getAttributes(),
            $reason
        );
    }

    /**
     * Log record update with before/after values
     */
    public function logUpdated(Model $model, array $originalValues, ?User $user = null, ?string $reason = null): AuditTrail
    {
        return $this->logAction(
            $model,
            'updated',
            $user,
            $originalValues,
            $model->getAttributes(),
            $reason
        );
    }

    /**
     * Log record deletion
     */
    public function logDeleted(Model $model, ?User $user = null, ?string $reason = null): AuditTrail
    {
        return $this->logAction(
            $model,
            'deleted',
            $user,
            $model->getAttributes(),
            null,
            $reason
        );
    }

    /**
     * Log workflow state change specifically
     */
    public function logWorkflowChange(
        Model $model,
        string $fromStatus,
        string $toStatus,
        ?User $user = null,
        ?string $reason = null
    ): AuditTrail {
        return $this->logAction(
            $model,
            'state_changed',
            $user,
            ['status' => $fromStatus],
            ['status' => $toStatus],
            $reason
        );
    }

    /**
     * Log file upload/attachment
     */
    public function logFileUpload(Model $model, string $fileName, ?User $user = null): AuditTrail
    {
        return $this->logAction(
            $model,
            'file_uploaded',
            $user,
            null,
            ['file_uploaded' => $fileName],
            "File uploaded: {$fileName}"
        );
    }

    /**
     * Log doctor review action
     */
    public function logReviewed(Model $model, ?User $user = null, ?string $doctorNotes = null): AuditTrail
    {
        return $this->logAction(
            $model,
            'reviewed',
            $user,
            null,
            ['reviewed_by' => $user->name, 'doctor_notes' => $doctorNotes],
            "Lab result reviewed by doctor"
        );
    }

    /**
     * Get audit history for a specific record
     */
    public function getRecordHistory(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return AuditTrail::where('table_name', $model->getTable())
            ->where('record_id', $model->getKey())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get audit history for a patient across all records
     */
    public function getPatientHistory(string $patientId): \Illuminate\Database\Eloquent\Collection
    {
        return AuditTrail::where('patient_id', $patientId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent audit activity for dashboard
     */
    public function getRecentActivity(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return AuditTrail::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit statistics for reporting
     */
    public function getAuditStats(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = AuditTrail::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return [
            'total_actions' => $query->count(),
            'actions_by_type' => $query->select('action')
                ->selectRaw('count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'actions_by_user' => $query->select('user_name', 'user_role')
                ->selectRaw('count(*) as count')
                ->groupBy('user_name', 'user_role')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->toArray(),
            'lab_results_modified' => $query->where('table_name', 'lab_result')->count(),
            'workflow_changes' => $query->where('action', 'state_changed')->count(),
        ];
    }

    /**
     * Check if action should be audited (filter out sensitive or unnecessary fields)
     */
    private function shouldAuditField(string $field): bool
    {
        $excludedFields = [
            'updated_at',
            'remember_token',
            'password',
            'email_verified_at'
        ];

        return !in_array($field, $excludedFields);
    }

    /**
     * Clean values for audit logging (remove sensitive data)
     */
    private function cleanValuesForAudit(array $values): array
    {
        $cleaned = [];
        foreach ($values as $key => $value) {
            if ($this->shouldAuditField($key)) {
                $cleaned[$key] = $value;
            }
        }
        return $cleaned;
    }
}
