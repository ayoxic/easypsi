<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_lesson_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_lesson_id')->constrained('teacher_lessons')->cascadeOnDelete();
            $table->string('part', 20);
            $table->string('content_type', 20)->default('youtube');
            $table->string('youtube_url')->nullable();
            $table->longText('quiz_body')->nullable();
            $table->longText('description_body')->nullable();
            $table->longText('support_body')->nullable();
            $table->timestamps();

            $table->unique(['teacher_lesson_id', 'part']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_lesson_assets');
    }
};
