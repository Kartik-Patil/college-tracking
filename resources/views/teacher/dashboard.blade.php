<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="flex justify-between mb-6">
    <h1 class="text-3xl font-bold">Teacher Dashboard</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="bg-red-600 text-white px-4 py-2 rounded">
            Logout
        </button>
    </form>
</div>

@if(session('success'))
<div class="bg-green-200 p-3 mb-4 rounded">
    {{ session('success') }}
</div>
@endif

<!-- FILTER FORM -->
<form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-3 gap-4">

    <div>
        <label class="font-semibold">Semester / Class</label>
        <select name="class_id" class="border p-2 w-full" required>
            <option value="">Select</option>
            @foreach($filters as $f)
                <option value="{{ $f->class_id }}"
                    {{ $selectedClass == $f->class_id ? 'selected' : '' }}>
                    Sem {{ $f->semester_number }} - Section {{ $f->section }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="font-semibold">Subject</label>
        <select name="subject_id" class="border p-2 w-full" required>
            <option value="">Select</option>
            @foreach($filters as $f)
                <option value="{{ $f->subject_id }}"
                    {{ $selectedSubject == $f->subject_id ? 'selected' : '' }}>
                    {{ $f->subject_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex items-end">
        <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
            Load Students
        </button>
    </div>

</form>

@if(count($students))

<!-- ATTENDANCE -->
<form method="POST" action="{{ route('teacher.attendance') }}" class="bg-white p-4 rounded shadow mb-6">
@csrf
<input type="hidden" name="subject_id" value="{{ $selectedSubject }}">

<label class="font-semibold">Attendance Date</label>
<input type="date" name="date" class="border p-2 mb-4" required>

<table class="w-full border">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">USN</th>
    <th class="border p-2">Status</th>
</tr>
</thead>
<tbody>
@foreach($students as $st)
<tr>
    <td class="border p-2 font-mono">{{ $st->usn }}</td>
    <td class="border p-2">
        <select name="attendance[{{ $st->student_id }}]" class="border">
            <option value="P">Present</option>
            <option value="A">Absent</option>
        </select>
    </td>
</tr>
@endforeach
</tbody>
</table>

<button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
    Save Attendance
</button>
</form>

<!-- MARKS -->
<form method="POST" action="{{ route('teacher.marks') }}" class="bg-white p-4 rounded shadow">
@csrf
<input type="hidden" name="subject_id" value="{{ $selectedSubject }}">

<label class="font-semibold">Assessment</label>
<select name="assessment_id" class="border p-2 ml-2">
    <option value="1">IA1</option>
    <option value="2">IA2</option>
    <option value="3">IA3</option>
</select>

<table class="w-full border mt-4">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">USN</th>
    <th class="border p-2">Marks</th>
</tr>
</thead>
<tbody>
@foreach($students as $st)
<tr>
    <td class="border p-2 font-mono">{{ $st->usn }}</td>
    <td class="border p-2">
        <input type="number"
               name="marks[{{ $st->student_id }}]"
               max="50"
               class="border w-full"
               required>
    </td>
</tr>
@endforeach
</tbody>
</table>

<button class="mt-4 bg-green-600 text-white px-4 py-2 rounded">
    Save Marks
</button>
</form>

@endif

</body>
</html>
