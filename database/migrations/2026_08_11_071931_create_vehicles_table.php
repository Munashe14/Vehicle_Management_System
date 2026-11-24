<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->string('vehicle_number')->unique();
        $table->string('make')->nullable();
        $table->string('model')->nullable();
        $table->integer('year')->nullable();
        $table->string('vehicle_type')->nullable();
        $table->integer('capacity')->nullable();
        $table->integer('odometer_start')->nullable();
        $table->string('status')->default('active');
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
        Schema::dropIfExists('vehicles');
    }
    
}
