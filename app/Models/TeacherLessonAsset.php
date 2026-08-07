<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherLessonAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_lesson_id',
        'part',
        'access_level',
        'content_type',
        'youtube_url',
        'media_path',
        'quiz_body',
        'description_body',
        'support_body',
        'support_file_path',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(TeacherLesson::class, 'teacher_lesson_id');
    }
}
