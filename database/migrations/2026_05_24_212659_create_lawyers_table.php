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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('firm');
            $table->string('city');
            $table->string('state', 10);
            $table->string('specialty');
            $table->text('bio');
            $table->string('avatar_color', 30)->default('#1E3A54');
            $table->string('initials', 4);
            $table->string('email');
            $table->string('phone');
            $table->string('website');
            $table->string('linkedin')->nullable();
            $table->integer('years_experience')->default(0);
            $table->integer('cases_count')->default(0);
            $table->integer('cases_won')->default(0);
            $table->integer('cases_lost')->default(0);
            $table->integer('cases_settled')->default(0);
            $table->integer('cases_active')->default(0);
            $table->string('financial_recovery')->nullable();
            $table->string('fee_structure')->nullable();
            $table->boolean('is_certified')->default(false);
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->string('availability', 20)->default('available'); // available, busy, unavailable
            $table->string('criminal_record')->default('CLEARED');
            $table->string('bar_discipline')->default('CLEARED');
            $table->text('trial_style')->nullable();
            $table->json('peer_reviews')->nullable();
            $table->json('recent_activity')->nullable();
            $table->json('practice_areas')->nullable();
            $table->json('trial_style_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyers');
    }
};
