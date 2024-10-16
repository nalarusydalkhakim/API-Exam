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
        Schema::create('task_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')
                ->constrained('tasks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_random_order')->default(false);
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
        Schema::dropIfExists('task_sections');
    }
};
