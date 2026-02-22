<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classroom_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('professor_id')->nullable()->after('course_class_id');
            $table->foreign('professor_id')->references('id')->on('professors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classroom_sessions', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropColumn('professor_id');
        });
    }
};
