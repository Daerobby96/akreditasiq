<?php

namespace App\Livewire;

use App\Models\Kriteria;
use Livewire\Component;

class KriteriaList extends Component
{
    public $kriterias;
    public $isEditing = false;
    public $editingId = null;
    public $kode, $nama, $bobot, $lam_type;
    public $selectedLamType = 'ban-pt';
    public $lamOptions = ['ban-pt', 'lam-infokom', 'lam-emba', 'lam-ptkes'];

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $prodi = \App\Models\Prodi::find($prodiId) ?? auth()->user()->prodi ?? \App\Models\Prodi::first();
        
        $this->selectedLamType = $prodi->lam_type ?? 'ban-pt';
        $this->refreshKriterias();
    }

    public function setLamFilter($type)
    {
        $this->selectedLamType = $type;
        $this->refreshKriterias();
    }

    public function refreshKriterias()
    {
        $this->kriterias = Kriteria::where('lam_type', $this->selectedLamType)
            ->withCount(['dokumens as submitted_count' => function($query) {
                $query->where('status', 'submitted');
            }, 'dokumens as approved_count' => function($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('kode', 'asc')
            ->get();
    }

    public function resetFields()
    {
        $this->kode = '';
        $this->nama = '';
        $this->bobot = 0;
        $this->lam_type = $this->selectedLamType;
        $this->editingId = null;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $k = Kriteria::find($id);
        $this->editingId = $k->id;
        $this->kode = $k->kode;
        $this->nama = $k->nama;
        $this->bobot = $k->bobot;
        $this->lam_type = $k->lam_type;
        $this->isEditing = true;
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate([
            'kode' => 'required',
            'nama' => 'required',
            'bobot' => 'required|numeric',
            'lam_type' => 'required'
        ]);

        Kriteria::updateOrCreate(
            ['id' => $this->editingId],
            [
                'kode' => $this->kode,
                'nama' => $this->nama,
                'bobot' => $this->bobot,
                'lam_type' => $this->lam_type
            ]
        );

        $lastSavedLam = $this->lam_type;
        $this->resetFields();
        $this->selectedLamType = $lastSavedLam; // Match filter to what was just saved
        $this->refreshKriterias();
        $this->dispatch('notify', message: 'Kriteria berhasil disimpan!', type: 'success');
    }

    public function delete($id)
    {
        Kriteria::destroy($id);
        $this->refreshKriterias();
        $this->dispatch('notify', message: 'Kriteria berhasil dihapus!', type: 'success');
    }

    public function render()
    {
        return view('livewire.kriteria-list')->layout('layouts.app');
    }
}
