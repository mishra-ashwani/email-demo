<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Smtp;
use DB;
use Illuminate\Support\Facades\Auth;

class IsPrimaryUser
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
        $subcription = DB::table('users')
             ->where('id', '=', Auth::User()->id)
             ->whereNull('parent_id')
             ->first();
        if($subcription){
            return $next($request);
        }
        return redirect()->route('dashboard');
    }
}
