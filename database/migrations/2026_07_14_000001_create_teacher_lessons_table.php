<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_lessons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('level_key');
            $table->string('level_label');
            $table->string('title');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['teacher_id', 'locale', 'slug']);
            $table->index(['teacher_id', 'locale', 'level_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_lessons');
    }
};
