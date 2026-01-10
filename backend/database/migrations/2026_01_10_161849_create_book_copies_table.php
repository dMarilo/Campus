<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            // Physical identifier of the copy
            $table->string('isbn')->index();

            // Condition of the copy itself
            $table->enum('status', [
                'available',
                'borrowed',
                'damaged',
                'lost',
            ])->default('available');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
