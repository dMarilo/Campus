<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('professor_id')
                  ->constrained('professors')
                  ->cascadeOnDelete();

            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();

            // Role of the professor in this class
            $table->string('role')->default('lecturer');
            // lecturer | assistant | lab_instructor | guest

            // Lifecycle state
            $table->string('status')->default('assigned');
            // assigned | active | completed | replaced

            // How many sessions this professor has taught
            $table->unsignedInteger('taught_sessions')->default(0);

            $table->timestamps();

            // A professor can appear only once per class
            $table->unique(['professor_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching');
    }
};
