<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_lesson_assets', function (Blueprint $table): void {
            $table->string('media_path')->nullable()->after('youtube_url');
            $table->string('support_file_path')->nullable()->after('support_body');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_lesson_assets', function (Blueprint $table): void {
            $table->dropColumn(['media_path', 'support_file_path']);
        });
    }
};
