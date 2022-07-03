<?php

namespace App\Models\Sushi;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use App\Traits\DBConnections;
use Illuminate\Support\Facades\DB;

class SearchStudentsTable extends Model
{

    use Sushi;
    use DBConnections;
    protected $table = 'SearchStudentsTable';

    public function getRows()
    {
        $this->setDBConnection();
        $query = DB::query()
            ->from('student as a')
            ->join('guardian as b', 'a.parent_id', 'b.id')
            ->join('app_db.student_status as c', 'a.status', 'c.id')
            ->join('batch as d', 'a.class_id', 'd.id')
            ->join('academic_year as e', 'd.year_id', 'e.id')
            ->join('course as f', 'd.class_id', 'f.id')
            ->select(
                'a.id',
                'a.admission_no',
                'a.full_name',
                'a.gender',
                'a.dob',
                'a.phone_no',
                'a.phone_home',
                'a.admission_date',
                'a.admitted_class',
                'a.second_language',
                'b.father_name',
                'b.father_occupation',
                'b.phone_father',
                'b.father_residential_address',
                'b.mother_name',
                'b.mother_occupation',
                'b.phone_mother',
                'b.mother_residential_address',
                'b.guardian_name',
                'b.phone_guardian',
                'b.guardian_address',
                'c.status',
                'f.id as class_id',
                'f.class_name',
                DB::raw("concat(e.name,' ',d.batch_name) as full_batch"),
                DB::raw("case(a.medium) when 1 then 'Eng' when 2 then 'Mal' end as medium"),
                DB::raw("case(a.classroom_type) when 1 then 'Digital' when 2 then 'Normal' end as digital")
            );

        // dd($query->tosql());

        // return $query->tosql();

        $data = $query->get()->toarray();

        //Converting objects to array
        foreach ($data as &$obj) {
            $obj = (array)$obj;
        }

        // dd($data);
        return $data;
    }
}
