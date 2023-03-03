<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerTaggingExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Customer Code',
            'Customer Name',
            'Customer Class',
            'Customer Segment',
            'Market Sector',
            'Market Sub-Sector',
            'Region',
            'Province',
            'Territory',
            'City',
        ];
    }

    public function title(): string
    {
        return "Customer Tagging";
    }
}
