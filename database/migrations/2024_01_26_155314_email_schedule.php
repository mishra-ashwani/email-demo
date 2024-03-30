<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->dateTime("schedule_date_time");
            $table->string("schedule_time",6);
            $table->integer("recipient_list")->length(7);
            $table->string('email_subject',255);
            $table->text('template_body');
            $table->integer("smtp_group_id")->length(7);
            $table->string('smtp_ids',255);
            $table->string('batch_number',55);
            $table->enum('status',['pending','completed','failed','partial']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_schedules');
    }
};
