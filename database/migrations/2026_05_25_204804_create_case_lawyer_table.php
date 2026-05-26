<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('case_lawyer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_case_id')->constrained('lawyer_cases')->onDelete('cascade');
            $table->foreignId('lawyer_id')->constrained('lawyers')->onDelete('cascade');
            $table->string('outcome', 20)->default('active'); // won, lost, settled, active
            $table->timestamps();
        });

        // Copy existing case relations into the pivot table
        $cases = DB::table('lawyer_cases')->get();
        foreach ($cases as $case) {
            if ($case->lawyer_id) {
                DB::table('case_lawyer')->insert([
                    'lawyer_case_id' => $case->id,
                    'lawyer_id' => $case->lawyer_id,
                    'outcome' => $case->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_lawyer');
    }
};
