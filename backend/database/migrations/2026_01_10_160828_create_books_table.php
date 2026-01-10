<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Core book data
            $table->string('title');
            $table->string('author');

            // Optional metadata
            $table->string('isbn')->nullable()->index();
            $table->string('publisher')->nullable();
            $table->integer('published_year')->nullable();
            $table->string('edition')->nullable();
            $table->text('description')->nullable();

            // Inventory
            $table->unsignedInteger('total_copies')->default(1);
            $table->unsignedInteger('available_copies')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
