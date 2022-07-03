<?php

namespace App\Traits;
use Illuminate\Support\Carbon;

trait DateRange
{
    public $start_date;
    public $end_date;
    public $start_date_dmy;
    public $end_date_dmy;
    

    public function setDateRange(string $subSessionName = '')
    {
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $start_date = request('start_date');
            $end_date = request('end_date');
            $this->start_date = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
            $this->end_date = Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');
            session()->put($subSessionName, ['start_date' => $this->start_date, 'end_date' => $this->end_date]);
        }
        /*works while clicking page links*/ 
        else {
            $this->start_date = session($subSessionName . '.start_date');
            $this->end_date = session($subSessionName . '.end_date');
        }

        $this->start_date_dmy=Carbon::createFromDate($this->start_date)->format('d-m-Y');
        $this->end_date_dmy=Carbon::createFromDate($this->end_date)->format('d-m-Y');
    }

    public function isSetDateRange() : bool
    {
        return (request()->filled('start_date') && request()->filled('end_date'));
    }

}
