<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use App\Traits\DBConnections;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use Sushi;
    use DBConnections;

    public function getRows()
    {
        $this->setDBConnection();
        $data = DB::table('course')->get()->toarray();

        //Converting objects to array
        foreach ($data as &$obj) {
            $obj = (array)$obj;
        }

        // dd($data);
        return $data;
    }
}
