<?php

use App\Models\CustomerSubscription;

function getUserPlan($user){
    $plan = CustomerSubscription::where('product_id', env("PRODUCT_ID", "101"))->where('customer_id',$user->id)->first();
    return ($plan && $plan->licence_key == null) ? "Free" : "Licenced";
}
