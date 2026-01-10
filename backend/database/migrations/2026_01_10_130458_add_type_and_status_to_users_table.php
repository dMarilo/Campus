<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('type')
                ->after('password')
                ->index()
                ->comment('system, admin, staff, client');

            $table->string('status')
                ->after('type')
                ->default('active')
                ->index()
                ->comment('active, inactive, blocked');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);
        });
    }
};
