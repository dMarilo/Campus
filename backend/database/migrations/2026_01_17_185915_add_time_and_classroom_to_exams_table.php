<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->time('exam_time')
                  ->after('exam_date');

            $table->string('classroom_name')
                  ->nullable()
                  ->after('exam_time');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_time', 'classroom_name']);
        });
    }
};
