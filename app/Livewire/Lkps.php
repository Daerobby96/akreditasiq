<?php

namespace App\Livewire;

use App\Models\Kriteria;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LkpsImport;
use App\Exports\LkpsTemplateExport;

class Lkps extends Component
{
    public $selectedLam;
    public $activeTab;
    public $prodi;
    public $dynamicTables = [];
    public $activeTableId = null;
    public $tableData = [];
    public $editingId = null;
    public $showPreview = false;
    public $editBuffer = [];
    public $excelFile;
    
    use WithFileUploads;

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
        $data = $row->data_values ?? [];
        
        // Initialize buffer with current data AND all possible columns for this table
        $currentTable = collect($this->dynamicTables)->where('id', $this->activeTableId)->first();
        $this->editBuffer = [];
        
        if ($currentTable) {
            foreach ($currentTable->columns as $column) {
                if ($column->field_name) {
                    $this->editBuffer[$column->field_name] = $data[$column->field_name] ?? '';
                }
            }
        }
    }

    public function saveEntry()
    {
        if ($this->editingId) {
            $row = \App\Models\LkpsData::find($this->editingId);
            if ($row) {
                $row->update([
                    'data_values' => $this->editBuffer
                ]);
            }
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

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $currentTable = collect($this->dynamicTables)->where('id', $this->activeTableId)->first();
        $leafColumns = $currentTable->columns->filter(fn($col) => $col->children->isEmpty());

        try {
            Excel::import(new LkpsImport($this->activeTableId, $this->prodi->id, $leafColumns), $this->excelFile->getRealPath());
            
            $count = session()->pull('import_summary', 0);
            $this->excelFile = null;
            $this->loadTableData();
            
            if ($count > 0) {
                $this->dispatch('notify', message: "Berhasil! {$count} data baru telah ditambahkan.", type: 'success');
            } else {
                $this->dispatch('notify', message: 'Tidak ada data baru yang cocok ditemukan dalam file.', type: 'warning');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal mengimpor file: ' . $e->getMessage(), type: 'error');
        }
    }

    public function downloadTemplate()
    {
        $currentTable = collect($this->dynamicTables)->where('id', $this->activeTableId)->first();
        if (!$currentTable) return;

        $leafColumns = $currentTable->columns->filter(fn($col) => $col->children->isEmpty());
        $headers = $leafColumns->map(fn($col) => $col->label ?: $col->header_name)->toArray();
        
        return Excel::download(new LkpsTemplateExport($headers), 'template_' . $currentTable->slug . '.xlsx');
    }

    public function render()
    {
        $currentTable = collect($this->dynamicTables)->where('slug', $this->activeTab)->first();
        $rootColumns = [];
        $leafColumns = [];

        if ($currentTable) {
            $allCols = $currentTable->columns;
            $rootColumns = $allCols->whereNull('parent_id');
            
            // Recursive helper to get leaf columns in correct visual order
            $getLeafs = function($cols) use (&$getLeafs, $allCols) {
                $leafs = collect();
                foreach($cols as $col) {
                    $children = $allCols->where('parent_id', $col->id);
                    if ($children->isEmpty()) {
                        $leafs->push($col);
                    } else {
                        $leafs = $leafs->concat($getLeafs($children));
                    }
                }
                return $leafs;
            };

            $leafColumns = $getLeafs($rootColumns);
        }

        $currentIndex = $this->dynamicTables->search(fn($t) => $t->slug === $this->activeTab);
        $totalTables = $this->dynamicTables->count();
        
        // Define Grouping Logic
        if ($this->selectedLam === 'lam-infokom') {
            $groupedTables = [
                '1. Budaya Mutu' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_1_/', $t->slug)),
                '2. Relevansi Pendidikan' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_2_/', $t->slug)),
                '3. Relevansi Penelitian' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_3_/', $t->slug)),
                '4. Relevansi PkM' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_4_/', $t->slug)),
                '5. Akuntabilitas' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_5_/', $t->slug)),
                '6. Diferensiasi Misi' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_6_/', $t->slug)),
            ];
        } elseif ($this->selectedLam === 'lam-emba') {
            $groupedTables = [
                'A. Sumber Daya Manusia' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_[1-5]_/', $t->slug)),
                'B. Keuangan' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_6_/', $t->slug)),
                'C. Luaran dan Capaian Tridharma' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_([7-9]|1[0-9]|2[0-3])_/', $t->slug)),
            ];
        } elseif ($this->selectedLam === 'lam-teknik') {
            $groupedTables = [
                '1. Diferensiasi Misi' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_1_/', $t->slug)),
                '2. Akuntabilitas' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_2_/', $t->slug)),
                '3. Relevansi Pendidikan, Penelitian & PkM' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_3_/', $t->slug)),
                '4. Sumber Daya Manusia' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_4_/', $t->slug)),
                '5. Sarana, Prasarana & K3L' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_5_/', $t->slug)),
                '6. Mahasiswa & Luaran' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_6_/', $t->slug)),
                '7. Penjaminan Mutu' => $this->dynamicTables->filter(fn($t) => preg_match('/tabel_7_/', $t->slug)),
            ];
        } else {
            // Default BAN-PT / Other LAM 9-Criteria style
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
        }

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
