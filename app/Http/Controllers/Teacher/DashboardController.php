<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = DB::table('teachers')
            ->where('user_id', auth()->id())
            ->value('teacher_id');

        $assignments = DB::table('teacher_subject_mapping as tsm')
            ->join('subjects as s', 's.subject_id', '=', 'tsm.subject_id')
            ->join('classes as c', 'c.class_id', '=', 'tsm.class_id')
            ->join('semesters as sem', 'sem.semester_id', '=', 'c.semester_id')
            ->select(
                'tsm.mapping_id',
                's.subject_name',
                'c.section',
                'sem.semester_number',
                'c.class_id',
                's.subject_id'
            )
            ->where('tsm.teacher_id', $teacherId)
            ->get();

        return view('teacher.dashboard', compact('assignments'));
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

    public function completeChapter(Request $request)
    {
        DB::table('chapters')
            ->where('chapter_id', $request->chapter_id)
            ->update([
                'teacher_status' => 'COMPLETED'
            ]);

        return back()->with('success', 'Chapter marked completed');
    }
}
