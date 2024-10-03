<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TeacherResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Requests\AssociateTeacherRequest;

class TeacherController extends Controller
{
    public function index()
    {
        $authId = auth()->user()->id;
        $search = $request['search'] ?? '';
        $perpage = $request['perpage'] ?? 10000;
        $cols = [
            'name',
            'email',
            'user_id'
        ];

        $teachers = Teacher::with('courses', 'subjects', 'students', 'image')->where('user_id', $authId)->where(function ($query) use ($search, $cols) {
            foreach ($cols as $col) {
                $query->orWhere($col, 'LIKE', "%{$search}%");
            }
        })->paginate($perpage);

        return response()->json($teachers, 200);
    }
    public function __construct(private ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
        public function store(StoreTeacherRequest $request)
    {
        $validated = $request->validated();

        $teacher = Teacher::create([
            'user_id' => auth()->user()->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($validated['img_id']) {
           $this->imageService->updateImage(
            $validated['img_id'],
            $teacher->id,
            "App\Models\Teacher"
           );
        }


        return response()->json($teacher, 201);
    }


    public function associate(AssociateTeacherRequest $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $validated = $request->validated();
        if ($request->has('course_id', 'student_id', 'subject_id')) {
            $teacher->courses()->attach($validated['course_id']);
            $teacher->students()->attach($validated['student_id']);
            $teacher->subjects()->attach($validated['subject_id']);
            return response()->json($teacher->load('courses', 'students', 'subjects'), 200);
        } else {
            return response()->json(['error' => 'Invalid association type'], 400);
        }
    }


    public function show(Teacher $teacher)
    {
        return new TeacherResource($teacher);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $validated = $request->validated();
        $teacher->update($validated);
        if ($validated['img_id']) {
            $this->imageService->updateImage(
             $validated['img_id'],
             $teacher->id,
             "App\Models\Teacher"
            );
         }
        return response()->json($teacher);
    }
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return response()->json(null, 204);
    }
}




















