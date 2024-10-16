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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('announcement')->nullable();
            $table->string('photo');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('quota');
            $table->enum('status', ['Belum Mulai', 'Pendaftaran', 'Sedang Berlangsung', 'Selesai'])->default('Belum Mulai');
            $table->boolean('is_visible')->default(false);
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedTinyInteger('discount')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('events');
    }
};
