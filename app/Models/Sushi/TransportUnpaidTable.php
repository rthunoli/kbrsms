<?php

namespace App\Models\Sushi;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
use App\Traits\DateRange;

class TransportUnpaidTable extends Model
{
    use Sushi;
    use DBConnections;
    use DateRange;
    protected $table = 'TransportUnpaidTable';

    public function getRows()
    {
        $this->setDBConnection();
        $this->setDateRange('transport_unpaid');

        $union = "";
        if ($this->isSetDateRange()) {
            $start = Carbon::createFromDate($this->start_date);
            $end = Carbon::createFromDate($this->end_date);
            while ($end >= $start) {
                if ($union == "") {
                    $union = "'" . $start . "' as month ";
                } else {
                    $union .= "union select '" . $start . "' as month ";
                }
                $start = $start->addMonth(1);
            }
            $union = trim($union);
            session(['transport_unpaid.union' => $union]);
        }
        /*works while clicking page links*/ else {
            $union = session('transport_unpaid.union');
        }

        $query = DB::query()
            ->select(
                'b.admission_no',
                'b.full_name',
                'a.start_date as startdate',
                DB::raw("concat(f.name,' ',c.batch_name) as full_batch"),
                'd.id as class_id',
                'd.class_name',
                'm.month',
                'g.amount'
            )
            ->fromsub(function ($query) {
                $query->select(
                    "student_id",
                    "status",
                    "start_date",
                    "year_id",
                    "point_id",
                    DB::raw("date(start_date - day(start_date) +1) as strt_date")
                )
                    ->from("transport_student_point");
            }, "a")
            ->join("student as b", "a.student_id", "b.id")
            ->join("batch as c", "b.class_id", "c.id")
            ->join("course as d", "c.class_id", "d.id")
            ->leftjoinsub(function ($query) use ($union) {
                $query->selectraw($union);
            }, "m", "a.strt_date", "<=", "m.month")
            ->leftjoin("transport_feecollection as e", function ($join) {
                $join->on("a.student_id", "e.student_id")
                    ->on("m.month", "e.month");
            })
            ->join("academic_year as f", "c.year_id", "f.id")
            ->join("transport_fee_head as g", function ($join) {
                $join->on("a.year_id", "g.year_id")
                    ->on("a.point_id", "g.point_id");
            })
            ->where("a.status", 1)
            ->wherenull("e.month")
            ->wherenotnull("m.month");

        $query = DB::query()->select(
            'admission_no',
            'full_name',
            'startdate',
            'full_batch',
            'class_id',
            'class_name',
            DB::raw("sum(amount) as amount"),
            DB::raw("group_concat(monthname(month) order by month separator ', ') as month")
        )
            ->from($query, 'x')
            ->groupby('admission_no', 'full_name', 'startdate', 'full_batch', 'class_id', 'class_name');

        // dd($query->tosql());

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
