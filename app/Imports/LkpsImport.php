<?php

namespace App\Imports;

use App\Models\LkpsData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LkpsImport implements ToCollection
{
    protected $tableId;
    protected $prodiId;
    protected $columns;

    public function __construct($tableId, $prodiId, $columns)
    {
        $this->tableId = $tableId;
        $this->prodiId = $prodiId;
        $this->columns = $columns;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // Take the FIRST row as headers manually
        $headerRow = $rows->first();
        $dataRows = $rows->slice(1);

        // Helper to clean string aggressively
        $cleanForMatch = function($str) {
            $str = (string)$str;
            // Remove newlines, tabs, and multiple spaces
            $str = str_replace(["\r", "\n", "\t"], ' ', $str);
            $str = preg_replace('/\s+/', ' ', $str);
            // Lowercase and remove all non-alphanumeric
            return strtolower(preg_replace('/[^a-z0-9]/', '', trim($str)));
        };

        // Get existing count for sort_order
        $count = LkpsData::where('lam_table_id', $this->tableId)
            ->where('prodi_id', $this->prodiId)
            ->count();

        $importedCount = 0;

        foreach ($dataRows as $row) {
            $dataValues = [];
            
            foreach ($this->columns as $column) {
                if (!$column->field_name) continue;

                $val = null;
                $labelToFind = $cleanForMatch($column->label ?: $column->header_name);

                foreach ($headerRow as $idx => $headerText) {
                    $cleanHeader = $cleanForMatch($headerText);
                    
                    if ($cleanHeader === $labelToFind && $cleanHeader !== '') {
                        $val = $row[$idx] ?? null;
                        break;
                    }
                }
                
                if ($val !== null) {
                    // Handle checkbox-like values
                    if ($column->data_type === 'boolean' || str_starts_with($column->field_name, 'pl') || (in_array($column->field_name, ['ts','ts1','ts2']) && in_array($this->tableId, [290, 302, 365]))) {
                        $val = in_array(strtolower(trim($val)), ['v', 'x', '1', 'ya', 'yes', 'true', 'checked', 'y']) ? 'v' : null;
                    }
                    $dataValues[$column->field_name] = $val;
                }
            }

            // Only create if row has at least one piece of data
            if (count(array_filter($dataValues)) > 0) {
                LkpsData::create([
                    'lam_table_id' => $this->tableId,
                    'prodi_id' => $this->prodiId,
                    'data_values' => $dataValues,
                    'sort_order' => $count++,
                ]);
                $importedCount++;
            }
        }
        
        session()->put('import_summary', $importedCount);
    }
}
