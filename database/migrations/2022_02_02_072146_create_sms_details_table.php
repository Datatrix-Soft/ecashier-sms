<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_details', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id')->nullable();
            $table->integer('sender_id');
            $table->string('sender_phone');
            $table->integer('receiver_id');
            $table->string('receiver_phone');
            $table->string('receiver_type')->nullable();
            $table->string('sms_type')->nullable();
            $table->string('content');
            $table->integer('sms_count')->nullable();
            $table->boolean('is_sent')->default(false);
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
        Schema::dropIfExists('sms_details');
    }
}
