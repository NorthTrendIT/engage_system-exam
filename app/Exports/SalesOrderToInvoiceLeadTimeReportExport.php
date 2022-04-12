<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesOrderToInvoiceLeadTimeReportExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Order Date',
            'Order No',
            'Sales Specialist',
            'Invoice Date',
            'Invoice No',
            'Lead Time',
        ];
    }

    public function title(): string
    {
        return "Sales Order To Invoice Lead Time Report";
    }
}
