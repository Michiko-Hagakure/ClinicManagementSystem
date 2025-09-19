<?php

namespace App\Services;

use App\Models\LabResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LabWorkflowService
{
    /**
     * Lab workflow states and transitions based on Mary Angels Diagnostic Clinic interview.
     * 
     * Workflow from interview:
     * 1. Tests requested -> pending
     * 2. Medical staff starts -> in_progress  
     * 3. Medical staff completes -> completed
     * 4. With results/files -> ready_for_review
     * 5. Doctor reviews -> reviewed
     */
    
    const VALID_STATES = [
        'pending',
        'in_progress', 
        'completed',
        'ready_for_review',
        'reviewed'
    ];

    const STATE_TRANSITIONS = [
        'pending' => ['in_progress', 'completed'], // Can skip to completed if simple test
        'in_progress' => ['completed', 'pending'], // Can go back if issues
        'completed' => ['ready_for_review', 'reviewed'], // Auto-transition with files, or direct review
        'ready_for_review' => ['reviewed', 'completed'], // Doctor reviews or sends back
        'reviewed' => [] // Final state
    ];

    /**
     * Validate if state transition is allowed based on clinic workflow
     */
    public function canTransitionTo(string $currentState, string $newState, User $user): bool
    {
        // Check if transition is valid
        if (!in_array($newState, self::STATE_TRANSITIONS[$currentState] ?? [])) {
            return false;
        }

        // Role-based transition permissions
        return match($newState) {
            'in_progress' => $user->isMedicalStaff() || $user->isOwner(),
            'completed' => $user->isMedicalStaff() || $user->isOwner(),
            'ready_for_review' => $user->isMedicalStaff() || $user->isOwner(),
            'reviewed' => $user->canReviewLabResults(),
            'pending' => $user->isOwner(), // Only owner can reset to pending
            default => false
        };
    }

    /**
     * Automatically determine next state based on lab result data and user role
     */
    public function determineNextState(LabResult $labResult, User $user, array $updates = []): string
    {
        $currentState = $labResult->status;
        
        // If medical staff is updating with results
        if ($user->isMedicalStaff()) {
            return match($currentState) {
                'pending' => 'in_progress',
                'in_progress' => $this->shouldMarkReadyForReview($labResult, $updates) ? 'ready_for_review' : 'completed',
                'completed' => $this->shouldMarkReadyForReview($labResult, $updates) ? 'ready_for_review' : 'completed',
                default => $currentState
            };
        }

        // If doctor is reviewing
        if ($user->canReviewLabResults() && $currentState === 'ready_for_review') {
            return 'reviewed';
        }

        return $currentState;
    }

    /**
     * Check if lab result should be marked as ready for review
     * Based on clinic workflow: results with files or significant findings need doctor review
     */
    private function shouldMarkReadyForReview(LabResult $labResult, array $updates = []): bool
    {
        // Has file attachments (X-ray images, lab reports, etc.)
        if ($labResult->file_path || isset($updates['file_path'])) {
            return true;
        }

        // Has detailed results that need interpretation
        if ($labResult->result_notes || isset($updates['result_notes'])) {
            return true;
        }

        // Has result values outside normal range
        if ($labResult->result_value || isset($updates['result_value'])) {
            return true;
        }

        // STAT or urgent priority always needs review
        if ($labResult->priority === 'stat' || $labResult->priority === 'urgent') {
            return true;
        }

        // Complex test types that typically need doctor interpretation
        $complexTests = ['CT Scan', 'MRI', 'X-ray', 'Ultrasound'];
        if (in_array($labResult->type, $complexTests)) {
            return true;
        }

        return false;
    }

    /**
     * Apply state transition with proper timestamps and validation
     */
    public function transitionState(LabResult $labResult, string $newState, User $user, array $additionalData = []): bool
    {
        if (!$this->canTransitionTo($labResult->status, $newState, $user)) {
            Log::warning("Invalid state transition attempted", [
                'lab_result_id' => $labResult->result_id,
                'from_state' => $labResult->status,
                'to_state' => $newState,
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            return false;
        }

        $updates = ['status' => $newState];

        // Set appropriate timestamps and staff assignments
        switch($newState) {
            case 'in_progress':
                $updates['performed_by'] = $user->name;
                break;
                
            case 'completed':
                $updates['completed_at'] = now();
                if (!$labResult->performed_by) {
                    $updates['performed_by'] = $user->name;
                }
                break;
                
            case 'ready_for_review':
                $updates['completed_at'] = $updates['completed_at'] ?? now();
                if (!$labResult->performed_by) {
                    $updates['performed_by'] = $user->name;
                }
                break;
                
            case 'reviewed':
                $updates['reviewed_by'] = $user->name;
                $updates['reviewed_at'] = now();
                break;
        }

        // Merge additional data
        $updates = array_merge($updates, $additionalData);

        $labResult->update($updates);

        Log::info("Lab result state transition successful", [
            'lab_result_id' => $labResult->result_id,
            'from_state' => $labResult->getOriginal('status'),
            'to_state' => $newState,
            'user_id' => $user->id,
            'user_role' => $user->role
        ]);

        return true;
    }

    /**
     * Get allowed next states for a lab result based on current user
     */
    public function getAllowedNextStates(LabResult $labResult, User $user): array
    {
        $currentState = $labResult->status;
        $possibleStates = self::STATE_TRANSITIONS[$currentState] ?? [];
        
        return array_filter($possibleStates, function($state) use ($currentState, $user) {
            return $this->canTransitionTo($currentState, $state, $user);
        });
    }

    /**
     * Get state display information
     */
    public function getStateInfo(string $state): array
    {
        return match($state) {
            'pending' => [
                'label' => 'Pending',
                'color' => 'warning',
                'icon' => 'clock',
                'description' => 'Test requested, waiting to be performed'
            ],
            'in_progress' => [
                'label' => 'In Progress', 
                'color' => 'info',
                'icon' => 'arrow-repeat',
                'description' => 'Test is currently being performed'
            ],
            'completed' => [
                'label' => 'Completed',
                'color' => 'success', 
                'icon' => 'check-circle',
                'description' => 'Test completed, results available'
            ],
            'ready_for_review' => [
                'label' => 'Ready for Review',
                'color' => 'primary',
                'icon' => 'eye', 
                'description' => 'Results completed, awaiting doctor review'
            ],
            'reviewed' => [
                'label' => 'Reviewed',
                'color' => 'dark',
                'icon' => 'check-all',
                'description' => 'Results reviewed and interpreted by doctor'
            ],
            default => [
                'label' => 'Unknown',
                'color' => 'secondary',
                'icon' => 'question',
                'description' => 'Unknown status'
            ]
        };
    }

    /**
     * Check if workflow is completed (reviewed or simple completed tests)
     */
    public function isWorkflowComplete(LabResult $labResult): bool
    {
        if ($labResult->status === 'reviewed') {
            return true;
        }

        // Simple completed tests that don't need review
        if ($labResult->status === 'completed' && !$this->shouldMarkReadyForReview($labResult)) {
            return true;
        }

        return false;
    }
}
