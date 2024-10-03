<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject_name',
        'user_id'
    ];

    public function students():BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_subjects');
    }

    public function courses():BelongsToMany
    {
      return $this->BelongsToMany(Course::class, 'course_subjects');
    }

    public function teachers():BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }
}
