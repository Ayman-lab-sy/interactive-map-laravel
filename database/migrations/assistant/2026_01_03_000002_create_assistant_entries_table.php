<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('assistant')->create('assistant_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->constrained('assistant_categories')
                  ->cascadeOnDelete();

            $table->string('tone')->nullable();
            $table->json('tags')->nullable();   // ← هذا السطر كان ناقص
            $table->string('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('assistant')->dropIfExists('assistant_entries');
    }
};

