<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSchedule extends Model
{
    use HasFactory;
    protected $table = 'email_schedules';

    protected $fillable = ['user_id','schedule_date','schedule_time','recipient_list','email_subject','template_body','smtp_group_id','smtp_ids','status','batch_number'];

    public function totalEmail(){
        return $this->hasMany(EmailLog::class, 'batch_number', 'batch_number');
    }
    public function sentEmail(){
        return $this->hasMany(EmailLog::class, 'batch_number', 'batch_number')->where('status','sent');
    }
    public function failedEmail(){
        return $this->hasMany(EmailLog::class, 'batch_number', 'batch_number')->where('status','fail');
    }
}
