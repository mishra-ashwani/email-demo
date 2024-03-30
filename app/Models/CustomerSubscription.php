<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'product_id',
        'licence_count',
        'payment_id',
        'subscription_start_date',
        'subscription_end_date'
    ];
}
