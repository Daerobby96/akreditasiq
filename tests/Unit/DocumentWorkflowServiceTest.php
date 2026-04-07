<?php

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\User;
use App\Models\Workflow;
use App\Services\DocumentWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can advance document to next workflow stage', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft',
        'workflow_stage' => 'upload'
    ]);

    $service = new DocumentWorkflowService();

    // Test advancing from upload to ai_analysis
    $result = $service->advanceStage($document);
    expect($result)->toBeTrue();

    $document->refresh();
    expect($document->workflow_stage)->toBe('ai_analysis');
    expect($document->status)->toBe('submitted');
});

it('can revert document to previous workflow stage', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'submitted',
        'workflow_stage' => 'ai_analysis'
    ]);

    $service = new DocumentWorkflowService();

    // Test reverting from ai_analysis to upload
    $result = $service->revertStage($document);
    expect($result)->toBeTrue();

    $document->refresh();
    expect($document->workflow_stage)->toBe('upload');
    expect($document->status)->toBe('draft');
});

it('logs workflow transitions', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft',
        'workflow_stage' => 'upload'
    ]);

    $service = new DocumentWorkflowService();

    // Advance stage to trigger logging
    $service->advanceStage($document);

    // Check if workflow log was created
    $workflowLog = Workflow::where('trackable_type', Dokumen::class)
        ->where('trackable_id', $document->id)
        ->first();

    expect($workflowLog)->not->toBeNull();
    expect($workflowLog->action)->toBe('stage_changed');
    expect($workflowLog->old_value)->toBe('upload');
    expect($workflowLog->new_value)->toBe('ai_analysis');
});

it('provides available actions for document', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft',
        'workflow_stage' => 'upload'
    ]);

    $service = new DocumentWorkflowService();
    $actions = $service->getAvailableActions($document);

    expect($actions)->toHaveCount(1);
    expect($actions[0]['action'])->toBe('advance');
    expect($actions[0]['stage'])->toBe('ai_analysis');
});

it('prevents invalid stage transitions', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'draft',
        'workflow_stage' => 'final_approval' // At final stage
    ]);

    $service = new DocumentWorkflowService();

    // Should not be able to advance from final stage
    $result = $service->advanceStage($document);
    expect($result)->toBeFalse();

    // Should not be able to revert from initial stage
    $initialDocument = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test2.pdf',
        'file_path' => 'test2.pdf',
        'versi' => '1.0',
        'status' => 'draft',
        'workflow_stage' => 'upload'
    ]);

    $result = $service->revertStage($initialDocument);
    expect($result)->toBeFalse();
});

it('handles document approval workflow', function () {
    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'under_review',
        'workflow_stage' => 'final_approval'
    ]);

    $service = new DocumentWorkflowService();

    // Test approval
    $result = $service->approveDocument($document);
    expect($result)->toBeTrue();

    $document->refresh();
    expect($document->workflow_stage)->toBe('final_approval');
    expect($document->status)->toBe('approved');
    expect($document->approved_at)->not->toBeNull();
});