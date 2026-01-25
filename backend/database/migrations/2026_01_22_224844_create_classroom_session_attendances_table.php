<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_session_attendances', function (Blueprint $table) {
            $table->id();

            // Session this attendance belongs to
            $table->foreignId('classroom_session_id')
                ->constrained()
                ->cascadeOnDelete();

            // Student who checked in
            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            // When the student checked in
            $table->timestamp('checked_in_at');

            // Attendance status for THIS session
            $table->enum('status', ['present', 'late']);

            $table->timestamps();

            // Prevent duplicate check-ins for the same session
            $table->unique(
                ['classroom_session_id', 'student_id'],
                'session_student_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_session_attendances');
    }
};
