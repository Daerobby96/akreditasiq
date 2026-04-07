<?php

namespace App\Livewire;

use App\Models\Kriteria;
use App\Models\Prodi;
use Livewire\Component;

class DataDukung extends Component
{
    public $selectedKriteriaId = null;
    public $prodi;

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = Prodi::find($prodiId) ?? Prodi::first();
        
        // Auto-select first criteria if available
        $firstKriteria = Kriteria::where('lam_type', $this->prodi->lam_type)->orderBy('kode', 'asc')->first();
        if ($firstKriteria) {
            $this->selectedKriteriaId = $firstKriteria->id;
        }
    }

    public function selectKriteria($id)
    {
        $this->selectedKriteriaId = $id;
        $this->dispatch('kriteria-selected', kriteriaId: $id);
    }

    public function render()
    {
        $kriterias = Kriteria::where('lam_type', $this->prodi->lam_type)->orderBy('kode', 'asc')->get();
        
        return view('livewire.data-dukung', [
            'kriterias' => $kriterias
        ])->layout('layouts.app');
    }
}
