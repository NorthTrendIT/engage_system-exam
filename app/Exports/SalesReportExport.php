<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesReportExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
{
    protected $records;

    public function __construct($records){
        $this->records = $records;
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
        return [
            'No.',
            'Invoice #',
            'Date',
            'Product Code',
            'Product Name',
            'Brand',
            'Business Unit',
            'Total Quantity',
            'UOM',
            'Unit Price',
            'Net Amount',
            'Status'
        ];
    }

    public function title(): string
    {
        return "Sales Report";
    }
}
