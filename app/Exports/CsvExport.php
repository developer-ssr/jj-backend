<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CsvExport implements FromCollection, WithStrictNullComparison, WithColumnWidths
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function new($data): CsvExport
    {
        return new self($data);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }


    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'D' => 20,            
        ];
    }
}
