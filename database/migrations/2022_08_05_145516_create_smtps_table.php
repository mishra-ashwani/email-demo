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
        Schema::create('smtps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('account_name');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_email');
            $table->string('server');
            $table->integer('port');
            $table->enum('use_auth', ['1', '0']);
            $table->enum('use_ssl', ['1', '0']);
            $table->string('user_email');
            $table->string('user_password');
            $table->string('status');
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
        Schema::dropIfExists('smtps');
    }
};
