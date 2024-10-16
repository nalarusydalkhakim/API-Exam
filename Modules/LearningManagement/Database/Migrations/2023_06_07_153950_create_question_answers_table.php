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
        Schema::create('question_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_question_id')
                ->constrained('task_questions')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('is_marked')->default(false);
            $table->text('text')->nullable();
            $table->foreignUuid('question_option_id')
                ->nullable()
                ->constrained('question_options')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('file')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_correct')->nullable();
            $table->float('score')->nullable();
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
        Schema::dropIfExists('question_answers');
    }
};
