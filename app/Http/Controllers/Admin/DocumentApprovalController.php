<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DocumentApprovalController extends Controller
{
    public function index()
    {
        $documents = DB::table('generated_documents as d')
            ->join('students as s', 's.student_id', '=', 'd.student_id')
            ->join('users as u', 'u.user_id', '=', 's.user_id')
            ->select(
                'd.document_id',
                'u.usn',
                'd.document_type',
                'd.current_version',
                'd.status'
            )
            ->where('d.status', 'DRAFT')
            ->get();

        return view('admin.documents', compact('documents'));
    }

    public function approve($id)
    {
        DB::table('generated_documents')
            ->where('document_id', $id)
            ->update(['status' => 'APPROVED']);

        DB::table('document_approvals')->insert([
            'document_id' => $id,
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Document approved');
    }
}
