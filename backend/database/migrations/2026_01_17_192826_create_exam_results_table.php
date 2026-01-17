<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('exam_id')
                  ->constrained('exams')
                  ->cascadeOnDelete();

            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();

            // Academic outcome
            $table->unsignedTinyInteger('grade')->nullable();
            // e.g. 5–10 or 1–10 depending on grading system

            $table->boolean('passed')->default(false);

            // Administrative registration date
            $table->date('registration_date')->nullable();

            $table->timestamps();

            // One result per student per exam
            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
