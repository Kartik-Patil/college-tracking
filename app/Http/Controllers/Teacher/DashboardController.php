<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = DB::table('teachers')
            ->where('user_id', auth()->id())
            ->value('teacher_id');

        /* ---------------------------
           FILTER DATA
        ----------------------------*/
        $filters = DB::table('teacher_subject_mapping as tsm')
            ->join('subjects as s', 's.subject_id', '=', 'tsm.subject_id')
            ->join('classes as c', 'c.class_id', '=', 'tsm.class_id')
            ->join('semesters as sem', 'sem.semester_id', '=', 'c.semester_id')
            ->where('tsm.teacher_id', $teacherId)
            ->select(
                'c.class_id',
                'c.section',
                'sem.semester_number',
                's.subject_id',
                's.subject_name'
            )
            ->orderBy('sem.semester_number')
            ->get();

        /* ---------------------------
           SELECTED FILTER
        ----------------------------*/
        $selectedClass   = $request->class_id;
        $selectedSubject = $request->subject_id;

        $students = [];

        if ($selectedClass && $selectedSubject) {
            $students = DB::table('class_student_mapping as csm')
                ->join('students as st', 'st.student_id', '=', 'csm.student_id')
                ->join('users as u', 'u.user_id', '=', 'st.user_id')
                ->where('csm.class_id', $selectedClass)
                ->select(
                    'st.student_id',
                    'u.usn'
                )
                ->orderBy('u.usn')
                ->get();
        }

        return view('teacher.dashboard', compact(
            'filters',
            'students',
            'selectedClass',
            'selectedSubject'
        ));
    }

    public function markAttendance(Request $request)
    {
        foreach ($request->attendance as $studentId => $status) {
            DB::table('attendance')->updateOrInsert(
                [
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'attendance_date' => $request->date
                ],
                ['status' => $status]
            );
        }

        return back()->with('success', 'Attendance saved');
    }

    public function saveMarks(Request $request)
    {
        foreach ($request->marks as $studentId => $marks) {
            DB::table('marks')->updateOrInsert(
                [
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'assessment_id' => $request->assessment_id
                ],
                ['marks_obtained' => $marks]
            );
        }

        return back()->with('success', 'Marks saved');
    }
}
