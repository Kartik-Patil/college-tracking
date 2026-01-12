<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Student basic info
        $student = DB::table('students')
            ->join('users', 'users.user_id', '=', 'students.user_id')
            ->where('students.user_id', $userId)
            ->select(
                'students.student_id',
                'users.usn',
                'users.first_name',
                'users.last_name'
            )
            ->first();

        if (!$student) {
            abort(403, 'Not a student');
        }

        // Attendance summary
        $attendance = DB::table('attendance as a')
            ->join('subjects as s', 's.subject_id', '=', 'a.subject_id')
            ->where('a.student_id', $student->student_id)
            ->select(
                's.subject_name',
                DB::raw("SUM(a.status='P') as present_days"),
                DB::raw("COUNT(*) as total_days")
            )
            ->groupBy('s.subject_name')
            ->get();

        // Marks
        $marks = DB::table('marks as m')
            ->join('subjects as s', 's.subject_id', '=', 'm.subject_id')
            ->join('assessments as a', 'a.assessment_id', '=', 'm.assessment_id')
            ->where('m.student_id', $student->student_id)
            ->select(
                's.subject_name',
                'a.assessment_name',
                'm.marks_obtained',
                'a.max_marks'
            )
            ->orderBy('s.subject_name')
            ->get();

        return view('student.dashboard', compact('student', 'attendance', 'marks'));
    }
}
