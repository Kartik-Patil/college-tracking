<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyAssignmentController extends Controller
{
    public function index()
    {
        $teachers = DB::table('teachers as t')
            ->join('users as u', 'u.user_id', '=', 't.user_id')
            ->select('t.teacher_id', DB::raw("CONCAT(u.first_name,' ',u.last_name,' (',u.usn,')') as name"))
            ->orderBy('u.first_name')
            ->get();

        $courses = DB::table('courses')->get();

        return view('admin.assign_faculty', compact('teachers', 'courses'));
    }

    public function fetchSemesters(Request $request)
    {
        return DB::table('semesters')
            ->where('course_id', $request->course_id)
            ->get();
    }

    public function fetchClasses(Request $request)
    {
        return DB::table('classes')
            ->where('semester_id', $request->semester_id)
            ->get();
    }

    public function fetchSubjects(Request $request)
    {
        return DB::table('subjects')
            ->where('semester_id', $request->semester_id)
            ->get();
    }

    public function assign(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'class_id'   => 'required',
            'subject_id' => 'required'
        ]);

        DB::table('teacher_subject_mapping')
            ->updateOrInsert(
                [
                    'class_id'   => $request->class_id,
                    'subject_id' => $request->subject_id
                ],
                [
                    'teacher_id' => $request->teacher_id
                ]
            );

        return back()->with('success', 'Faculty assigned successfully');
    }
}
