<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_coupons');
    }
};
