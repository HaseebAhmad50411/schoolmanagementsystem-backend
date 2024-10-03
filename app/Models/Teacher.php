<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'image',
        'user_id'
    ];
    public function courses():BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'teacher_courses');
    }

    public function subjects():BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects');
    }

    public function students():BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'teacher_students');
    }
    public function image():MorphOne
    {
        return $this->MorphOne(Image::class, 'imageable')->latest();
    }

}
