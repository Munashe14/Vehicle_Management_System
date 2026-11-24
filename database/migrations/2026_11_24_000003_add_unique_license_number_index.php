<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUniqueLicenseNumberIndex extends Migration
{
    public function up()
    {
        $duplicates = DB::table('users')
            ->select('license_number')
            ->whereNotNull('license_number')
            ->groupBy('license_number')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicates) {
            throw new RuntimeException(
                'Duplicate driver license numbers exist. Resolve them before applying the unique license-number index.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('license_number');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['license_number']);
        });
    }
}
