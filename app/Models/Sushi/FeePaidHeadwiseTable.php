<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sushi\Sushi;
use App\Traits\DBConnections;
use App\Traits\DateRange;

class FeePaidHeadwiseTable extends Model
{
    use Sushi;
    use DBConnections;
    use DateRange;
    protected $table = 'FeePaidHeadwiseTable';
    public function getRows()
    {
        $this->setDBConnection();
        $this->setDateRange('fee_paid_headwise');

        $aca_fee = DB::query()
            ->select(
                'a.receipt_date',
                'e.name as feehead',
                'c.feehead_id',
                // 'a.method as method_id',
                DB::raw("case a.method 
                    when 1 then 'Cash' 
                    when 2 then 'Cheque'
                    when 3 then 'Swipe'
                    when 4 then 'Online Payment' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
                DB::raw('sum(c.amount) as amount'),
                DB::raw('sum(d.amount) as discount')
                // DB::raw('max(c.id) as row_id')
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
            ->join('new_feehead as e', 'c.feehead_id', 'e.id')
            ->where([['a.status', 1], ['c.status', 1]])
            ->wheredate('a.receipt_date', '>=', $this->start_date)
            ->wheredate('a.receipt_date', '<=', $this->end_date)
            ->groupby(['a.receipt_date', 'e.name', 'c.feehead_id', 'a.method']);

        $adm_fee = DB::query()
            ->select(
                'a.receipt_date',
                'e.name as feehead',
                DB::raw('c.feehead_id+100 as feehead_id'),
                // 'a.method as method_id',
                DB::raw("case a.method 
                    when 1 then 'Cash' 
                    when 2 then 'Cheque'
                    when 3 then 'Swipe'
                    when 4 then 'Online Payment' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
                DB::raw('sum(c.amount) as amount'),
                DB::raw('null as discount')
                // DB::raw('max(c.id) as row_id')
            )
            ->from('admissionfee as a')
            ->join('adm_receipt_collection as b', 'a.id', 'b.admreceipt_id')
            ->join('adm_feecollection as c', 'c.id', 'b.admcollection_id')
            // ->join('adm_feehead as e', 'c.feehead_id', 'e.id')
            ->join('adm_feehead as e', function($join){
                //Adding 100 is necessary because 7,8 ids in new_feehead and adm_feehead will be ambiguous while making union  
                $join->on(DB::raw('c.feehead_id+100'),DB::raw('e.id+100'));
            })
            ->where([['a.status', 4], ['c.status', 1]])
            ->wheredate('a.receipt_date', '>=', $this->start_date)
            ->wheredate('a.receipt_date', '<=', $this->end_date)
            ->groupby(['a.receipt_date', 'e.name', 'c.feehead_id', 'a.method']);

        // $aca_fee->dd();
        // return $aca_fee;

        // $adm_fee->dd();
        // return $adm_fee;

        $data = DB::query()->from($aca_fee->unionall($adm_fee), 'x')
            // ->select('receipt_date', 'feehead', 'method', 'amount', 'discount')
            ->get()->toarray();

        // $query->select('*')->from($query,'x');
        // $query->dd();

        //Converting objects to array
        $i=1;
        foreach ($data as &$obj) {
            $obj->id = $i++;
            $obj = (array)$obj;
        }

        return $data;
    }
}
