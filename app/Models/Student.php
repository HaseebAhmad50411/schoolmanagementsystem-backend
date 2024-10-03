<?php

namespace App\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'user_id'
    ];
    protected $with = [
        'image',
    ];

  public function courses():BelongsToMany
  {
    return $this->belongsToMany(Course::class, 'course_students');
  }

  public function subjects():BelongsToMany
  {
      return $this->belongsToMany(Subject::class, 'student_subjects');
  }

  public function teachers():BelongsToMany
  {
    return $this->belongsToMany(Teacher::class, 'teacher_students');
  }

  public function image():MorphOne
  {
    return $this->MorphOne(Image::class, 'imageable')->latest();
  }

}
