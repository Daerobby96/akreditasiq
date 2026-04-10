<?php

namespace App\Livewire;

use App\Models\Prodi;
use Livewire\Component;

class ProdiList extends Component
{
    public $nama, $kode, $jenjang, $lam_type;
    public $editingProdiId = null;
    public $isEditing = false;

    protected $rules = [
        'nama' => 'required|string',
        'kode' => 'required|string|unique:prodis,kode',
        'jenjang' => 'required|string',
        'lam_type' => 'required|string',
    ];

    public function render()
    {
        return view('livewire.prodi-list', [
            'prodis' => Prodi::all(),
            'lamOptions' => ['ban-pt', 'lam-infokom', 'lam-emba', 'lam-ptkes', 'lam-teknik', 'lamdik']
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditing = true;
    }

    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);
        $this->editingProdiId = $prodi->id;
        $this->nama = $prodi->nama;
        $this->kode = $prodi->kode;
        $this->jenjang = $prodi->jenjang;
        $this->lam_type = $prodi->lam_type;
        $this->isEditing = true;
    }

    public function save()
    {
        $validationRules = $this->rules;
        if ($this->editingProdiId) {
            $validationRules['kode'] = 'required|string|unique:prodis,kode,' . $this->editingProdiId;
        }

        $this->validate($validationRules);

        Prodi::updateOrCreate(
            ['id' => $this->editingProdiId],
            [
                'nama' => $this->nama,
                'kode' => $this->kode,
                'jenjang' => $this->jenjang,
                'lam_type' => $this->lam_type,
            ]
        );

        $this->resetFields();
        $this->dispatch('notify', message: 'Data Prodi berhasil disimpan!', type: 'success');
        $this->dispatch('prodi-updated'); // Force refresh navigation if needed
    }

    public function delete($id)
    {
        Prodi::destroy($id);
        $this->dispatch('notify', message: 'Prodi berhasil dihapus.', type: 'info');
    }

    public function resetFields()
    {
        $this->nama = '';
        $this->kode = '';
        $this->jenjang = 'S1';
        $this->lam_type = 'ban-pt';
        $this->editingProdiId = null;
        $this->isEditing = false;
    }
}
