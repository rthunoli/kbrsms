<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDbIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(! session()->has('db')){
            // return redirect('/')->with('dbnotok', 'Please select a database (EMS/HSS)');
            return redirect()->route('home')->with('error', 'Please select a database (EMS/HSS)');
        }
        return $next($request);
    }
}
