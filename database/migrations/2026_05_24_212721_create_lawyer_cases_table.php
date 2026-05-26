<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lawyer_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('case_number');
            $table->string('jurisdiction');
            $table->string('type', 30); // commercial, corporate, employment, civil
            $table->string('type_label');
            $table->string('status', 20); // won, lost, active, settled
            $table->integer('year');
            $table->string('court');
            $table->string('won_party')->nullable();
            $table->integer('motions_count')->default(0);
            $table->string('motion_success_rate', 10)->default('0%');
            $table->json('timeline')->nullable();
            $table->json('motions')->nullable();
            $table->json('parties')->nullable();
            $table->json('issues')->nullable();
            $table->json('documents')->nullable();
            $table->text('summary')->nullable();
            $table->text('key_finding')->nullable();
            $table->json('rate_boxes')->nullable();
            $table->json('next_steps')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_cases');
    }
};
