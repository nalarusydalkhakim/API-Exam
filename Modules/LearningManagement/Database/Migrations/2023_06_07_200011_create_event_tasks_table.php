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
        Schema::create('event_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')
                ->constrained('events')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('task_id')
                ->constrained('tasks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('point_correct')->default(0);
            $table->integer('point_incorrect')->default(0);
            $table->integer('point_empty')->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
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
        Schema::dropIfExists('event_tasks');
    }
};
