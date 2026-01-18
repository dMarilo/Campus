<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();

            /**
             * Location
             */
            $table->foreignId('building_id')
                  ->constrained('buildings')
                  ->cascadeOnDelete();

            /**
             * Identification
             */
            $table->string('name');              // e.g. A-101, Lab 3
            $table->unsignedInteger('capacity'); // max number of students

            /**
             * Type of classroom
             */
            $table->enum('type', [
                'classroom',
                'computer_room',
                'lecture_hall',
                'lab',
            ]);

            /**
             * LIVE operational status
             */
            $table->enum('status', [
                'empty',
                'reserved',
                'occupied',
            ])->default('empty');

            /**
             * Currently active session (ONLY when occupied)
             */
            $table->foreignId('active_session_id')
                  ->nullable();

            $table->timestamps();

            /**
             * Prevent duplicate classroom names within the same building
             */
            $table->unique(['building_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
