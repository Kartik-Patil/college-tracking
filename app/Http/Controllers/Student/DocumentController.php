<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function generateMarkscard()
    {
        $userId = auth()->id();

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
            abort(403);
        }

        // 🔹 Check existing document
        $document = DB::table('generated_documents')
            ->where('student_id', $student->student_id)
            ->where('document_type', 'MARKSCARD')
            ->first();

        if ($document) {
            $documentId = $document->document_id;
            $version = $document->current_version + 1;

            DB::table('generated_documents')
                ->where('document_id', $documentId)
                ->update(['current_version' => $version, 'status' => 'DRAFT']);
        } else {
            $documentId = DB::table('generated_documents')->insertGetId([
                'student_id' => $student->student_id,
                'document_type' => 'MARKSCARD',
                'current_version' => 1,
                'status' => 'DRAFT'
            ]);
            $version = 1;
        }

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
            ->get();

        // ✅ Correct view path
        $pdf = Pdf::loadView('pdfs.markscard', compact('student', 'marks'));

        $filePath = "pdfs/markscards/markscard_{$student->usn}_v{$version}.pdf";
        Storage::put($filePath, $pdf->output());

        DB::table('document_versions')->insert([
            'document_id' => $documentId,
            'version_number' => $version,
            'file_path' => $filePath
        ]);

        return response()->download(storage_path("app/".$filePath));
    }
}
