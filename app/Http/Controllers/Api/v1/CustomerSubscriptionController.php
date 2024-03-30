<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Mail\NewCustomerEmail;
use App\Models\CustomerSubscription;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerSubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'=>['required', 'string','max:128'],
            'customer_email'=>['required','email'],
            'product_id'=>['required'],
            'licence_count'=>['required']
        ]);
        try {
            // Check user exist
            $user = User::where('email', '=', $request->customer_email)->first();
            if(!$user){
                // Create user
                $password= Hash::make($request->customer_email);
                $user = User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'password' => $password,
                ]);
            }
            // Create Subscription

            $payment_id =  $request->payment_id ?? NULL;
            if($payment_id){
                $subscription_end_date=Carbon::now()->addYear($request->subscription_validity);
            }else{
                $subscription_end_date = Carbon::now()->addDays(15);
            }
            CustomerSubscription::create([
                'customer_id' => $user->id,
                'product_id' => $request->product_id,
                'licence_count' => $request->licence_count,
                'payment_id' => $payment_id,
                'subscription_start_date' => Carbon::now(),
                'subscription_end_date' =>$subscription_end_date
            ]);

            $emailBody='Hi, use following credentials to logging in portal.'."<br><br>";
            $emailBody.='User Name: '.$request->customer_email."<br><br>";
            $emailBody.='Password: '.$request->customer_email."<br><br>";
            $emailBody.='Link: <a href="https://mailer.lantechsoft.com/" target="_blank">Follow thr link</a>';
            $mailData = [
                'title' => 'Mail from Lantechsoft',
                'body' => $emailBody
            ];

            Mail::to($request->customer_email)->send(new NewCustomerEmail($mailData));

            return response()->json([
                'status' => 'success',
            ]);
        }catch(Exception $e){
            logError("Root File - CustomerSubscriptionController : ".$e->getMessage().'--'.$e->getFile().'--'.$e->getLine());
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage().' '.$e->getFile().' '.$e->getLine(),
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerSubscription  $customerSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerSubscription $customerSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerSubscription  $customerSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerSubscription $customerSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerSubscription  $customerSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerSubscription $customerSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerSubscription  $customerSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerSubscription $customerSubscription)
    {
        //
    }
}
