<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('assistant')->create('assistant_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')
                  ->constrained('assistant_entries')
                  ->cascadeOnDelete();
            $table->text('answer_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('assistant')->dropIfExists('assistant_answers');
    }
};
