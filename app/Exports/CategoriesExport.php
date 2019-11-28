<?php

namespace App\Exports;

use App\Model\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::select('id','name','code')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'code',
        ];
    }
}
