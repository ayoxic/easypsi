<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_lessons', function (Blueprint $table): void {
            $table->string('subject_key')->default('physics-chemistry')->after('locale');
            $table->string('subject_label')->default('Physique-Chimie')->after('subject_key');
            $table->index(['teacher_id', 'locale', 'subject_key', 'level_key'], 'teacher_lessons_teacher_locale_subject_level_index');
        });

        DB::table('teacher_lessons')
            ->whereNull('subject_key')
            ->orWhere('subject_key', '')
            ->update([
                'subject_key' => 'physics-chemistry',
                'subject_label' => 'Physique-Chimie',
            ]);
    }

    public function down(): void
    {
        Schema::table('teacher_lessons', function (Blueprint $table): void {
            $table->dropIndex('teacher_lessons_teacher_locale_subject_level_index');
            $table->dropColumn(['subject_key', 'subject_label']);
        });
    }
};
