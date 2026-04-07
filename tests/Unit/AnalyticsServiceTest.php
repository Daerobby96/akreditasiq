<?php

use App\Models\Dokumen;
use App\Models\Kriteria;
use App\Models\PenilaianAi;
use App\Models\Prodi;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can generate score trend data', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    // Create some documents with AI evaluations
    $document = Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'prodi_id' => $prodi->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'approved'
    ]);

    PenilaianAi::create([
        'dokumen_id' => $document->id,
        'skor' => 3.5,
        'analisis_teks' => 'Test analysis',
        'gap_analysis' => 'Test gap',
        'rekomendasi' => 'Test recommendation',
        'engine' => 'gpt-4o-mini'
    ]);

    $analyticsService = new AnalyticsService();
    $data = $analyticsService->getScoreTrendData($prodi);

    expect($data)->toBeArray();
    expect(count($data))->toBe(6); // 6 months
    expect($data[0])->toHaveKeys(['month', 'score', 'date']);
});

it('can generate status distribution data', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

    $user = User::factory()->create();
    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10
    ]);

    // Create documents with different statuses
    Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'prodi_id' => $prodi->id,
        'nama_file' => 'draft.pdf',
        'file_path' => 'draft.pdf',
        'versi' => '1.0',
        'status' => 'draft'
    ]);

    Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'prodi_id' => $prodi->id,
        'nama_file' => 'approved.pdf',
        'file_path' => 'approved.pdf',
        'versi' => '1.0',
        'status' => 'approved'
    ]);

    $analyticsService = new AnalyticsService();
    $data = $analyticsService->getStatusDistributionData($prodi);

    expect($data)->toBeArray();
    expect(count($data))->toBeGreaterThan(0);
    expect($data[0])->toHaveKeys(['status', 'count', 'color', 'percentage']);
});

it('can generate criteria progress data', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

    $kriteria = Kriteria::create([
        'kode' => 'C1',
        'nama' => 'Kriteria 1',
        'deskripsi' => 'Deskripsi',
        'bobot' => 10,
        'lam_type' => 'sarjana'
    ]);

    $user = User::factory()->create();

    // Create approved document for the criteria
    Dokumen::create([
        'user_id' => $user->id,
        'kriteria_id' => $kriteria->id,
        'prodi_id' => $prodi->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'approved'
    ]);

    $analyticsService = new AnalyticsService();
    $data = $analyticsService->getCriteriaProgressData($prodi);

    expect($data)->toBeArray();
    expect(count($data))->toBeGreaterThan(0);
    expect($data[0])->toHaveKeys(['kriteria', 'nama', 'progress', 'total_docs', 'approved_docs', 'avg_score', 'color']);
});

it('can generate AI analysis heatmap data', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

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
        'prodi_id' => $prodi->id,
        'nama_file' => 'test.pdf',
        'file_path' => 'test.pdf',
        'versi' => '1.0',
        'status' => 'approved'
    ]);

    // Create AI evaluations with different scores
    PenilaianAi::create([
        'dokumen_id' => $document->id,
        'skor' => 3.8,
        'analisis_teks' => 'High score analysis',
        'gap_analysis' => 'Test gap',
        'rekomendasi' => 'Test recommendation',
        'engine' => 'gpt-4o-mini'
    ]);

    PenilaianAi::create([
        'dokumen_id' => $document->id,
        'skor' => 2.5,
        'analisis_teks' => 'Medium score analysis',
        'gap_analysis' => 'Test gap',
        'rekomendasi' => 'Test recommendation',
        'engine' => 'gpt-4o-mini'
    ]);

    $analyticsService = new AnalyticsService();
    $data = $analyticsService->getAiAnalysisHeatmap($prodi);

    expect($data)->toBeArray();
    expect(count($data))->toBe(4); // Should have 4 score ranges
    expect($data[0])->toHaveKeys(['range', 'count', 'color', 'percentage']);
});

it('can generate accreditation insights', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

    $analyticsService = new AnalyticsService();
    $insights = $analyticsService->getAccreditationInsights($prodi);

    expect($insights)->toBeArray();
    // Should at least have progress insight even with no data
    expect(count($insights))->toBeGreaterThan(0);
    expect($insights[0])->toHaveKeys(['type', 'message']);
});

it('returns correct data types', function () {
    $prodi = Prodi::create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TI',
        'lam_type' => 'sarjana'
    ]);

    $analyticsService = new AnalyticsService();

    // Test all methods return arrays
    expect($analyticsService->getScoreTrendData($prodi))->toBeArray();
    expect($analyticsService->getStatusDistributionData($prodi))->toBeArray();
    expect($analyticsService->getCriteriaProgressData($prodi))->toBeArray();
    expect($analyticsService->getWorkflowActivityData($prodi))->toBeArray();
    expect($analyticsService->getAiAnalysisHeatmap($prodi))->toBeArray();
    expect($analyticsService->getAccreditationInsights($prodi))->toBeArray();
});