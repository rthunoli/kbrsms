<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
// use App\Traits\DateRange;

class TransportUsersTable extends Model
{
    use Sushi;
    use DBConnections;
    // use DateRange;
    protected $table = 'TransportUsersTable';

/*
select b.admission_no,b.full_name,c.batch_name as batch,e.name as aca_year,a.amount,
d.id as class_id,d.class_name,g.name as bus_name,h.name as bus_stop,
concat(e.name,' ',c.batch_name) as full_batch,
f.start_date as s_date,f.end_date as e_date
from
(
select student_id,year_id,sum(amount) as amount
from transport_feecollection a
where a.status=1 and a.transaction_status=1 
group by student_id,year_id
)
as a
inner join student b
on a.student_id=b.id
inner join batch c
on b.class_id=c.id
inner join course d
on c.class_id=d.id
inner join academic_year e
on a.year_id=e.id
inner join transport_student_point f
on a.student_id=f.student_id and a.year_id=f.year_id
inner join transport_vehicle g
on f.vehicle_id=g.id
inner join transport_point h
on f.point_id=h.id
*/

    public function getRows()
    {
        $this->setDBConnection();
        // $this->setDateRange('transport_paid');

        $transport_fee = DB::query()->select(
            'student_id','year_id',
            DB::raw("sum(amount) as amount")
        )
        ->from('transport_feecollection')
        ->where([['status',1],['transaction_status',1]])
        ->groupby('student_id')
        ->groupby('year_id');

        // dd($transport_fee->tosql());
        
        $query = DB::query()->select(
            'b.admission_no',
            'b.full_name',
            'f.start_date as s_date',
            'f.end_date as e_date',
            'g.name as bus_name',
            'h.name as bus_stop',
            'd.id as class_id',
            'd.class_name',
            'a.amount',
            'a.year_id',
            'e.name as aca_year',
            DB::raw("concat(e.name,' ',c.batch_name) as full_batch")
        )
            ->fromsub($transport_fee,'a')
            ->join('student as b', 'a.student_id', 'b.id')
            ->join('batch as c', 'b.class_id', 'c.id')
            ->join('course as d', 'c.class_id', 'd.id')
            ->join('academic_year as e', 'a.year_id', 'e.id')
            ->join('transport_student_point as f',function($join){
                $join->on('a.student_id','f.student_id')
                ->on('a.year_id','f.year_id');
            })
            ->join('transport_vehicle as g', 'f.vehicle_id', 'g.id')
            ->join('transport_point as h', 'f.point_id', 'h.id')
            // ->join('transport_fee_head as i',function($join){
            //     $join->on('i.point_id', 'h.id')->on('a.year_id','i.year_id');
            // }) 
            ->where([
                ['b.status', 1],
                // ['c.status', 1],
                // ['d.status', 1],
                // ['f.status', 1],
                // ['g.status', 1],    
                // ['h.status', 1]
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
