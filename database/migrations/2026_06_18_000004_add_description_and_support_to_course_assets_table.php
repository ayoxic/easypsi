<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_assets', function (Blueprint $table): void {
            $table->longText('description_body')->nullable()->after('quiz_body');
            $table->longText('support_body')->nullable()->after('description_body');
        });
    }

    public function down(): void
    {
        Schema::table('course_assets', function (Blueprint $table): void {
            $table->dropColumn(['description_body', 'support_body']);
        });
    }
};
