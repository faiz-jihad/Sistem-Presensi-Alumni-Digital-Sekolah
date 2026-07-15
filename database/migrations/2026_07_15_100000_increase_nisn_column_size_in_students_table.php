<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // NISN and NIP size constraints are handled by the import classes.
    }

    public function down(): void
    {
        // No schema changes to reverse.
    }
};
