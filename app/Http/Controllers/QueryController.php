<?php

namespace App\Http\Controllers;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryController extends Controller
{
    public function index()
    {
        
        /*
            select admission_no,full_name,batch_name,start_date, monthname(month) as unpaid
            from
            (
            select b.admission_no,b.full_name,c.batch_name,a.start_date,m.month
            from transport_student_point a
            inner join student b
            on a.student_id=b.id
            inner join batch c
            on b.class_id=c.id
            inner join course d
            on c.class_id=d.id and d.class_name='X'
            left join 
            (
            select '2021-12-01' as month
            union
            select '2022-01-01' as month
            union
            select '2022-02-01' as month
            union
            select '2022-03-01' as month
            ) as m
            on a.start_date<=m.month
            left join transport_feecollection e
            on a.student_id=e.student_id and m.month=e.month
            where a.status=1 and e.month is null 
            ) as x
            order by batch_name,admission_no,month
        */

        /*

            select `admission_no`, `full_name`, `batch_name`, `start_date`, monthname(month) as unpaid from (select `b`.`admission_no`, `b`.`full_name`, `c`.`batch_name`, `a`.`start_date`, `m`.`month` from `transport_student_point` as `a` inner join `student` as `b` on `a`.`student_id` = `b`.`id` inner join `batch` as `c` on `b`.`class_id` = `c`.`id` inner join `course` as `d` on `c`.`class_id` = `d`.`id` and `d`.`class_name` = ? inner join ((select '2021-12-01' as month) union (select '2022-01-01' as month) union (select '2022-02-01' as month) union (select '2022-03-01' as month)) as `m` on `a`.`start_date` <= `m`.`month` left join `transport_feecollection` as `e` on `a`.`student_id` = `e`.`student_id` and `m`.`month` = `e`.`month` and `a`.`status` = ? and `e`.`month` is null) as `x`
        */


        DB::setDefaultConnection('mysql_ems');

        $month=DB::query()
            ->selectraw("'2021-11-01' as month")
            ->union(function($query){
                $query->selectraw("'2021-12-01' as month");})
            ->union(function($query){
                $query->selectraw("'2022-01-01' as month");})
            ->union(function($query){
                $query->selectraw("'2022-02-01' as month");})
            ->union(function($query){
                $query->selectraw("'2022-03-01' as month");})
            ->union(function($query){
                $query->selectraw("'2022-04-01' as month");});

        $x = DB::query()
                    ->select("b.admission_no","b.full_name","c.batch_name","a.start_date","m.month")
                    ->from("transport_student_point as a")
                    ->join("student as b","a.student_id","b.id")
                    ->join("batch as c","b.class_id","c.id")
                    ->join("course as d","c.class_id","d.id")
                    ->joinsub($month,"m",function($join){
                        $join->on("a.start_date","<=","m.month");
                    })
                    ->leftjoin("transport_feecollection as e",function($join){
                        $join->on("a.student_id","e.student_id")
                            ->on("m.month","e.month")
                            ->where("a.status",1)
                            ->where("e.month",null);
                    });

        $result = DB::query()
                    ->select("admission_no","full_name","batch_name","start_date",DB::raw("monthname(month) as unpaid"))
                    ->fromsub($x,"x");
        
        // $result->dd();
        // return $result->tosql();
        return $result->get();

        // return Student::select('a.id','a.full_name','a.status','b.father_name')
        //     ->from('student as a')
        //     ->join('guardian as b','a.parent_id','b.id')
        //     ->wherein('a.id',function($query){
        //         $query->select('id')->from('student')->where('full_name','like','x%');
        //     })->get();

            
    }
}