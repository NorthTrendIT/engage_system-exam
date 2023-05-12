<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportUser implements FromCollection,WithHeadings,WithTitle,ShouldAutoSize
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
            'Card Code',
            'Business Unit',
            'Role',
            'First Name',
            'Last Name',
            'Email',
            'Parent',
            'Status',
        ];
    }

    public function title(): string
    {
        return "User";
    }
}
