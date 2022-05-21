<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reg_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('spot_id');
            $table->integer('vaccine_id');
            $table->date('vaccin_date');
            $table->enum('status',['Vaccinated','Waiting']);
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
        Schema::dropIfExists('reg_vaccinations');
    }
};
