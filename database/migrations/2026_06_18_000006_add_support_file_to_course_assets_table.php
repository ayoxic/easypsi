<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_assets', function (Blueprint $table): void {
            $table->string('support_file_path')->nullable()->after('support_body');
        });
    }

    public function down(): void
    {
        Schema::table('course_assets', function (Blueprint $table): void {
            $table->dropColumn('support_file_path');
        });
    }
};
