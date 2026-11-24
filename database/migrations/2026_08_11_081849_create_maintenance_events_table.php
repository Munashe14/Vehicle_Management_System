<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
{
    Schema::create('maintenance_events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('maintenance_schedule_id')->constrained()->cascadeOnDelete();
        $table->text('work_details');
        $table->decimal('cost', 12, 2);
        $table->date('completed_at');
        $table->string('status')->default('pending');
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
        Schema::dropIfExists('maintenance_events');
    }
}
