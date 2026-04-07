<?php

use App\Models\User;
use App\Models\Dokumen;
use App\Models\Comment;
use App\Models\Kriteria;
use App\Services\GDPRComplianceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('exports user data successfully', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Test kriteria',
        'bobot' => 10
    ]);

    // Create some test data
    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft'
    ]);

    $comment = Comment::create([
        'user_id' => $user->id,
        'document_id' => $document->id,
        'content' => 'Test comment'
    ]);

    $gdprService = new GDPRComplianceService();
    $exportData = $gdprService->exportUserData($user->id);

    expect($exportData)->toHaveKey('user_info');
    expect($exportData)->toHaveKey('documents');
    expect($exportData)->toHaveKey('comments');
    expect($exportData)->toHaveKey('data_usage');
    expect($exportData['user_info']['id'])->toBe($user->id);
    expect(count($exportData['documents']))->toBe(1);
    expect(count($exportData['comments']))->toBe(1);
});

test('generates data portability export in JSON format', function () {
    $user = User::factory()->create();

    $gdprService = new GDPRComplianceService();
    $jsonExport = $gdprService->generateDataPortabilityExport($user->id);

    expect($jsonExport)->toBeString();

    $parsedData = json_decode($jsonExport, true);
    expect($parsedData)->toHaveKey('gdpr_compliance');
    expect($parsedData['gdpr_compliance'])->toHaveKey('user_rights');
    expect($parsedData['gdpr_compliance']['export_format'])->toBe('JSON');
});

test('anonymizes user data for GDPR deletion', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);

    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Test kriteria',
        'bobot' => 10
    ]);

    // Create test data
    Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft'
    ]);

    Comment::create([
        'user_id' => $user->id,
        'document_id' => 1,
        'content' => 'Test comment'
    ]);

    $gdprService = new GDPRComplianceService();
    $deletionResult = $gdprService->deleteUserData($user->id);

    expect($deletionResult['status'])->toBe('completed');

    // Refresh user from database
    $user->refresh();

    expect($user->name)->toBe('User Deleted');
    expect($user->email)->toContain('anonymized.local');
    expect($user->gdpr_deleted_at)->not->toBeNull();
});

test('checks data processing consent', function () {
    $userWithoutConsent = User::factory()->create();
    $userWithConsent = User::factory()->create([
        'gdpr_consent_given_at' => now()
    ]);

    $gdprService = new GDPRComplianceService();

    expect($gdprService->hasDataProcessingConsent($userWithoutConsent->id))->toBeFalse();
    expect($gdprService->hasDataProcessingConsent($userWithConsent->id))->toBeTrue();
});

test('records data processing consent', function () {
    $user = User::factory()->create();

    $gdprService = new GDPRComplianceService();
    $result = $gdprService->recordDataProcessingConsent($user->id, [
        'marketing_emails' => true,
        'analytics' => false
    ]);

    expect($result)->toBeTrue();

    $user->refresh();
    expect($user->gdpr_consent_given_at)->not->toBeNull();
    expect($user->gdpr_consent_data)->toHaveKey('marketing_emails');
});

test('withdraws data processing consent', function () {
    $user = User::factory()->create([
        'gdpr_consent_given_at' => now(),
        'gdpr_consent_data' => ['marketing' => true]
    ]);

    $gdprService = new GDPRComplianceService();
    $result = $gdprService->withdrawDataProcessingConsent($user->id);

    expect($result)->toBeTrue();

    $user->refresh();
    expect($user->gdpr_consent_given_at)->toBeNull();
    expect($user->gdpr_consent_withdrawn_at)->not->toBeNull();
});

test('provides data retention summary', function () {
    $user = User::factory()->create();

    $gdprService = new GDPRComplianceService();
    $summary = $gdprService->getDataRetentionSummary($user->id);

    expect($summary)->toHaveKey('account_created');
    expect($summary)->toHaveKey('last_activity');
    expect($summary)->toHaveKey('documents_count');
    expect($summary)->toHaveKey('data_volume');
    expect($summary)->toHaveKey('retention_period');
});

test('identifies users for deletion review', function () {
    $activeUser = User::factory()->create([
        'updated_at' => now()
    ]);

    $inactiveUser = User::factory()->create([
        'updated_at' => now()->subYears(3) // 3 years ago
    ]);

    $gdprService = new GDPRComplianceService();

    expect($gdprService->shouldReviewForDeletion($activeUser->id))->toBeFalse();
    expect($gdprService->shouldReviewForDeletion($inactiveUser->id))->toBeTrue();
});

test('handles non-existent users gracefully', function () {
    $gdprService = new GDPRComplianceService();

    expect(fn() => $gdprService->exportUserData(999999))->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    expect($gdprService->hasDataProcessingConsent(999999))->toBeFalse();
});

test('anonymizes related data during deletion', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Test kriteria',
        'bobot' => 10
    ]);

    // Create related data
    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft'
    ]);

    $comment = Comment::create([
        'user_id' => $user->id,
        'document_id' => $document->id,
        'content' => 'Original comment'
    ]);

    $gdprService = new GDPRComplianceService();
    $gdprService->deleteUserData($user->id);

    // Check that related data is anonymized
    $document->refresh();
    expect($document->user_id)->toBeNull();
    expect($document->metadata)->toHaveKey('original_owner');

    $comment->refresh();
    expect($comment->user_id)->toBeNull();
    expect($comment->content)->toContain('anonymized');
});

test('includes GDPR compliance metadata in exports', function () {
    $user = User::factory()->create();

    $gdprService = new GDPRComplianceService();
    $export = $gdprService->generateDataPortabilityExport($user->id);

    $data = json_decode($export, true);

    expect($data)->toHaveKey('gdpr_compliance');
    expect($data['gdpr_compliance'])->toHaveKey('user_rights');
    expect($data['gdpr_compliance']['user_rights'])->toHaveKey('right_to_access');
    expect($data['gdpr_compliance']['user_rights'])->toHaveKey('right_to_erasure');
    expect($data['gdpr_compliance']['gdpr_version'])->toBe('GDPR Article 20');
});
