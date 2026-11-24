<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('capacity')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('capacity')->nullable();
        });
    }
};