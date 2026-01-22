<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * ============================
     * ADMIN DASHBOARD (HOME)
     * ============================
     */
    public function index()
    {
        // BASIC COUNTS
        $totalStudents = DB::table('students')->count();
        $totalTeachers = DB::table('teachers')->count();
        $totalClasses  = DB::table('classes')->count();
        $totalSubjects = DB::table('subjects')->count();

        // CR CONFIRMATION SUMMARY
        $crSummary = DB::table('cr_confirmations')
            ->select(
                'confirmation_status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('confirmation_status')
            ->get();

        // RECENT CR CONFIRMATIONS
        $recentCR = DB::table('cr_confirmations as c')
            ->join('chapters as ch', 'ch.chapter_id', '=', 'c.chapter_id')
            ->join('class_representatives as cr', 'cr.cr_id', '=', 'c.cr_id')
            ->join('students as s', 's.student_id', '=', 'cr.student_id')
            ->join('users as u', 'u.user_id', '=', 's.user_id')
            ->select(
                'u.usn',
                'c.confirmation_status',
                'c.remarks',
                'c.confirmation_date'
            )
            ->orderByDesc('c.confirmation_date')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalSubjects',
            'crSummary',
            'recentCR'
        ));
    }

    /**
     * ===================================
     * FACULTY → SUBJECT → CLASS (VIEW)
     * ===================================
     */
    public function facultyAssignments(Request $request)
    {
        // FACULTY DROPDOWN
        $faculties = DB::table('teachers as t')
            ->join('users as u', 'u.user_id', '=', 't.user_id')
            ->select(
                't.teacher_id',
                DB::raw("CONCAT(u.first_name,' ',u.last_name,' (',u.usn,')') as faculty_name")
            )
            ->orderBy('u.first_name')
            ->get();

        $assignments = collect();
        $selectedFaculty = $request->teacher_id;

        if ($selectedFaculty) {
            $assignments = DB::table('teacher_subject_mapping as tsm')
                ->join('subjects as s', 's.subject_id', '=', 'tsm.subject_id')
                ->join('classes as c', 'c.class_id', '=', 'tsm.class_id')
                ->join('courses as co', 'co.course_id', '=', 'c.course_id')
                ->join('semesters as sem', 'sem.semester_id', '=', 'c.semester_id')
                ->where('tsm.teacher_id', $selectedFaculty)
                ->select(
                    'co.course_name',
                    'sem.semester_number',
                    'c.section',
                    's.subject_name',
                    'c.academic_year'
                )
                ->orderBy('co.course_name')
                ->orderBy('sem.semester_number')
                ->orderBy('c.section')
                ->get();
        }

        return view(
            'admin.faculty_assignments',
            compact('faculties', 'assignments', 'selectedFaculty')
        );
    }

    /**
     * ===================================
     * FACULTY ASSIGNMENT FORM (ADMIN)
     * ===================================
     */
public function assignFacultyForm()
{
    $faculties = DB::table('teachers as t')
        ->join('users as u', 'u.user_id', '=', 't.user_id')
        ->select(
            't.teacher_id',
            DB::raw("CONCAT(u.first_name,' ',u.last_name,' (',u.usn,')') as name")
        )
        ->orderBy('u.first_name')
        ->get();

    $courses = DB::table('courses')
        ->select('course_id', 'course_name')
        ->orderBy('course_name')
        ->get();

    return view('admin.assign_faculty', compact(
        'faculties',
        'courses'
    ));
}


    /**
     * ===================================
     * SAVE FACULTY ASSIGNMENT
     * ===================================
     */
    public function assignFacultySave(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'class_id'   => 'required',
            'subject_id' => 'required',
        ]);

        DB::table('teacher_subject_mapping')
            ->updateOrInsert(
                [
                    'class_id'   => $request->class_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'teacher_id' => $request->teacher_id,
                ]
            );

        return back()->with('success', 'Faculty assigned successfully');
    }

    public function getSemesters($courseId)
    {
        return DB::table('semesters')
            ->where('course_id', $courseId)
            ->select('semester_id', 'semester_number')
            ->orderBy('semester_number')
            ->get();
    }

    public function getClasses($semesterId)
    {
        return DB::table('classes')
            ->where('semester_id', $semesterId)
            ->select('class_id', 'section')
            ->orderBy('section')
            ->get();
    }

    public function getSubjects($semesterId)
    {
        return DB::table('subjects')
            ->where('semester_id', $semesterId)
            ->select('subject_id', 'subject_name')
            ->orderBy('subject_name')
            ->get();
    }
    /**
 * ===========================
 * STUDENT SEARCH FORM
 * ===========================
 */
public function studentSearchForm()
{
    return view('admin.student_search');
}

/**
 * ===========================
 * STUDENT SEARCH RESULT
 * ===========================
 */
public function studentSearchResult(Request $request)
{
    $request->validate([
        'usn' => 'required|string'
    ]);

    // BASIC STUDENT INFO
    $student = DB::table('users as u')
        ->join('students as st', 'st.user_id', '=', 'u.user_id')
        ->leftJoin('class_student_mapping as csm', 'csm.student_id', '=', 'st.student_id')
        ->leftJoin('classes as c', 'c.class_id', '=', 'csm.class_id')
        ->leftJoin('semesters as sem', 'sem.semester_id', '=', 'c.semester_id')
        ->leftJoin('courses as co', 'co.course_id', '=', 'c.course_id')
        ->where('u.usn', $request->usn)
        ->select(
            'st.student_id',
            'u.usn',
            'u.first_name',
            'u.last_name',
            'u.dob',
            'co.course_name',
            'sem.semester_number',
            'c.section',
            'c.academic_year'
        )
        ->first();

    if (!$student) {
        return back()->withErrors(['usn' => 'Student not found']);
    }

    // ATTENDANCE
    $attendance = DB::table('attendance as a')
        ->join('subjects as s', 's.subject_id', '=', 'a.subject_id')
        ->where('a.student_id', $student->student_id)
        ->select(
            's.subject_name',
            DB::raw("SUM(a.status='P') as present"),
            DB::raw("COUNT(*) as total")
        )
        ->groupBy('s.subject_name')
        ->get();

    // MARKS
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

    // DOCUMENTS
    $documents = DB::table('generated_documents')
        ->where('student_id', $student->student_id)
        ->get();

    return view('admin.student_search', compact(
        'student',
        'attendance',
        'marks',
        'documents'
    ));
}

public function editStudent($id)
{
    $student = DB::table('users')
        ->join('students', 'students.user_id', '=', 'users.user_id')
        ->where('students.student_id', $id)
        ->select(
            'students.student_id',
            'users.user_id',
            'users.usn',
            'users.first_name',
            'users.last_name',
            'users.dob',
            'users.is_active'
        )
        ->first();

    abort_if(!$student, 404);

    return view('admin.edit_student', compact('student'));
}
public function updateStudent(Request $request, $id)
{
    $request->validate([
        'first_name' => 'required',
        'last_name'  => 'required',
        'dob'        => 'required|date',
    ]);

    DB::table('students')
        ->where('student_id', $id)
        ->join('users', 'users.user_id', '=', 'students.user_id')
        ->update([
            'users.first_name' => $request->first_name,
            'users.last_name'  => $request->last_name,
            'users.dob'        => $request->dob,
        ]);

    return back()->with('success', 'Student updated successfully');
}
public function toggleStudent($id)
{
    $userId = DB::table('students')->where('student_id', $id)->value('user_id');

    DB::table('users')->where('user_id', $userId)
        ->update([
            'is_active' => DB::raw('NOT is_active')
        ]);

    return back()->with('success', 'Student status updated');
}
public function searchUSN(Request $request)
{
    return DB::table('users')
        ->where('role_id', 3)
        ->where('usn', 'LIKE', "%{$request->q}%")
        ->limit(10)
        ->pluck('usn');
}


}
