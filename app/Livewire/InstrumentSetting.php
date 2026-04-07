<?php

namespace App\Livewire;

use App\Models\LamTable;
use App\Models\LamTableColumn;

class InstrumentSetting extends \Livewire\Component
{
    public $lamTypes = [
        'ban-pt' => 'BAN-PT',
        'lam-infokom' => 'LAM-INFOKOM',
        'lam-ptkes' => 'LAM-PTKes',
        'lam-emba' => 'LAMEMBA'
    ];
    public $selectedLam = 'ban-pt';
    public $tables = [];

    // Edit Table
    public $showTableModal = false;
    public $editingTable = null;
    public $tableLabel = '';
    public $tableSlug = '';

    // Edit Column
    public $showColumnModal = false;
    public $editingColumn = null;
    public $selectedTableId = null;
    public $columnHeader = '';
    public $columnField = '';
    public $columnType = 'text';
    public $parentColumnId = null;

    public function mount()
    {
        $this->loadTables();
    }

    public function loadTables()
    {
        $this->tables = LamTable::where('lam_type', $this->selectedLam)
            ->with(['columns' => fn($q) => $q->orderBy('sort_order')])
            ->get();
    }

    public function updatedSelectedLam()
    {
        $this->loadTables();
    }

    // Table CRUD
    public function openCreateTable()
    {
        $this->editingTable = null;
        $this->tableLabel = '';
        $this->tableSlug = '';
        $this->showTableModal = true;
    }

    public function editTable($id)
    {
        $this->editingTable = LamTable::find($id);
        $this->tableLabel = $this->editingTable->label;
        $this->tableSlug = $this->editingTable->slug;
        $this->showTableModal = true;
    }

    public function saveTable()
    {
        $this->validate([
            'tableLabel' => 'required',
            'tableSlug' => 'required|alpha_dash'
        ]);

        if ($this->editingTable) {
            $this->editingTable->update([
                'label' => $this->tableLabel,
                'slug' => $this->tableSlug
            ]);
        } else {
            LamTable::create([
                'lam_type' => $this->selectedLam,
                'label' => $this->tableLabel,
                'slug' => $this->tableSlug
            ]);
        }

        $this->showTableModal = false;
        $this->loadTables();
        $this->dispatch('notify', message: 'Tabel berhasil disimpan!', type: 'success');
    }

    public function deleteTable($id)
    {
        LamTable::find($id)->delete();
        $this->loadTables();
        $this->dispatch('notify', message: 'Tabel berhasil dihapus!', type: 'info');
    }

    // Column CRUD
    public function openCreateColumn($tableId)
    {
        $this->selectedTableId = $tableId;
        $this->editingColumn = null;
        $this->columnHeader = '';
        $this->columnField = '';
        $this->columnType = 'text';
        $this->parentColumnId = null;
        $this->showColumnModal = true;
    }

    public function editColumn($id)
    {
        $this->editingColumn = LamTableColumn::find($id);
        $this->selectedTableId = $this->editingColumn->lam_table_id;
        $this->columnHeader = $this->editingColumn->header_name;
        $this->columnField = $this->editingColumn->field_name;
        $this->columnType = $this->editingColumn->data_type;
        $this->parentColumnId = $this->editingColumn->parent_id;
        $this->showColumnModal = true;
    }

    public function saveColumn()
    {
        $this->validate([
            'columnHeader' => 'required',
            'columnField' => 'required|alpha_dash',
        ]);

        if ($this->editingColumn) {
            $this->editingColumn->update([
                'header_name' => $this->columnHeader,
                'field_name' => $this->columnField,
                'data_type' => $this->columnType,
                'parent_id' => $this->parentColumnId
            ]);
        } else {
            LamTableColumn::create([
                'lam_table_id' => $this->selectedTableId,
                'header_name' => $this->columnHeader,
                'field_name' => $this->columnField,
                'data_type' => $this->columnType,
                'parent_id' => $this->parentColumnId,
                'sort_order' => LamTableColumn::where('lam_table_id', $this->selectedTableId)->count()
            ]);
        }

        $this->showColumnModal = false;
        $this->loadTables();
        $this->dispatch('notify', message: 'Kolom berhasil disimpan!', type: 'success');
    }

    public function deleteColumn($id)
    {
        LamTableColumn::find($id)->delete();
        $this->loadTables();
        $this->dispatch('notify', message: 'Kolom berhasil dihapus!', type: 'info');
    }

    public function render()
    {
        return view('livewire.instrument-setting')->layout('layouts.app');
    }
}
