<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
use App\Traits\DateRange;

class TransportUsersTable extends Model
{
    use Sushi;
    use DBConnections;
    use DateRange;
    protected $table = 'TransportUsersTable';

    public function getRows()
    {
        $this->setDBConnection();
        $this->setDateRange('transport_paid');

        $query = DB::query()->select(
            'b.admission_no',
            'b.full_name',
            'a.start_date as s_date',
            'a.end_date as e_date',
            'f.name as bus_name',
            'g.name as bus_stop',
            'd.id as class_id',
            'd.class_name',
            'h.amount',
            'a.year_id',
            'e.name as aca_year',
            DB::raw("concat(e.name,' ',c.batch_name) as full_batch")
        )
            ->from('transport_student_point as a')
            ->join('student as b', 'a.student_id', 'b.id')
            ->join('batch as c', 'b.class_id', 'c.id')
            ->join('course as d', 'c.class_id', 'd.id')
            ->join('academic_year as e', 'a.year_id', 'e.id')
            ->join('transport_vehicle as f', 'a.vehicle_id', 'f.id')
            ->join('transport_point as g', 'a.point_id', 'g.id')
            ->join('transport_fee_head as h',function($join){
                $join->on('h.point_id', 'g.id')->on('a.year_id','h.year_id');
            }) 
            ->where([
                // ['a.status', 1],
                ['b.status', 1],
                ['c.status', 1],
                ['d.status', 1],
                ['f.status', 1],
                ['g.status', 1],    
                ['h.status', 1]
            ]);

        // return $query->tosql();
        
        $data = $query->get()->toarray();

        //Converting objects to array
        $i = 1;
        foreach ($data as &$obj) {
            $obj->id = $i++;
            $obj = (array)$obj;
        }

        // dd($data);
        return $data;
    }
}
