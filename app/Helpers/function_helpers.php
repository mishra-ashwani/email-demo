<?php

use App\Models\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

function getUserPlan($user_id){
    $plan = CustomerSubscription::where('product_id', env("PRODUCT_ID", "101"))->where('customer_id',$user_id)->first();
    return ($plan && $plan->licence_key == null) ? "Free" : "Licenced";
}
function cleanString($string) {
    $string = strtolower(preg_replace('/\s+/', '_', $string));
    return preg_replace('/[^A-Za-z0-9\-\_]/', '', $string); // Removes special chars.
 }
 function cleanFileName($string) {
    $string = strtolower(preg_replace('/\s+/', '-', $string));
    return preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $string); // Removes special chars.
 }
 function logError($message){
    Log::channel(env('LOG_CHANNEL', 'slack'))->info($message);
 }
 function getPrimaryUserId($userId){
    $primaryUser = DB::table('users')->where('id',$userId)->first();
    if($primaryUser){
        return is_null($primaryUser->parent_id) ? $userId : $primaryUser->parent_id;
    }
    return redirect()->route('logout');
 }
