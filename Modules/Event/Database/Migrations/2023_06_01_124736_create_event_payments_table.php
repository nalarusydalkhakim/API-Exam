<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')
                ->constrained('events')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('code')->unique();
            $table->unsignedBigInteger('price')->default(0);
            $table->enum('status', [
                'Belum Dibayar',
                'Menunggu Pembayaran',
                'Gagal',
                'Sukses'
            ])->default('Menunggu Pembayaran');
            $table->string('token')->nullable();
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
        Schema::dropIfExists('event_payments');
    }
};
