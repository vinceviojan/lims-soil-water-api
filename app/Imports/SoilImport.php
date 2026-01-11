<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoilImport implements WithHeadingRow, ToArray
{
    public array $rows = [];

     public function array(array $array)
    {
        $this->rows = $array;
    }
}
