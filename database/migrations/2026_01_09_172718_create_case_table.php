<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();

            $table->string('case_number', 30)->unique();
            $table->string('followup_token', 20);

            // ?????? ?????
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->string('sect', 100)->nullable();
            $table->string('location')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            // ??????
            $table->string('spouse_name')->nullable();
            $table->json('children')->nullable();

            // ?????? ????????
            $table->enum('direct_threat', ['???', '??'])->nullable();
            $table->text('threat_description')->nullable();
            $table->string('threat_source')->nullable();
            $table->date('threat_date')->nullable();
            $table->string('threat_locations')->nullable();

            // ????? ??????
            $table->enum('psychological_impact', ['???', '??'])->nullable();
            $table->text('impact_details')->nullable();

            // ?????????
            $table->boolean('agreed_to_document')->default(false);
            $table->boolean('agreed_to_share')->default(false);
            $table->boolean('agreed_to_campaign')->default(false);

            // ???? ????????
            $table->enum('status', ['new', 'under_review', 'archived'])->default('new');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
