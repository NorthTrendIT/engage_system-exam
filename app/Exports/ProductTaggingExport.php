<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductTaggingExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
{
    protected $records, $headers;

    public function __construct($records, $headers){
        $this->records = $records;
        $this->headers = $headers;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->records);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return "Product Tagging";
    }
}
