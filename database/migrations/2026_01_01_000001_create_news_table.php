<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    // نربط المايغريشن بقاعدة الأخبار فقط
    protected $connection = 'news';

    public function up(): void
    {
        Schema::connection($this->connection)->create('news', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('title_en')->nullable();

            $table->date('date');

            $table->text('summary');
            $table->text('summary_en')->nullable();

            $table->longText('content');
            $table->longText('content_en')->nullable();

            $table->string('image')->nullable();
            $table->string('slug')->unique();

            $table->boolean('published')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('news');
    }
};
