<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use App\Traits\DBConnections;
use Illuminate\Support\Facades\DB;

class Feehead extends Model
{
    use Sushi;
    use DBConnections;

    public function getRows()
    {
        $this->setDBConnection();
        $data1 = DB::table('new_feehead')
            ->select('id', 'name')
            ->where('status', 1)
            ->get()
            ->toarray();

        $data2 = DB::table('adm_feehead as a')
            ->join('academic_year as b', 'a.year_id','b.id')
            ->select(DB::raw('a.id+100 as id'),DB::raw("concat(b.name,' ',a.name) as name"))
            ->where('status', 1)
            ->get()
            ->toarray();
        
        $data = array_merge($data1,$data2);
        //Converting objects to array
        foreach ($data as &$obj) {
            $obj = (array)$obj;
        }

        // dd($data);

        return $data;
    }
}
