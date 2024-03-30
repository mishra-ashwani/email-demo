<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Smtp;
use DB;
use Illuminate\Support\Facades\Auth;

class TrailOrLive
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
        $subcription = DB::table('subscriptions')
             ->where('user_id', '=', getPrimaryUserId(Auth::user()->id))
             ->first();

        if($subcription->type == 'free'){
            $smtps = Smtp::where('user_id', getPrimaryUserId(Auth::user()->id))->get();
            if($smtps->count() >= 2){
                session()->flash('message', 'Free Account Limit Reached.');
                session()->flash('classes', 'alert-danger');
                return redirect()->route('add-new-smtp');
            }
        }
        return $next($request);
    }
}
