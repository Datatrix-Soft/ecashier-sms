<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_billings', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id');
            $table->integer('user_id');
            $table->string('bill_number')->unique()->nullable();
            $table->double('payable')->default(0);
            $table->double('paid')->default(0);
            $table->string('payment_type')->nullable();
            $table->string('transaction_number')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_billings');
    }
}
