<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Resources\StudentResource;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\AssociateStudentRequest;

class StudentController extends Controller
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
        $students = Student::with('subjects', 'courses', 'teachers', 'image')->where('user_id', $authId)->where(function ($query) use ($search, $cols) {
            foreach ($cols as $col) {
                $query->orWhere($col, 'LIKE', "%{$search}%");
            }
        })->paginate($perpage);
        return response()->json($students, 200);
    }

    public function __construct(private ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        // Create the student
        $student = Student::create([
            'user_id' => auth()->user()->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($validated['img_id']) {
           $this->imageService->updateImage(
            $validated['img_id'],
            $student->id,
            "App\Models\Student"
           );
        }


        return response()->json($student, 201);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $validated = $request->validated();
        $student->update($validated);
        if ($validated['img_id']) {
            $this->imageService->updateImage(
             $validated['img_id'],
             $student->id,
             "App\Models\Student"
            );
         }
        return response()->json($student);
    }

    public function associate(AssociateStudentRequest $request, $id)
    {
        $student = Student::findOrFail($id);
        $validated = $request->validated();
        if ($request->has('teacher_id', 'course_id', 'subject_id')) {
            $student->teachers()->attach($validated['teacher_id']);
            $student->courses()->attach($validated['course_id']);
            $student->subjects()->attach($validated['subject_id']);
            return response()->json($student->load('teachers', 'courses', 'subjects'), 200);
        } else {
            return response()->json(['error' => 'Invalid association type'], 400);
        }
    }

    public function show(Student $student)
    {
        return new StudentResource($student);
    }


    public function destroy(Student $student)
    {
        Storage::disk('public')->delete($student->image);
        $student->delete();
        return response()->json(null, 204);
    }
}
