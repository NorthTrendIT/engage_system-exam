<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ReturnOrderReportExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'No',
            'Customer Name',
            'Business Unit',
            'Return Date',
            'Return No',
            'Sales Specialist',
            'Return Amount',
            'Item Code',
            'Item Description',
            'Item Price',
            'Qty Returned',
            'Remarks',
        ];
    }

    public function title(): string
    {
        return "Return Order Report";
    }
}
