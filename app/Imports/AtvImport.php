<?php

namespace App\Imports;

use App\Models\atividade;
use Maatwebsite\Excel\Concerns\ToModel;

class AtvImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new atividade([
            
        ]);
    }
}
