<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

<h1 class="text-3xl font-bold mb-6">Teacher Dashboard</h1>

@if(session('success'))
<div class="bg-green-200 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@foreach($assignments as $a)
<div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="text-xl font-semibold">
        {{ $a->subject_name }} — Sem {{ $a->semester_number }} ({{ $a->section }})
    </h2>

    <!-- ATTENDANCE -->
    <form method="POST" action="{{ route('teacher.attendance') }}">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $a->subject_id }}">
        <input type="date" name="date" required class="border p-1">

        <table class="w-full mt-4 border">
            <tr class="bg-gray-200">
                <th class="border p-2">Student ID</th>
                <th class="border p-2">Status</th>
            </tr>

            @php
                $students = DB::table('class_student_mapping as csm')
                    ->join('students as s', 's.student_id', '=', 'csm.student_id')
                    ->where('csm.class_id', $a->class_id)
                    ->get();
            @endphp

            @foreach($students as $st)
            <tr>
                <td class="border p-2">{{ $st->student_id }}</td>
                <td class="border p-2">
                    <select name="attendance[{{ $st->student_id }}]" class="border">
                        <option value="P">Present</option>
                        <option value="A">Absent</option>
                    </select>
                </td>
            </tr>
            @endforeach
        </table>

        <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded">
            Save Attendance
        </button>
    </form>

    <!-- MARKS -->
    <form method="POST" action="{{ route('teacher.marks') }}" class="mt-6">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $a->subject_id }}">

        <select name="assessment_id" class="border p-1">
            <option value="1">IA1</option>
            <option value="2">IA2</option>
            <option value="3">IA3</option>
        </select>

        <table class="w-full mt-4 border">
            <tr class="bg-gray-200">
                <th class="border p-2">Student ID</th>
                <th class="border p-2">Marks</th>
            </tr>

            @foreach($students as $st)
            <tr>
                <td class="border p-2">{{ $st->student_id }}</td>
                <td class="border p-2">
                    <input type="number" name="marks[{{ $st->student_id }}]" class="border w-full" max="50">
                </td>
            </tr>
            @endforeach
        </table>

        <button class="mt-3 bg-green-600 text-white px-4 py-2 rounded">
            Save Marks
        </button>
    </form>

</div>
@endforeach

</body>
</html>
