<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
use App\Traits\DateRange;

class TransportPaidTable extends Model
{
    use Sushi;
    use DBConnections;
    use DateRange;
    protected $table = 'TransportPaidTable';

    public function getRows()
    {
        $this->setDBConnection();
        $this->setDateRange('transport_paid');

        // return Student::from('transport_feecollection as a')
        //         ->join('student as b','a.student_id','b.id')
        //         ->join('batch as c','b.class_id','c.id')
        //         ->join('course as d','c.class_id','d.id')
        //         ->where('a.status',1)
        //         ->where('a.transaction_status',1)
        //         ->wheredate('a.date','>=',$start)
        //         ->wheredate('a.date','<=',$end);

        $query = DB::query()->select(
            'b.admission_no',
            'b.full_name',
            'a.date',
            'd.id as class_id',
            'd.class_name',
            'a.amount',
            'a.discount_amount as discount',
            DB::raw("concat(e.name,' ',c.batch_name) as full_batch"),
            DB::raw("monthname(a.month) as month"),
        )
            ->from('transport_feecollection as a')
            ->join('student as b', 'a.student_id', 'b.id')
            ->join('batch as c', 'b.class_id', 'c.id')
            ->join('course as d', 'c.class_id', 'd.id')
            ->join('academic_year as e', 'c.year_id', 'e.id')
            ->where('a.status', 1)
            ->where('a.transaction_status', 1)
            ->wheredate('a.date', '>=', $this->start_date)
            ->wheredate('a.date', '<=', $this->end_date);

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
