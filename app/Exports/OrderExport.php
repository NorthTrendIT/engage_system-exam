<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
        // return [
        //     'No.',
        //     'Business Unit',
        //     'Order #',
        //     'Order Type',
        //     'Customer Code',
        //     'Customer Name',
        //     'Total',
        //     'Placed By',
        //     'Created Date',
        //     'Status',
        // ];

        return [
            'No.',
            'SO No.',
            'Order Date',
            'Order Time',
            'Creator Name',
            'Business Unit',
            'Branch',
            'Customer Code',
            'Customer Name',
            'Order Amount',
            'Delivery Address',
            'Approval Status',
            'Approved By',
            'Approval Date',
            'Approval Time',
            'Approval Duration',
            'Reason',
        ];
    }

    public function title(): string
    {
        return "Order";
    }
}
