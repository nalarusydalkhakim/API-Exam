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
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('answer_type', [
                'Pilihan Ganda',
                'Esai',
                'Unggah Berkas'
            ]);
            $table->text('question')->nullable();
            $table->string('file')->nullable();
            $table->string('file_name')->nullable();
            $table->text('explanation')->nullable();
            $table->enum('level', [
                'C1-Pengetahuan',
                'C2-Pemahaman',
                'C3-Penerapan',
                'C4-Analisis',
                'C5-Sintesis',
                'C6-Evaluasi'
            ])->nullable();
            $table->enum('visibility', [
                'Hanya Saya',
                'Publik'
            ]);
            $table->foreignUuid('subject_id')
                ->nullable()
                ->constrained('subjects')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->unsignedTinyInteger('class')->nullable();
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
        Schema::dropIfExists('questions');
    }
};
