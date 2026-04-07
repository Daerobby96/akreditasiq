<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\Workflow;
use App\Notifications\WorkflowTransitionNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DocumentWorkflowService
{
    protected array $workflowStages = [
        'upload' => ['next' => 'ai_analysis', 'prev' => null],
        'ai_analysis' => ['next' => 'review', 'prev' => 'upload'],
        'review' => ['next' => 'revision', 'prev' => 'ai_analysis'],
        'revision' => ['next' => 'final_approval', 'prev' => 'review'],
        'final_approval' => ['next' => null, 'prev' => 'revision']
    ];

    protected array $statusMapping = [
        'upload' => 'draft',
        'ai_analysis' => 'submitted',
        'review' => 'under_review',
        'revision' => 'revision',
        'final_approval' => 'approved'
    ];

    /**
     * Transition document to next workflow stage
     */
    public function advanceStage(Dokumen $document, array $data = []): bool
    {
        $currentStage = $document->workflow_stage;
        $nextStage = $this->workflowStages[$currentStage]['next'] ?? null;

        if (!$nextStage) {
            return false; // Already at final stage
        }

        return $this->transitionToStage($document, $nextStage, $data);
    }

    /**
     * Transition document to previous workflow stage
     */
    public function revertStage(Dokumen $document, array $data = []): bool
    {
        $currentStage = $document->workflow_stage;
        $prevStage = $this->workflowStages[$currentStage]['prev'] ?? null;

        if (!$prevStage) {
            return false; // Already at initial stage
        }

        return $this->transitionToStage($document, $prevStage, $data);
    }

    /**
     * Transition document to specific stage
     */
    public function transitionToStage(Dokumen $document, string $stage, array $data = []): bool
    {
        if (!array_key_exists($stage, $this->workflowStages)) {
            return false;
        }

        try {
            // Update document
            $updateData = [
                'workflow_stage' => $stage,
                'status' => $this->statusMapping[$stage] ?? $document->status
            ];

            // Add timestamps based on stage
            switch ($stage) {
                case 'ai_analysis':
                    $updateData['submitted_at'] = now();
                    break;
                case 'review':
                    $updateData['reviewed_at'] = now();
                    break;
                case 'final_approval':
                    $updateData['approved_at'] = now();
                    break;
            }

            // Add reviewer notes if provided
            if (isset($data['reviewer_notes'])) {
                $updateData['reviewer_notes'] = $data['reviewer_notes'];
            }

            $document->update($updateData);

            // Log workflow transition
            $this->logWorkflowTransition($document, $stage, $data);

            // Send notifications
            $this->sendWorkflowNotifications($document, $stage, $data);

            return true;

        } catch (\Exception $e) {
            Log::error('Workflow transition failed: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'stage' => $stage,
                'data' => $data
            ]);
            return false;
        }
    }

    /**
     * Check if document can advance to next stage
     */
    public function canAdvance(Dokumen $document): bool
    {
        $currentStage = $document->workflow_stage;
        return isset($this->workflowStages[$currentStage]['next']);
    }

    /**
     * Check if document can revert to previous stage
     */
    public function canRevert(Dokumen $document): bool
    {
        $currentStage = $document->workflow_stage;
        return isset($this->workflowStages[$currentStage]['prev']);
    }

    /**
     * Get available actions for document
     */
    public function getAvailableActions(Dokumen $document): array
    {
        $actions = [];

        if ($this->canAdvance($document)) {
            $nextStage = $this->workflowStages[$document->workflow_stage]['next'];
            $actions[] = [
                'action' => 'advance',
                'stage' => $nextStage,
                'label' => $this->getStageLabel($nextStage)
            ];
        }

        if ($this->canRevert($document)) {
            $prevStage = $this->workflowStages[$document->workflow_stage]['prev'];
            $actions[] = [
                'action' => 'revert',
                'stage' => $prevStage,
                'label' => $this->getStageLabel($prevStage)
            ];
        }

        return $actions;
    }

    /**
     * Get human-readable label for stage
     */
    protected function getStageLabel(string $stage): string
    {
        return match ($stage) {
            'upload' => 'Upload Dokumen',
            'ai_analysis' => 'Analisis AI',
            'review' => 'Review',
            'revision' => 'Revisi',
            'final_approval' => 'Persetujuan Final',
            default => ucfirst($stage)
        };
    }

    /**
     * Log workflow transition
     */
    protected function logWorkflowTransition(Dokumen $document, string $stage, array $data = []): void
    {
        Workflow::create([
            'trackable_type' => Dokumen::class,
            'trackable_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => 'stage_changed',
            'old_value' => $document->getOriginal('workflow_stage'),
            'new_value' => $stage,
            'metadata' => array_merge($data, [
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ])
        ]);
    }

    /**
     * Submit document for AI analysis
     */
    public function submitForAnalysis(Dokumen $document): bool
    {
        if ($document->workflow_stage !== 'upload') {
            return false;
        }

        return $this->transitionToStage($document, 'ai_analysis');
    }

    /**
     * Mark document as reviewed
     */
    public function markAsReviewed(Dokumen $document, string $notes = null): bool
    {
        if ($document->workflow_stage !== 'review') {
            return false;
        }

        return $this->transitionToStage($document, 'revision', [
            'reviewer_notes' => $notes
        ]);
    }

    /**
     * Approve document
     */
    public function approveDocument(Dokumen $document): bool
    {
        if ($document->workflow_stage !== 'final_approval') {
            return false;
        }

        return $this->transitionToStage($document, 'final_approval');
    }

    /**
     * Send workflow transition notifications
     */
    protected function sendWorkflowNotifications(Dokumen $document, string $stage, array $data = []): void
    {
        try {
            // Notify document owner
            if ($document->user && $document->user->id !== Auth::id()) {
                $document->user->notify(new WorkflowTransitionNotification(
                    $document,
                    $document->getOriginal('workflow_stage'),
                    $stage,
                    $data
                ));
            }

            // Notify reviewers/admins based on stage
            $this->notifyRelevantUsers($document, $stage, $data);

        } catch (\Exception $e) {
            Log::error('Failed to send workflow notifications: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'stage' => $stage
            ]);
        }
    }

    /**
     * Notify relevant users based on workflow stage
     */
    protected function notifyRelevantUsers(Dokumen $document, string $stage, array $data = []): void
    {
        // Get users with specific roles based on stage
        $usersToNotify = [];

        switch ($stage) {
            case 'ai_analysis':
                // Notify AI reviewers or admins
                $usersToNotify = \App\Models\User::role(['admin', 'reviewer'])->get();
                break;
            case 'review':
                // Notify document reviewers
                $usersToNotify = \App\Models\User::role(['reviewer', 'admin'])->get();
                break;
            case 'final_approval':
                // Notify all admins
                $usersToNotify = \App\Models\User::role('admin')->get();
                break;
        }

        if ($usersToNotify->isNotEmpty()) {
            Notification::send(
                $usersToNotify->where('id', '!=', Auth::id()), // Exclude current user
                new WorkflowTransitionNotification($document, $document->getOriginal('workflow_stage'), $stage, $data)
            );
        }
    }
}
