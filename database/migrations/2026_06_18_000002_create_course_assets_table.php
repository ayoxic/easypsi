<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_assets', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5);
            $table->string('lesson_slug');
            $table->string('lesson_title');
            $table->string('part', 20);
            $table->string('content_type', 20);
            $table->string('media_path')->nullable();
            $table->longText('quiz_body')->nullable();
            $table->string('access_level', 20)->default('free');
            $table->timestamps();

            $table->unique(['locale', 'lesson_slug', 'part']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assets');
    }
};
