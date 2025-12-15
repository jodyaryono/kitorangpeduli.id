<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old constraint first
        DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_status_check');

        // Update existing 'in_progress' to 'draft' if any
        DB::statement("UPDATE responses SET status = 'draft' WHERE status = 'in_progress'");

        // Add new constraint with draft option
        DB::statement("ALTER TABLE responses ADD CONSTRAINT responses_status_check CHECK (status::text = ANY (ARRAY['draft'::text, 'in_progress'::text, 'completed'::text]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'draft' back to 'in_progress'
        DB::statement("UPDATE responses SET status = 'in_progress' WHERE status = 'draft'");

        // Restore old constraint
        DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_status_check');
        DB::statement("ALTER TABLE responses ADD CONSTRAINT responses_status_check CHECK (status::text = ANY (ARRAY['in_progress'::text, 'completed'::text]))");
    }
};
