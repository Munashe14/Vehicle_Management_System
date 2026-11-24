<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('fuels', function (Blueprint $table) {
        $table->id();
        $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
        $table->string('fuel_type');
        $table->decimal('consumed_liters', 10, 2);
        $table->decimal('unit_cost', 10, 2);
        $table->decimal('total_cost', 12, 2);
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
        Schema::dropIfExists('fuels');
    }
}
