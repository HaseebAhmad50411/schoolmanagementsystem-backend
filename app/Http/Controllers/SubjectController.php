<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssociateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\SubjectResource;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Jobs\ContentJob;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $authId = auth()->user()->id;
        $search = $request->input('search', '');
        $perpage = $request->input('perpage', 10000);
        $cols = [
            'id',
            'subject_name',
            'user_id'
        ];

        $subjects = Subject::with('teachers', 'courses', 'students')
            ->where('user_id', $authId)
            ->where(function ($query) use ($search, $cols) {
                foreach ($cols as $col) {
                    $query->orWhere($col, 'LIKE', "%{$search}%");
                }
            })->paginate($perpage);


        return response()->json($subjects, 200);
    }


    public function store(StoreSubjectRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $data = $validated;
        $subject = $data;
        ContentJob::dispatch($data);
        return response()->json($subject, 201);
    }

    public function associate(AssociateSubjectRequest $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $validated = $request->validated();
        if ($request->has('teacher_id', 'student_id', 'course_id')) {
            $subject->teachers()->attach($validated['teacher_id']);
            $subject->students()->attach($validated['student_id']);
            $subject->courses()->attach($validated['course_id']);
            return response()->json($subject->load('teachers', 'students', 'courses'), 200);
        } else {
            return response()->json(['error' => 'Invalid association type'], 400);
        }
    }



    public function show(Subject $subject)
    {
        return new SubjectResource($subject);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $validated = $request->validated();
        $subject->update($validated);
        return response()->json($subject);
    }
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(null, 204);
    }
}
