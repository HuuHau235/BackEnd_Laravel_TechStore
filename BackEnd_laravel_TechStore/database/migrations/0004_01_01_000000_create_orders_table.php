<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('order_date');
            $table->string('status')->comment('Order status: pending, processing, completed, cancelled');
            $table->decimal('total_amount', 10, 2);

            // Thông tin người nhận hàng (không phụ thuộc vào tài khoản người dùng)
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_email')->nullable();
            $table->string('shipping_address');
            $table->string('note')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
