<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_lesson_assets', function (Blueprint $table): void {
            $table->string('access_level', 20)->default('free')->after('part');
        });

        DB::table('teacher_lesson_assets')
            ->whereNull('access_level')
            ->orWhere('access_level', '')
            ->update(['access_level' => 'free']);
    }

    public function down(): void
    {
        Schema::table('teacher_lesson_assets', function (Blueprint $table): void {
            $table->dropColumn('access_level');
        });
    }
};
