<!DOCTYPE html>
<html>
<head>
    <title>Faculty Assignments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="flex justify-between mb-6">
    <h1 class="text-3xl font-bold">Faculty Assignment Viewer</h1>
    <a href="{{ route('admin.dashboard') }}"
       class="bg-gray-600 text-white px-4 py-2 rounded">
       Back
    </a>
</div>

<!-- FILTER -->
<form method="GET" class="bg-white p-4 rounded shadow mb-6">
    <label class="font-semibold block mb-2">Select Faculty</label>
    <div class="flex gap-4">
        <select name="teacher_id" class="border p-2 w-96" required>
            <option value="">-- Choose Faculty --</option>
            @foreach($faculties as $f)
                <option value="{{ $f->teacher_id }}"
                    {{ $selectedFaculty == $f->teacher_id ? 'selected' : '' }}>
                    {{ $f->faculty_name }}
                </option>
            @endforeach
        </select>

        <button class="bg-indigo-600 text-white px-6 py-2 rounded">
            View Assignments
        </button>
    </div>
</form>

@if($selectedFaculty)

<table class="w-full bg-white border rounded shadow">
    <thead class="bg-gray-200">
        <tr>
            <th class="border p-2">Course</th>
            <th class="border p-2">Semester</th>
            <th class="border p-2">Section</th>
            <th class="border p-2">Subject</th>
            <th class="border p-2">Academic Year</th>
        </tr>
    </thead>
    <tbody>
        @forelse($assignments as $a)
        <tr>
            <td class="border p-2">{{ $a->course_name }}</td>
            <td class="border p-2 text-center">{{ $a->semester_number }}</td>
            <td class="border p-2 text-center">{{ $a->section }}</td>
            <td class="border p-2">{{ $a->subject_name }}</td>
            <td class="border p-2">{{ $a->academic_year }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="border p-4 text-center text-gray-500">
                No assignments found for this faculty
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endif

</body>
</html>
