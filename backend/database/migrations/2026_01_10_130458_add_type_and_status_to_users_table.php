<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->enum('type', ['admin', 'professor', 'student'])
                ->after('password')
                ->index()
                ->comment('admin, professor, student');

            $table->enum('status', ['pending', 'active', 'inactive', 'blocked'])
                ->after('type')
                ->default('pending')  // ✅ Changed from 'active' to 'pending'
                ->index()
                ->comment('pending, active, inactive, blocked');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);
        });
    }
};
