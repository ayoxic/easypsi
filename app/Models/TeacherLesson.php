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
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeForAudience($query, string $selection)
    {
        $keys = static::query()->distinct()->pluck('level_key')
            ->filter(fn ($key) => \App\Support\LevelAudience::matches($key, $selection));

        return $query->whereIn('level_key', $keys);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(TeacherLessonAsset::class);
    }
}
