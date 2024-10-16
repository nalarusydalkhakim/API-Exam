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
        Schema::create('task_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('question_id')
                ->constrained('questions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignUuid('task_section_id')
                ->constrained('task_sections')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedInteger('number')->nullable();
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
        Schema::dropIfExists('task_questions');
    }
};
