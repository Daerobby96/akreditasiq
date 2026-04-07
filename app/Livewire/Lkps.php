<?php

namespace App\Livewire;

use App\Models\Kriteria;
use Livewire\Component;

class Lkps extends Component
{
    public $selectedLam;
    public $activeTab;
    public $prodi;
    public $availableLams = [
        'ban-pt' => 'BAN-PT',
        'lam-infokom' => 'LAM-INFOKOM',
        'lam-ptkes' => 'LAM-PTKes',
        'lam-emba' => 'LAMEMBA'
    ];

    public $dynamicTables = [];
    public $activeTableId = null;
    public $tableData = [];
    public $editingId = null;
    public $editBuffer = [];

    public function mount()
    {
        $prodiId = session('selected_prodi_id');
        $this->prodi = \App\Models\Prodi::find($prodiId) ?? \App\Models\Prodi::first();
        
        $this->selectedLam = $this->prodi->lam_type ?? 'ban-pt';
        $this->loadDynamicTables();
        $this->loadTableData();
    }

    public function loadDynamicTables()
    {
        $this->dynamicTables = \App\Models\LamTable::where('lam_type', $this->selectedLam)
            ->with(['columns' => function($q) {
                $q->orderBy('sort_order');
            }])
            ->get();
            
        if ($this->dynamicTables->isNotEmpty()) {
            $firstTable = $this->dynamicTables->first();
            $this->activeTab = $firstTable->slug;
            $this->activeTableId = $firstTable->id;
        }
    }

    public function loadTableData()
    {
        if ($this->activeTableId) {
            $this->tableData = \App\Models\LkpsData::where('lam_table_id', $this->activeTableId)
                ->where('prodi_id', $this->prodi->id)
                ->orderBy('sort_order')
                ->get();
        } else {
            $this->tableData = [];
        }
    }

    public function setLam($lam)
    {
        $this->selectedLam = $lam;
        $this->loadDynamicTables();
        $this->loadTableData();
        $this->cancelEdit();
    }

    public function setTab($slug)
    {
        $this->activeTab = $slug;
        $table = collect($this->dynamicTables)->where('slug', $slug)->first();
        if ($table) {
            $this->activeTableId = $table->id;
            $this->loadTableData();
            $this->cancelEdit();
        }
    }

    public function nextTable()
    {
        $currentIndex = $this->dynamicTables->search(fn($t) => $t->slug === $this->activeTab);
        if ($currentIndex !== false && $currentIndex < $this->dynamicTables->count() - 1) {
            $next = $this->dynamicTables[$currentIndex + 1];
            $this->setTab($next->slug);
        }
    }

    public function previousTable()
    {
        $currentIndex = $this->dynamicTables->search(fn($t) => $t->slug === $this->activeTab);
        if ($currentIndex !== false && $currentIndex > 0) {
            $prev = $this->dynamicTables[$currentIndex - 1];
            $this->setTab($prev->slug);
        }
    }

    // --- ROW CRUD ---
    public function addRow()
    {
        if (!$this->activeTableId) return;

        $newRow = \App\Models\LkpsData::create([
            'lam_table_id' => $this->activeTableId,
            'prodi_id' => $this->prodi->id,
            'data_values' => [],
            'sort_order' => count($this->tableData)
        ]);

        $this->loadTableData();
        $this->editEntry($newRow->id);
    }

    public function editEntry($id)
    {
        $this->editingId = $id;
        $row = collect($this->tableData)->firstWhere('id', $id);
        $this->editBuffer = $row->data_values ?? [];
    }

    public function saveEntry()
    {
        if ($this->editingId) {
            \App\Models\LkpsData::find($this->editingId)->update([
                'data_values' => $this->editBuffer
            ]);
            $this->editingId = null;
            $this->loadTableData();
            $this->dispatch('notify', message: 'Data berhasil disimpan!', type: 'success');
        }
    }

    public function deleteRow($id)
    {
        \App\Models\LkpsData::find($id)->delete();
        $this->loadTableData();
        $this->dispatch('notify', message: 'Data berhasil dihapus!', type: 'info');
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editBuffer = [];
    }

    public function render()
    {
        $currentTable = collect($this->dynamicTables)->where('slug', $this->activeTab)->first();
        $rootColumns = [];
        $leafColumns = [];

        if ($currentTable) {
            $rootColumns = $currentTable->columns->whereNull('parent_id');
            $leafColumns = $currentTable->columns->filter(fn($col) => $col->children->isEmpty());
        }

        $currentIndex = $this->dynamicTables->search(fn($t) => $t->slug === $this->activeTab);
        $totalTables = $this->dynamicTables->count();
        
        // Define INFOKOM Grouping Logic (Simplified Mapping)
        $groupedTables = [
            'C2: Tata Pamong & Kerjasama' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_1_/', $t->slug)),
            'C3: Mahasiswa' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_[2-3]_/', $t->slug)),
            'C4: Sumber Daya Manusia' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_[4-9]_|tabel_10_/', $t->slug)),
            'C5: Keuangan & Sarana' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_19_/', $t->slug)),
            'C6: Pendidikan' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_20_|tabel_21_/', $t->slug)),
            'C7: Penelitian' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_11_|tabel_13_|tabel_14_|tabel_15_|tabel_16_|tabel_17_/', $t->slug)),
            'C8: Pengabdian Masyarakat' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_12_|tabel_18_/', $t->slug)),
            'C9: Luaran & Capaian' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_22_|tabel_23_|tabel_24_|tabel_25_|tabel_26_|tabel_27_|tabel_28_|tabel_29_|tabel_30_|tabel_31_/', $t->slug)),
        ];

        return view('livewire.lkps', [
            'currentTable' => $currentTable,
            'rootColumns' => $rootColumns,
            'leafColumns' => $leafColumns,
            'currentIndex' => $currentIndex,
            'totalTables' => $totalTables,
            'groupedTables' => $groupedTables
        ])->layout('layouts.app');
    }
}
