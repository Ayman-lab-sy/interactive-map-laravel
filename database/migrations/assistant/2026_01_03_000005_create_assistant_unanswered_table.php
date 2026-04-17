<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('assistant')->create('assistant_unanswered', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->text('question_text');
            $table->text('expanded_text')->nullable();
            $table->enum('status', ['new','approved','ignored','converted'])->default('new');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('assistant')->dropIfExists('assistant_unanswered');
    }
};
