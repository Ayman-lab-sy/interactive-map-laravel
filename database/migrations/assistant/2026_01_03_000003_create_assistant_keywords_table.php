<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('assistant')->create('assistant_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')
                  ->constrained('assistant_entries')
                  ->cascadeOnDelete();
            $table->string('keyword', 255)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('assistant')->dropIfExists('assistant_keywords');
    }
};
