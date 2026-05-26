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
        // Update existing cases status from won/lost to decided
        DB::table('lawyer_cases')
            ->whereIn('status', ['won', 'lost'])
            ->update(['status' => 'decided']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op ( irreversible update without extra tracking, but safe )
    }
};
