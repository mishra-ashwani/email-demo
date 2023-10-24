<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipient;
use App\Models\Smtp as Sm;

class DashboardController extends Controller
{
    public function index()
    {
        $smtp_count = Sm::where('user_id', '=', Auth::user()->id)->get()->count();
        $recipients_count = Recipient::where('user_id', Auth::User()->id)->get()->count();

        $email_sent = DB::table('email_logs')->where('user_id', Auth::User()->id)->get()->count();

        return view('user.dashboard', compact('smtp_count', 'recipients_count', 'email_sent'));
    }
}
