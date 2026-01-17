<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            // Academic context
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete();

            $table->foreignId('semester_id')
                  ->constrained('semesters')
                  ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            // How many times this course has been held
            $table->unsignedInteger('iteration')->default(1);

            // Location (optional)
            // $table->foreignId('classroom_id')
            //       ->nullable()
            //       ->constrained('units')
            //       ->nullOnDelete();

            // Lifecycle state
            $table->string('status')->default('planned');
            // planned | active | finished | canceled

            $table->timestamps();

            // A course can exist only once per semester
            $table->unique(['course_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
