<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DBConnections;

class Student extends Model
{
    use HasFactory;
    use DBConnections;

    protected $table = 'student';

    public function __construct() {

        $this->setDBConnection();

    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class,'parent_id')->where('status',1);

    }

    public function batch()
    {
        return $this->belongsTo(Batch::class,'class_id')->where('status',1);

    }

    public function student_status()
    {
        return $this->belongsTo(StudentStatus::class,'status_id');

    }

}
