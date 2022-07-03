<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class LivewireController extends Controller
{
    public function test()
    {
        return view('livewire.livewire-test');
    }

    public function xsearch()
    {
        return view('livewire.xstudents');
    }

}
