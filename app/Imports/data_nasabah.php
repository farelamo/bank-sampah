<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class data_nasabah implements ToCollection,WithHeadingRow, SkipsOnError, SkipsEmptyRows,WithStartRow
{
    use Importable, SkipsErrors, SkipsFailures;
    public function startRow(): int 
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        return $collection;
    }
}
