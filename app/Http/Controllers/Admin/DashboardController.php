<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalStudents = DB::table('students')->count();
        $totalTeachers = DB::table('teachers')->count();
        $totalClasses  = DB::table('classes')->count();
        $totalSubjects = DB::table('subjects')->count();

        // CR confirmations summary
        $crSummary = DB::table('cr_confirmations')
            ->select(
                'confirmation_status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('confirmation_status')
            ->get();

        // Recent CR confirmations
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
}
