<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LkpsTemplateExport implements FromCollection, WithHeadings
{
    protected $headers;

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function collection()
    {
        return collect([]);
    }
}
