<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('premium_teacher_id')
                ->nullable()
                ->after('premium_level_key')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('premium_subject_key')->nullable()->after('premium_teacher_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('premium_teacher_id');
            $table->dropColumn('premium_subject_key');
        });
    }
};
