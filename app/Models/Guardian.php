<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DBConnections;

class Guardian extends Model
{
    use HasFactory;
    use DBConnections;

    protected $table = 'guardian';

    public function __construct() {
        
        $this->setDBConnection();
    }

    public function child()
    {
       return $this->hasMany(Student::class,'parent_id')->where('status',1);

    }
}
