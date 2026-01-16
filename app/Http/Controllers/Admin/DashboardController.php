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
}
