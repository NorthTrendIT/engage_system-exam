<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductExport implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Product Name',
            'Product Brand',
            'Product Code',
            'Product Line',
            'Product Category',
            'Created Date',
            'Status',
            'Online Price',
            'Commercial Price',
            'SRP',
            'RDLP',
            'RDLP-2',
        ];
    }

    public function title(): string
    {
        return "Product";
    }
}
