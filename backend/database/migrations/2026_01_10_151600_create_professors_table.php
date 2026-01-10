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

            // Relation to users
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Identity
            $table->string('code')->index(); // renamed from isbn
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();

            // Contact
            $table->string('phone')->nullable();

            // Academic info
            $table->string('academic_title');
            $table->string('department');

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'external',
            ]);

            // Status
            $table->string('status')->default('active');

            // Office
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
