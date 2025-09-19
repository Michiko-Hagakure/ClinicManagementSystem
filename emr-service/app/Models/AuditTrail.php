<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditTrail extends Model
{
    use HasFactory;

    protected $table = 'audit_trail';
    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'user_id',
        'user_name',
        'user_role',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'reason',
        'patient_id',
        'status_from',
        'status_to',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the related model instance
     */
    public function getRelatedModel()
    {
        $modelClass = match($this->table_name) {
            'lab_result' => \App\Models\LabResult::class,
            'patient' => \App\Models\Patient::class,
            'consultation' => \App\Models\Consultation::class,
            default => null
        };

        if ($modelClass) {
            return $modelClass::find($this->record_id);
        }

        return null;
    }

    /**
     * Scope for lab result audits
     */
    public function scopeLabResults($query)
    {
        return $query->where('table_name', 'lab_result');
    }

    /**
     * Scope for specific patient
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted action label
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'state_changed' => 'Status Changed',
            'file_uploaded' => 'File Uploaded',
            'reviewed' => 'Reviewed',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get summary of changes
     */
    public function getChangesSummaryAttribute(): string
    {
        if ($this->action === 'created') {
            return 'Record created';
        }

        if ($this->action === 'state_changed') {
            return "Status changed from {$this->status_from} to {$this->status_to}";
        }

        if ($this->changed_fields && is_array($this->changed_fields)) {
            $fields = implode(', ', $this->changed_fields);
            return "Modified: {$fields}";
        }

        return $this->action_label;
    }
}
