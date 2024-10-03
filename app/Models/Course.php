<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'class',
        'user_id'
    ];



    public function students():BelongsToMany
    {
      return $this->BelongsToMany(Student::class, 'course_students');
    }


    public function subjects():BelongsToMany
    {
      return $this->BelongsToMany(Subject::class, 'course_subjects');
    }

    public function teachers():BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_courses');
    }


}
