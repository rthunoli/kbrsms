<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\DBConnections;

class Batch extends Model
{
    use HasFactory;
    use DBConnections;
    protected $table = 'batch';

    public function __construct() {
        
        $this->setDBConnection();

    }
    
}
