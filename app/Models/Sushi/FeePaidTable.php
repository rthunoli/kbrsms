<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
use App\Traits\DateRange;

class FeePaidTable extends Model
{
    use Sushi;
    use DBConnections;
    use DateRange;
    protected $table = 'FeePaidTable';

    public function getRows()
    {
        $this->setDBConnection();
        $this->setDateRange('fee_paid');

        $aca_fee = DB::query()
            ->select(
                'a.receipt_id',
                'a.receipt_date',
                'e.admission_no',
                'e.full_name',
                'g.id as class_id',
                'g.class_name',
                'f.id as batch_id',
                'f.batch_name',
                'h.name as aca_year',
                DB::raw("case a.method 
                    when 1 then 'Cash' 
                    when 2 then 'Cheque'
                    when 3 then 'Swipe'
                    when 4 then 'Online Payment' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
                DB::raw('sum(c.amount) as amount'),
                DB::raw('sum(d.amount) as discount'),
                DB::raw("'Academic' as fee_type")
            )
            ->from('new_feereceipt as a')
            ->join('new_feereceipt_feecollection as b', 'a.id', 'b.feereceipt_id')
            ->join('new_feecollection as c', 'c.id', 'b.feecollection_id')
            ->leftjoin('fee_discount as d', function ($join) {
                $join->on([
                    ['c.student_id', 'd.student_id'],
                    ['c.batch_id', 'd.batch_id'],
                    ['c.feegroup_id', 'd.feegroup_id'],
                    ['c.feehead_id', 'd.feehead_id'],
                    ['c.feetype_id', 'd.feetype_id'],
                    ['c.feeinstallment_id', 'd.feeinstallment_id'],
                    ['c.feeheadgroup_id', 'd.feeheadgroup_id'],
                    ['c.status', 'd.status']
                ]);
            })
            ->join('student as e', 'a.student_id', 'e.id')
            ->join('batch as f', 'a.batch_id', 'f.id')
            ->join('course as g', 'f.class_id', 'g.id')
            ->join('academic_year as h', 'f.year_id', 'h.id')
            ->where([['a.status', 1], ['c.status', 1]])
            ->wheredate('a.receipt_date', '>=', $this->start_date)
            ->wheredate('a.receipt_date', '<=', $this->end_date)
            ->groupby([
                'a.receipt_id', 'a.receipt_date', 'e.admission_no', 'e.full_name', 'g.id',
                'g.class_name', 'f.id', 'f.batch_name', 'h.id', 'h.name', 'a.method'
            ]);

        $adm_fee = DB::query()->select(
                'a.receipt_id',
                'a.receipt_date',
                'e.admission_no',
                'e.full_name',
                'g.id as class_id',
                'g.class_name',
                'f.id as batch_id',
                'f.batch_name',
                'h.name as aca_year',
                DB::raw("case a.method 
                        when 1 then 'Cash' 
                        when 2 then 'Cheque'
                        when 3 then 'Swipe'
                        when 4 then 'Online Payment' 
                        when 5 then 'Bank Through'
                        when 6 then 'Bulk Import'
                    end as method"),
                DB::raw('sum(c.amount) as amount'),
                DB::raw('null as discount'),
                DB::raw("'Admission' as fee_type")
            )
            ->from('admissionfee as a')
            ->join('adm_receipt_collection as b', 'a.id', 'b.admreceipt_id')
            ->join('adm_feecollection as c', 'c.id', 'b.admcollection_id')
            ->join('student as e', 'a.student_id', 'e.id')
            ->join('batch as f', 'a.batch_id', 'f.id')
            ->join('course as g', 'f.class_id', 'g.id')
            ->join('academic_year as h', 'f.year_id', 'h.id')
            ->where([['a.status', 4], ['c.status', 1]])
            ->wheredate('a.receipt_date', '>=', $this->start_date)
            ->wheredate('a.receipt_date', '<=', $this->end_date)
            ->groupby([
                'a.receipt_id', 'a.receipt_date', 'e.admission_no', 'e.full_name', 'g.id',
                'g.class_name', 'f.id', 'f.batch_name', 'h.id', 'h.name', 'a.method'
            ]);

        // $aca_fee->dd();
        // return $aca_fee;

        // $adm_fee->dd();
        // return $adm_fee;

        // $query = DB::query()->from($aca_fee->unionall($adm_fee),'x');
        $data = DB::query()->from($aca_fee->unionall($adm_fee), 'x')
            ->select(
                'receipt_id',
                'receipt_date',
                'admission_no',
                'full_name',
                'class_id',
                'class_name',
                'batch_id',
                'batch_name',
                'aca_year',
                'method',
                'amount',
                'discount',
                'fee_type',
                DB::raw("concat(aca_year,' ',batch_name) as full_batch")
            )
            ->get()->toarray();

        //Converting objects to array
        foreach ($data as &$obj) {
            $obj = (array)$obj;
        }

        // dd($data);
        return $data;
    }
}
