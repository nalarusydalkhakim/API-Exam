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
        Schema::create('task_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_task_id')
                ->constrained('event_tasks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedFloat('score')->nullable();
            $table->enum('status', [
                'Belum Dikerjakan',
                'Sedang Dikerjakan',
                'Belum Dikoreksi',
                'Sedang Dikoreksi',
                'Selesai'
            ]);
            $table->boolean('is_passed')->nullable();
            $table->datetime('finish_at')->nullable();
            $table->text('feedback')->nullable();
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
        Schema::dropIfExists('task_results');
    }
};
