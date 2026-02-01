<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_sessions', function (Blueprint $table) {
            $table->id();

            /**
             * Relations
             */
            $table->foreignId('classroom_id')
                  ->constrained('classrooms')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('course_class_id');

            /**
             * Time boundaries
             */
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();

            /**
             * Session lifecycle
             */
            $table->enum('status', [
                'scheduled',
                'ongoing',
                'finished',
                'cancelled',
            ])->default('scheduled');

            $table->timestamps();

            /**
             * Performance & conflict prevention
             */
            $table->index(['classroom_id', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_sessions');
    }
};
