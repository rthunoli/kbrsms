<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait DBConnections
{
    public function setDBConnection()
    {
        if (session()->has('db') && session('db') == 'EMS') {
            $this->connection = 'mysql_ems';
            DB::setDefaultConnection('mysql_ems');
        } elseif (session()->has('db') && session('db') == 'HSS') {
            $this->connection = 'mysql_hss';
            DB::setDefaultConnection('mysql_hss');
        }
    }
}
