<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'lesson_slug',
        'lesson_title',
        'part',
        'content_type',
        'media_path',
        'quiz_body',
        'description_body',
        'support_body',
        'support_file_path',
        'access_level',
    ];
}
