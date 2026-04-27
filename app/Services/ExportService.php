<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Narasi;
use App\Models\LkpsData;
use App\Models\LamTable;
use App\Models\Prodi;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

class ExportService
{
    public function exportLedToWord($prodiId)
    {
        $prodi = Prodi::findOrFail($prodiId);
        $phpWord = $this->createBaseDocument();
        $section = $this->addSection($phpWord);

        // Cover Page
        $section->addTitle("LAPORAN EVALUASI DIRI (LED)", 1);
        $section->addTitle(strtoupper($prodi->nama), 1);
        $section->addTitle("UNIVERSITAS TEKNOLOGI CILEGON", 1);
        $section->addTextBreak(5);
        $section->addTitle("TAHUN " . date('Y'), 1);
        $section->addPageBreak();

        $section->addTitle("DAFTAR ISI LED", 2);
        $section->addText("Dokumen ini digenerate secara otomatis oleh AKRE SMART AI.", ['italic' => true]);
        $section->addPageBreak();

        $kriterias = Kriteria::where('lam_type', $prodi->lam_type)->orderBy('kode', 'asc')->get();
        foreach ($kriterias as $kriteria) {
            $section->addTitle("KRITERIA {$kriteria->kode}: {$kriteria->nama}", 2);
            $narasi = Narasi::where('prodi_id', $prodiId)->where('kriteria_id', $kriteria->id)->first();
            
            if ($narasi && is_array($narasi->content)) {
                foreach ($narasi->content as $label => $text) {
                    $section->addTitle(ucfirst(str_replace('_', ' ', $label)), 3);
                    if (empty($text)) {
                        $section->addText('-', ['italic' => true]);
                        continue;
                    }
                    try {
                        $cleanHtml = str_replace(['&nbsp;', '<br>', '<p>&nbsp;</p>'], [' ', '<br/>', ''], $text);
                        \PhpOffice\PhpWord\Shared\Html::addHtml($section, '<div>' . $cleanHtml . '</div>', false, false);
                    } catch (\Exception $e) {
                        $section->addText(strip_tags($text));
                    }
                    $section->addTextBreak(1);
                }
            } else {
                $section->addText("Belum ada narasi untuk kriteria ini.", ['italic' => true]);
            }
        }

        return $phpWord;
    }

    public function exportLkpsToWord($prodiId)
    {
        $prodi = Prodi::findOrFail($prodiId);
        $phpWord = $this->createBaseDocument();
        $section = $this->addSection($phpWord);

        // Cover Page
        $section->addTitle("LAPORAN KINERJA PROGRAM STUDI (LKPS)", 1);
        $section->addTitle(strtoupper($prodi->nama), 1);
        $section->addTitle("UNIVERSITAS TEKNOLOGI CILEGON", 1);
        $section->addTextBreak(5);
        $section->addTitle("TAHUN " . date('Y'), 1);
        $section->addPageBreak();

        $tables = LamTable::where('lam_type', $prodi->lam_type)->get();
        foreach ($tables as $table) {
            $section->addTitle($table->label, 2);
            $lkpsDataRows = LkpsData::where('prodi_id', $prodiId)
                ->where('lam_table_id', $table->id)
                ->orderBy('sort_order')
                ->get();
            
            if ($lkpsDataRows->isNotEmpty()) {
                $this->addWordTable($section, $table, $lkpsDataRows);
            } else {
                $section->addText("Data tabel belum diisi.", ['italic' => true]);
            }
            $section->addTextBreak(2);
        }

        return $phpWord;
    }

    protected function createBaseDocument()
    {
        $phpWord = new PhpWord();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16, 'color' => '333333'], ['alignment' => Jc::CENTER, 'spaceAfter' => 240]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14], ['spaceBefore' => 240, 'spaceAfter' => 120]);
        $phpWord->addTitleStyle(3, ['bold' => true, 'size' => 12], ['spaceBefore' => 120, 'spaceAfter' => 60]);
        return $phpWord;
    }

    protected function addSection($phpWord)
    {
        return $phpWord->addSection([
            'marginTop' => Converter::cmToTwip(2.5),
            'marginBottom' => Converter::cmToTwip(2.5),
            'marginLeft' => Converter::cmToTwip(3),
            'marginRight' => Converter::cmToTwip(2.5),
        ]);
    }

    protected function addWordTable($section, $lamTable, $lkpsDataRows)
    {
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'width' => 100 * 50,
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
        ];
        $phpWordTable = $section->addTable($tableStyle);
        $fontHeader = ['bold' => true, 'size' => 9];
        $cellHeaderStyle = ['valign' => 'center', 'bgColor' => 'F2F2F2'];
        $paraCenter = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];

        $allCols = $lamTable->columns;
        $rootColumns = $allCols->whereNull('parent_id')->sortBy('sort_order');
        
        // --- Level 1 Header ---
        $phpWordTable->addRow();
        foreach ($rootColumns as $col) {
            $label = $col->label ?: ($col->header_name ?: 'Col');
            $colspan = $col->colspan;
            $phpWordTable->addCell(null, array_merge($cellHeaderStyle, ['gridSpan' => $colspan]))
                ->addText(htmlspecialchars($label), $fontHeader, $paraCenter);
        }

        // --- Level 2 Header (Sub-headers) ---
        // Check if any root column has children
        $hasChildren = $rootColumns->some(fn($c) => $allCols->where('parent_id', $c->id)->isNotEmpty());
        if ($hasChildren) {
            $phpWordTable->addRow();
            foreach ($rootColumns as $rootCol) {
                $children = $allCols->where('parent_id', $rootCol->id)->sortBy('sort_order');
                if ($children->isNotEmpty()) {
                    foreach ($children as $child) {
                        $label = $child->label ?: ($child->header_name ?: '-');
                        $phpWordTable->addCell(null, array_merge($cellHeaderStyle, ['gridSpan' => $child->colspan]))
                            ->addText(htmlspecialchars($label), $fontHeader, $paraCenter);
                    }
                } else {
                    // This is a leaf node at Level 1, we should logically vMerge but for now just add empty cell
                    // or repeat label (simpler for Word compatibility in some versions)
                    $phpWordTable->addCell(null, $cellHeaderStyle)->addText('', $fontHeader, $paraCenter);
                }
            }
        }

        // Get leaf columns for data mapping
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

        // Data Rows
        foreach ($lkpsDataRows as $row) {
            $phpWordTable->addRow();
            $data = $row->data_values;
            foreach ($leafColumns as $col) {
                $val = $data[$col->field_name] ?? ($data[$col->header_name] ?? '');
                $phpWordTable->addCell()->addText(htmlspecialchars((string)$val), ['size' => 9]);
            }
        }
    }
}
