<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Business Unit',
            'Card Code',
            'Card Name',
            'Email',
            'Universal Card Code',
            'Credit Limit',
            'Group',
            'Assignment Name',
            'Territory',
            'Class',
            'Created Date',
            // 'Status',
        ];
    }

    public function title(): string
    {
        return "Customer";
    }
}
