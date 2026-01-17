<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            // Context: exam belongs to a specific class
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();

            // Exam definition
            $table->string('type');
            // midterm | final | retake | quiz | oral | lab_exam

            $table->string('title')->nullable();

            // Scheduling
            $table->date('exam_date');

            // Grading rules
            $table->unsignedInteger('max_points');

            // Lifecycle state
            $table->string('status')->default('planned');
            // planned | open | closed | graded | canceled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
