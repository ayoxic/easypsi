<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'locale',
        'subject_key',
        'subject_label',
        'level_key',
        'level_label',
        'title',
        'slug',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(TeacherLessonAsset::class);
    }
}
