<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lkps', \App\Livewire\Lkps::class)->name('lkps');
    Route::get('/led', \App\Livewire\Led::class)->name('led');
    Route::get('/ai-audit', \App\Livewire\AiAudit::class)->name('ai-audit');
    Route::get('/monitoring', \App\Livewire\Monitoring::class)->name('monitoring');
    Route::get('/prodi', \App\Livewire\ProdiList::class)->name('prodi');
    Route::get('/data-dukung', \App\Livewire\DataDukung::class)->name('data-dukung');
    Route::get('/kriteria', \App\Livewire\KriteriaList::class)->name('kriteria');
    Route::get('/team-management', \App\Livewire\TeamManagement::class)->name('team-management');
    Route::get('/instrument-setting', \App\Livewire\InstrumentSetting::class)->name('instrument-setting');

    // Template routes
    Route::get('/templates', \App\Livewire\TemplateManager::class)->name('templates');
    Route::get('/templates/create', \App\Livewire\TemplateManager::class)->name('templates.create');
    Route::get('/templates/{template}/edit', \App\Livewire\TemplateManager::class)->name('templates.edit');

    // Collaborative editor routes
    Route::get('/documents/{document}/edit', \App\Livewire\CollaborativeEditor::class)->name('documents.edit');

    // Comments routes
    Route::get('/documents/{document}/comments', \App\Livewire\DocumentComments::class)->name('documents.comments');

    Route::get('/settings', \App\Livewire\Settings::class)->name('settings');
});

Route::middleware('auth')->group(function () {
    Route::get('/report/preview-led', [ReportController::class, 'previewLed'])->name('report.preview-led');
    Route::get('/report/preview-lkps', [ReportController::class, 'previewLkps'])->name('report.preview-lkps');
    Route::get('/report/download-lkps', [ReportController::class, 'downloadLkps'])->name('report.download-lkps');
    Route::get('/report/download-lkps-docx', [ReportController::class, 'downloadLkpsDocx'])->name('report.download-lkps-docx');
    Route::get('/report/download-led', [ReportController::class, 'downloadLed'])->name('report.download-led');
    Route::get('/report/download-led-docx', [ReportController::class, 'downloadLedDocx'])->name('report.download-led-docx');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
