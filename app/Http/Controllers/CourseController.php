<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssociateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $authId = auth()->user()->id;
        $search = $request->input('search', '');
        $perPage = $request->input('perpage', 15);
        $cols = ['class', 'user_id'];

        $courses = Course::with('teachers', 'students', 'subjects')
            ->where('user_id', $authId)
            ->where(function ($query) use ($search, $cols) {
                foreach ($cols as $col) {
                    $query->orWhere($col, 'LIKE', "%{$search}%");
                }
            })
            ->paginate($perPage);

        return response()->json($courses, 200);
    }

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->user()->id;
        $course = Course::create($validated);
        return response()->json($course, 201);
    }
    public function associate(AssociateCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validated();
        if ($request->has('teacher_id', 'student_id', 'subject_id')) {
            $course->teachers()->attach($validated['teacher_id']);
            $course->students()->attach($validated['student_id']);
            $course->subjects()->attach($validated['subject_id']);
            return response()->json($course->load('teachers', 'students', 'subjects'), 200);
        } else {
            return response()->json(['error' => 'Invalid association type'], 400);
        }
    }




    public function show(Course $Course)
    {
        return new CourseResource($Course);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $validated = $request->validated();
        $course->update($validated);
        return response()->json($course);
    }
    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(null, 204);
    }
}
