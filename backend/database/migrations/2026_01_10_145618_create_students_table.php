<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Relation
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Identity (required on creation)
            $table->string('email')->index();
            $table->string('first_name');
            $table->string('last_name');

            // Optional profile data
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('student_index')->nullable()->index();
            $table->string('code')->nullable()->index();

            $table->unsignedInteger('year_of_study')->default(1);
            $table->string('department')->nullable();
            $table->decimal('gpa', 4, 2)->nullable();

            // Status
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
