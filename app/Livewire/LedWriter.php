<?php

namespace App\Livewire;

use App\Services\ExportService;
use Livewire\Component;

class LedWriter extends Component
{
    public $prodi;
    public $isExporting = false;

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
    }

    public function downloadLed(ExportService $exportService)
    {
        return $this->handleExport($exportService, 'exportLedToWord', 'LED');
    }

    public function downloadLkps(ExportService $exportService)
    {
        return $this->handleExport($exportService, 'exportLkpsToWord', 'LKPS');
    }

    protected function handleExport(ExportService $exportService, $method, $prefix)
    {
        $this->isExporting = true;
        try {
            $phpWord = $exportService->$method($this->prodi->id);
            $filename = "{$prefix}_" . str_replace(' ', '_', $this->prodi->nama) . "_" . date('Ymd') . ".docx";
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            $this->isExporting = false;
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            $this->isExporting = false;
            $this->dispatch('notify', message: "Gagal mengekspor {$prefix}: " . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.led-writer')->layout('layouts.app');
    }
}
