<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AuditNarrativeJob implements ShouldQueue
{
    use Queueable;

    public $kriteriaId;
    public $prodiId;
    public $prodiName;
    public $narrative;

    /**
     * Create a new job instance.
     */
    public function __construct($kriteriaId, $prodiId, $prodiName, $narrative)
    {
        $this->kriteriaId = $kriteriaId;
        $this->prodiId = $prodiId;
        $this->prodiName = $prodiName;
        $this->narrative = $narrative;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $kriteria = \App\Models\Kriteria::find($this->kriteriaId);
        $aiService = new \App\Services\AccreditationAiService();
        
        // Prepare context
        $documents = \App\Models\Dokumen::where('kriteria_id', $this->kriteriaId)
            ->where('prodi_id', $this->prodiId)
            ->whereIn('status', ['submitted', 'approved'])
            ->get();
            
        $docContext = $documents->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama_file])->toArray();
        $lkpsRows = \App\Models\LkpsData::where('prodi_id', $this->prodiId)->get();
        $lkpsContext = $lkpsRows->map(fn($r) => $r->data_values)->toArray();

        $context = [
            'prodi' => $this->prodiName,
            'kriteria' => $kriteria->nama,
            'kode_kriteria' => $kriteria->kode,
            'dokumen_tersedia' => $docContext,
            'data_lkps' => $lkpsContext
        ];

        $result = $aiService->auditNarrative($kriteria, $this->narrative, $context);
        
        // Save to Narasi model metadata
        $narasi = \App\Models\Narasi::where('prodi_id', $this->prodiId)
            ->where('kriteria_id', $this->kriteriaId)
            ->first();

        if ($narasi) {
            $meta = $narasi->metadata ?? [];
            $meta['last_audit'] = array_merge($result, ['timestamp' => now()->toDateTimeString()]);
            $narasi->update(['metadata' => $meta]);
            
            // Log workflow
            $narasi->workflows()->create([
                'from_status' => null,
                'to_status' => null,
                'user_id' => null, // Background task
                'action' => 'ai_audit_job',
                'comment' => 'Background AI Audit completed. Score: ' . $result['predicted_score']
            ]);
        }
    }
}
