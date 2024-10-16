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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
