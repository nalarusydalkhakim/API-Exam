<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Laravolt\Indonesia\Seeds\VillagesSeeder;

class CreateVillagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravolt.indonesia.table_prefix').'villages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('code', 10)->unique();
            $table->char('district_code', 7);
            $table->string('name', 255);
            $table->text('meta')->nullable();
            $table->timestamps();

            $table->foreign('district_code')
                ->references('code')
                ->on(config('laravolt.indonesia.table_prefix').'districts')
                ->onUpdate('cascade')->onDelete('restrict');
        });
        Artisan::call('db:seed', [
            '--class' => VillagesSeeder::class,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('laravolt.indonesia.table_prefix').'villages');
    }
}
