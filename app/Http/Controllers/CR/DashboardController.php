<?php

namespace App\Http\Controllers\CR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get CR record
        $cr = DB::table('class_representatives')
            ->join('students', 'students.student_id', '=', 'class_representatives.student_id')
            ->where('students.user_id', auth()->id())
            ->select(
                'class_representatives.cr_id',
                'class_representatives.class_id',
                'class_representatives.subject_id'
            )
            ->first();

        if (!$cr) {
            abort(403, 'You are not assigned as CR');
        }

        // Fetch chapters for CR subject
        $chapters = DB::table('chapters as ch')
            ->join('syllabus as sy', 'sy.syllabus_id', '=', 'ch.syllabus_id')
            ->join('subjects as s', 's.subject_id', '=', 'sy.subject_id')
            ->where('s.subject_id', $cr->subject_id)
            ->select(
                'ch.chapter_id',
                'ch.chapter_name',
                'ch.planned_end_date',
                'ch.teacher_status'
            )
            ->get();

        return view('cr.dashboard', compact('cr', 'chapters'));
    }

    public function confirmChapter(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required',
            'confirmation_status' => 'required|in:CONFIRMED,DELAYED,NOT_COMPLETED',
            'remarks' => 'nullable|string'
        ]);

        DB::table('cr_confirmations')->insert([
            'chapter_id' => $request->chapter_id,
            'cr_id' => $request->cr_id,
            'confirmation_status' => $request->confirmation_status,
            'remarks' => $request->remarks
        ]);

        return back()->with('success', 'Chapter status submitted');
    }
}
