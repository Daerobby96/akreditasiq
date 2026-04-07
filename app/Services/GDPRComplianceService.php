<?php

namespace App\Services;

use App\Models\User;
use App\Models\Dokumen;
use App\Models\Comment;
use App\Models\Workflow;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GDPRComplianceService
{
    /**
     * Export all user data for GDPR compliance
     */
    public function exportUserData(int $userId): array
    {
        $user = User::findOrFail($userId);

        $exportData = [
            'export_timestamp' => now()->toISOString(),
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'documents' => $this->getUserDocuments($userId),
            'comments' => $this->getUserComments($userId),
            'workflows' => $this->getUserWorkflows($userId),
            'notifications' => $this->getUserNotifications($userId),
            'activity_log' => $this->getUserActivityLog($userId),
            'data_usage' => $this->getDataUsageSummary($userId),
        ];

        return $exportData;
    }

    /**
     * Delete all user data (Right to be Forgotten)
     */
    public function deleteUserData(int $userId): array
    {
        $user = User::findOrFail($userId);
        $deletionSummary = [];

        DB::beginTransaction();

        try {
            // Count data before deletion
            $deletionSummary['before_deletion'] = [
                'documents_count' => Dokumen::where('user_id', $userId)->count(),
                'comments_count' => Comment::where('user_id', $userId)->count(),
                'workflows_count' => Workflow::where('user_id', $userId)->count(),
                'notifications_count' => Notification::where('notifiable_type', User::class)
                    ->where('notifiable_id', $userId)->count(),
            ];

            // Anonymize user data instead of hard delete (GDPR best practice)
            $anonymizedData = [
                'name' => 'User Deleted',
                'email' => 'deleted_' . $userId . '@anonymized.local',
                'password' => bcrypt('deleted_account_' . time()),
                'email_verified_at' => null,
                'remember_token' => null,
                'gdpr_deleted_at' => now(),
            ];

            $user->update($anonymizedData);

            // Soft delete or anonymize related data
            $this->anonymizeUserDocuments($userId);
            $this->anonymizeUserComments($userId);
            $this->anonymizeUserWorkflows($userId);

            DB::commit();

            $deletionSummary['status'] = 'completed';
            $deletionSummary['anonymized_at'] = now();

            Log::info('User data GDPR deletion completed', [
                'user_id' => $userId,
                'anonymized_at' => now(),
                'deletion_summary' => $deletionSummary
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $deletionSummary['status'] = 'failed';
            $deletionSummary['error'] = $e->getMessage();

            Log::error('User data GDPR deletion failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }

        return $deletionSummary;
    }

    /**
     * Get data portability export in JSON format
     */
    public function generateDataPortabilityExport(int $userId): string
    {
        $exportData = $this->exportUserData($userId);

        // Add GDPR compliance metadata
        $exportData['gdpr_compliance'] = [
            'export_format' => 'JSON',
            'gdpr_version' => 'GDPR Article 20',
            'export_date' => now()->toISOString(),
            'data_controller' => config('app.name'),
            'retention_period' => 'Data retained for legal compliance only',
            'user_rights' => [
                'right_to_access' => 'Article 15',
                'right_to_rectification' => 'Article 16',
                'right_to_erasure' => 'Article 17',
                'right_to_restriction' => 'Article 18',
                'right_to_data_portability' => 'Article 20',
                'right_to_object' => 'Article 21'
            ]
        ];

        return json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Check if user has consented to data processing
     */
    public function hasDataProcessingConsent(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // Check if user has consented (you might want to store this in a separate table)
        return $user->gdpr_consent_given_at !== null;
    }

    /**
     * Record data processing consent
     */
    public function recordDataProcessingConsent(int $userId, array $consentData = []): bool
    {
        $user = User::findOrFail($userId);

        $consentRecord = [
            'gdpr_consent_given_at' => now(),
            'gdpr_consent_ip' => request()->ip(),
            'gdpr_consent_user_agent' => request()->userAgent(),
            'gdpr_consent_data' => $consentData
        ];

        return $user->update($consentRecord);
    }

    /**
     * Withdraw data processing consent
     */
    public function withdrawDataProcessingConsent(int $userId): bool
    {
        $user = User::findOrFail($userId);

        return $user->update([
            'gdpr_consent_given_at' => null,
            'gdpr_consent_withdrawn_at' => now(),
            'gdpr_consent_data' => null
        ]);
    }

    /**
     * Get data retention summary for user
     */
    public function getDataRetentionSummary(int $userId): array
    {
        $user = User::findOrFail($userId);

        return [
            'account_created' => $user->created_at,
            'last_activity' => $user->updated_at,
            'documents_count' => Dokumen::where('user_id', $userId)->count(),
            'data_volume' => $this->calculateUserDataVolume($userId),
            'retention_period' => $this->getRetentionPeriod($user),
            'next_review_date' => $this->calculateNextReviewDate($user),
        ];
    }

    /**
     * Check if user data should be reviewed for deletion
     */
    public function shouldReviewForDeletion(int $userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // Review inactive accounts older than 2 years
        $inactiveThreshold = now()->subYears(2);

        return $user->updated_at < $inactiveThreshold;
    }

    // Private helper methods

    private function getUserDocuments(int $userId): array
    {
        return Dokumen::where('user_id', $userId)
            ->with(['kriteria', 'prodi'])
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->nama_file,
                    'kriteria' => $doc->kriteria->nama ?? null,
                    'prodi' => $doc->prodi->nama ?? null,
                    'status' => $doc->status,
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at,
                    'metadata' => $doc->metadata,
                ];
            })
            ->toArray();
    }

    private function getUserComments(int $userId): array
    {
        return Comment::where('user_id', $userId)
            ->with(['document.kriteria'])
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'document_name' => $comment->document->nama_file ?? null,
                    'content' => $comment->content,
                    'is_resolved' => $comment->is_resolved,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ];
            })
            ->toArray();
    }

    private function getUserWorkflows(int $userId): array
    {
        return Workflow::where('user_id', $userId)
            ->with(['trackable'])
            ->get()
            ->map(function ($workflow) {
                return [
                    'id' => $workflow->id,
                    'action' => $workflow->action,
                    'old_value' => $workflow->old_value,
                    'new_value' => $workflow->new_value,
                    'created_at' => $workflow->created_at,
                ];
            })
            ->toArray();
    }

    private function getUserNotifications(int $userId): array
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            })
            ->toArray();
    }

    private function getUserActivityLog(int $userId): array
    {
        // This would typically come from a dedicated audit log table
        // For now, we'll use recent workflow activities
        return Workflow::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    private function getDataUsageSummary(int $userId): array
    {
        return [
            'documents_created' => Dokumen::where('user_id', $userId)->count(),
            'comments_posted' => Comment::where('user_id', $userId)->count(),
            'templates_used' => Dokumen::where('user_id', $userId)
                ->whereNotNull('template_id')
                ->count(),
            'workflows_participated' => Workflow::where('user_id', $userId)->count(),
            'account_age_days' => User::find($userId)->created_at->diffInDays(now()),
        ];
    }

    private function anonymizeUserDocuments(int $userId): void
    {
        Dokumen::where('user_id', $userId)->update([
            'user_id' => null, // Remove user association
            'metadata->original_owner' => $userId,
            'metadata->anonymized_at' => now(),
        ]);
    }

    private function anonymizeUserComments(int $userId): void
    {
        Comment::where('user_id', $userId)->update([
            'user_id' => null,
            'content' => '[Comment anonymized for GDPR compliance]',
            'mentions' => null,
        ]);
    }

    private function anonymizeUserWorkflows(int $userId): void
    {
        Workflow::where('user_id', $userId)->update([
            'user_id' => null,
            'metadata->original_user' => $userId,
            'metadata->anonymized_at' => now(),
        ]);
    }

    private function calculateUserDataVolume(int $userId): int
    {
        // Estimate data volume (this is approximate)
        $documents = Dokumen::where('user_id', $userId)->count();
        $comments = Comment::where('user_id', $userId)->count();
        $workflows = Workflow::where('user_id', $userId)->count();

        // Rough estimate: 10KB per document, 1KB per comment, 0.5KB per workflow
        return ($documents * 10240) + ($comments * 1024) + ($workflows * 512);
    }

    private function getRetentionPeriod(User $user): string
    {
        // Define retention periods based on user activity
        $lastActivity = max($user->created_at, $user->updated_at);

        if ($lastActivity->diffInYears(now()) >= 2) {
            return 'Review for deletion (inactive > 2 years)';
        }

        return 'Active account - retain until account deletion';
    }

    private function calculateNextReviewDate(User $user): string
    {
        // Review inactive accounts annually, active accounts every 2 years
        $lastActivity = max($user->created_at, $user->updated_at);

        if ($lastActivity->diffInYears(now()) >= 1) {
            return $lastActivity->addYears(1)->toDateString();
        }

        return $lastActivity->addYears(2)->toDateString();
    }
}
