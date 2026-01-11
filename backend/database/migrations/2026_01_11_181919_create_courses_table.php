<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unsignedTinyInteger('ects');
            $table->string('department');

            $table->enum('level', ['bachelor', 'master']);
            $table->boolean('mandatory')->default(true);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->index(['department', 'level']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

