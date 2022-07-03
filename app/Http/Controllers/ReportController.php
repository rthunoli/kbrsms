<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    private static function date_validate($redirect)
    {
        request()->session()->forget('invalid_date_range');
        if(request()->filled('start_date') and request()->filled('end_date')){
            $start_date = request('start_date');
            $end_date = request('end_date');
            $start = Carbon::createFromFormat('d/m/Y',$start_date);
            $end = Carbon::createFromFormat('d/m/Y',$end_date);
            if ($start > $end){
                return redirect($redirect)->with('invalid_date_range',"Invalid date range");      
            }        
        }
    }

    public function transport_unpaid()
    {
        self::date_validate(route("transport_unpaid"));
        return view('livewire.transport-unpaid');
    }

    public function transport_paid()
    {
        self::date_validate(route("transport_paid"));
        return view('livewire.transport-paid');
    }

    public function transport_users()
    {
        // self::date_validate(route("transport_users"));
        return view('livewire.transport-users');
    }


    public function fee_paid()
    {
        // dd('Fee Paid Report');
        self::date_validate(route("fee_paid"));
        return view('livewire.fee-paid');
    }

    public function fee_paid_headwise()
    {
        self::date_validate(route("fee_paid_headwise"));
        return view('livewire.fee-paid-headwise');
    }

   

}
