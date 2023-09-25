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
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Arr;

class trx_sampah implements ToCollection,WithHeadingRow,SkipsEmptyRows,WithStartRow, WithMapping

{
    use Importable, SkipsErrors, SkipsFailures;
    public function startRow(): int 
    {
        return 3;
    }

    public function map($data): array
    {
        $data['tanggal'] = Date::excelToDateTimeObject($data['tanggal'])->format('Y-m-d');
        return Arr::except($data, ['24', '25', '26', '27', '28', '29']);
    }

    public function collection(Collection $collection)
    {
        return $collection;
    }

    // public function rules(): array 
    // {
    //     return [
    //         'sid' => ['required']
    //     ];
    // }

}
