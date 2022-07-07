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

        /*discounts from fee_discount table*/
        /***********************************/
        $aca_fee1 = DB::query()
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
                    when 4 then 'Online' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
                DB::raw('sum(c.amount) as amount'),
                DB::raw('ifnull(sum(d.amount),0) as discount'),
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

        /*discounts from new_feereceipt table*/
        /*************************************/
        $aca_fee2 = DB::query()
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
                    when 4 then 'Online' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
                DB::raw('a.amount'),
                DB::raw('ifnull(a.discount_amount,0) as discount'),
                DB::raw("'Academic' as fee_type")
            )
            ->from('new_feereceipt as a')
            ->join('student as e', 'a.student_id', 'e.id')
            ->join('batch as f', 'a.batch_id', 'f.id')
            ->join('course as g', 'f.class_id', 'g.id')
            ->join('academic_year as h', 'f.year_id', 'h.id')
            ->where([['a.status', 1]])
            ->wheredate('a.receipt_date', '>=', $this->start_date)
            ->wheredate('a.receipt_date', '<=', $this->end_date);

        $aca_fee = DB::query()->select(
            'aca_fee2.receipt_id',
            'aca_fee2.receipt_date',
            'aca_fee2.admission_no',
            'aca_fee2.full_name',
            'aca_fee2.class_id',
            'aca_fee2.class_name',
            'aca_fee2.batch_id',
            'aca_fee2.batch_name',
            'aca_fee2.aca_year',
            'aca_fee2.method',
            'aca_fee2.amount',
            DB::raw('aca_fee1.discount+aca_fee2.discount as tot_discount'),
            'aca_fee2.fee_type'
        )
            ->fromsub($aca_fee1, 'aca_fee1')
            ->joinsub($aca_fee2, 'aca_fee2', function ($join) {
                $join->on([
                    ['aca_fee1.receipt_id', 'aca_fee2.receipt_id'],
                    ['aca_fee1.receipt_date', 'aca_fee2.receipt_date'],
                    ['aca_fee1.admission_no', 'aca_fee2.admission_no'],
                    ['aca_fee1.batch_id', 'aca_fee2.batch_id']
                ]);
            });

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
                    when 4 then 'Online' 
                    when 5 then 'Bank Through'
                    when 6 then 'Bulk Import'
                end as method"),
            DB::raw('sum(c.amount) as amount'),
            DB::raw('0 as tot_discount'),
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

        $transport_fee = DB::query()->select(
            'a.transaction_id as receipt_id',
            'a.date as receipt_date',
            'd.admission_no',
            'd.full_name',
            'f.id as class_id',
            'f.class_name',
            'e.id as batch_id',
            'e.batch_name',
            'g.name as aca_year',
            DB::raw("case a.method 
                when 1 then 'Cash' 
                when 2 then 'Cheque'
                when 3 then 'Online'
                when 4 then 'Card'
                when 5 then 'Net Banking'
                when 6 then 'Bank Through'
                when 7 then 'Swipe'
            end as method"),
            DB::raw('sum(c.amount) as amount'),
            DB::raw('sum(c.discount_amount) as tot_discount'),
            DB::raw("'Transport' as fee_type")
        )
            ->from('transport_receipt as a')
            ->join('transport_receipt_feecollection as b', 'a.id', 'b.receipt_id')
            ->join('transport_feecollection as c', 'c.id', 'b.collection_id')
            ->join('student as d', 'a.student_id', 'd.id')
            ->join('batch as e', 'd.class_id', 'e.id')
            ->join('course as f', 'e.class_id', 'f.id')
            ->join('academic_year as g', 'e.year_id', 'g.id')
            ->where([['a.status', 1], ['c.status', 1], ['c.transaction_status', 1]])
            ->wheredate('a.date', '>=', $this->start_date)
            ->wheredate('a.date', '<=', $this->end_date)
            ->groupby([
                'a.transaction_id', 'a.date', 'd.admission_no', 'd.full_name',
                'f.id', 'f.class_name', 'e.id', 'e.batch_name', 'g.name','a.method'
            ]);

        // $transport_fee->dd();

        $all_fee = DB::query()->from($aca_fee->unionall($adm_fee)->unionall($transport_fee), 'x')
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
                'tot_discount',
                'fee_type',
                DB::raw("concat(aca_year,' ',batch_name) as full_batch")
            );

        // $data = $transport_fee->get()->toarray();

        $data = $all_fee->get()->toarray();

        //Converting objects to array
        $i = 0;
        foreach ($data as &$obj) {
            $obj->id = $i++;
            $obj = (array)$obj;
        }

        // dd($data);
        return $data;
    }

}
