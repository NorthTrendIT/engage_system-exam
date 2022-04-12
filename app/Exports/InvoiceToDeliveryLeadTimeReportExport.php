<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoiceToDeliveryLeadTimeReportExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Invoice Date',
            'Invoice No',
            'Sales Specialist',
            'Delivery Date',
            'Delivery Status',
            'Reference/Order No',
            'Lead Time',
        ];
    }

    public function title(): string
    {
        return "Invoice To Delivery Lead Time Report";
    }
}
