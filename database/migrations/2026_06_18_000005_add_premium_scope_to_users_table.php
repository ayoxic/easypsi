<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('premium_duration')->nullable()->after('premium_expires_at');
            $table->string('premium_level_key')->nullable()->after('premium_duration');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['premium_duration', 'premium_level_key']);
        });
    }
};
