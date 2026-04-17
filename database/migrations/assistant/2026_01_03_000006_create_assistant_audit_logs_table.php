<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('assistant')->create('assistant_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('question_id')->nullable();
            $table->unsignedBigInteger('entry_id')->nullable();
            $table->string('category_name');
            $table->json('payload')->nullable();
            $table->string('admin_email');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::connection('assistant')->dropIfExists('assistant_audit_logs');
    }
};
