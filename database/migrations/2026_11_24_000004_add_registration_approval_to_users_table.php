<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationApprovalToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('requested_role_id')
                ->nullable()
                ->after('role_id')
                ->constrained('roles')
                ->nullOnDelete();
            $table->string('registration_status')->default('approved')->after('requested_role_id');
            $table->index(['registration_status', 'is_active']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['requested_role_id']);
            $table->dropIndex(['registration_status', 'is_active']);
            $table->dropColumn(['requested_role_id', 'registration_status']);
        });
    }
}
