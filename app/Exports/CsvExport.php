<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CsvExport implements FromCollection, WithStrictNullComparison
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
}
