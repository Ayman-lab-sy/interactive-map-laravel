<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->cascadeOnDelete();

            $table->foreignId('update_id')
                ->nullable()
                ->constrained('case_updates')
                ->nullOnDelete();

            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_files');
    }
};
