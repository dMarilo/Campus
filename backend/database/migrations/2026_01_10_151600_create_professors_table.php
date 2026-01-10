<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professors', function (Blueprint $table) {
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
            $table->string('code')->nullable()->index();
            $table->string('phone')->nullable();

            $table->string('academic_title')->nullable();
            $table->string('department')->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'external',
            ])->nullable();

            $table->string('status')->default('active');

            $table->string('office_location')->nullable();
            $table->string('office_hours')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professors');
    }
};
