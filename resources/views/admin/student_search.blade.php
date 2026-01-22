<!DOCTYPE html>
<html>
<head>
    <title>Student Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold mb-6">Search Student by USN</h1>

<!-- SEARCH FORM -->
<div class="relative">
<form method="POST"
      action="{{ route('admin.student.search.result') }}"
      class="bg-white p-4 rounded shadow mb-6 w-96 relative">
@csrf

<label class="font-semibold">Enter USN</label>

<input type="text"
       id="usnInput"
       name="usn"
       class="border p-2 w-full mt-2"
       autocomplete="off"
       required>

<ul id="suggestions"
    class="absolute bg-white border w-full mt-1 hidden z-10 max-h-40 overflow-y-auto">
</ul>

<button class="bg-indigo-600 text-white px-4 py-2 rounded mt-4 w-full">
    Search
</button>

@if($errors->any())
<p class="text-red-600 mt-2">{{ $errors->first() }}</p>
@endif
</form>
</div>

@if(isset($student))

<!-- STUDENT INFO -->
<div class="bg-white p-4 rounded shadow mb-6 flex justify-between items-start">
    <div>
        <h2 class="text-xl font-semibold mb-2">Student Details</h2>
        <p><b>Name:</b> {{ $student->first_name }} {{ $student->last_name }}</p>
        <p><b>USN:</b> {{ $student->usn }}</p>
        <p><b>DOB:</b> {{ $student->dob }}</p>
        <p><b>Course:</b> {{ $student->course_name }}</p>
        <p><b>Semester:</b> {{ $student->semester_number }}</p>
        <p><b>Section:</b> {{ $student->section }}</p>
        <p><b>Academic Year:</b> {{ $student->academic_year }}</p>
    </div>

    <!-- ACTION BUTTON -->
    <div class="flex flex-col gap-2">
        <a href="{{ route('admin.student.edit', $student->student_id) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded text-center">
            Edit Student
        </a>
    </div>
</div>

<!-- ATTENDANCE -->
<div class="bg-white p-4 rounded shadow mb-6">
<h2 class="text-xl font-semibold mb-2">Attendance</h2>

<table class="w-full border">
<thead class="bg-gray-200">
<tr>
<th class="border p-2">Subject</th>
<th class="border p-2">Present</th>
<th class="border p-2">Total</th>
<th class="border p-2">%</th>
</tr>
</thead>

<tbody>
@foreach($attendance as $a)
<tr>
<td class="border p-2">{{ $a->subject_name }}</td>
<td class="border p-2">{{ $a->present }}</td>
<td class="border p-2">{{ $a->total }}</td>
<td class="border p-2 font-semibold">
{{ round(($a->present / $a->total) * 100, 2) }} %
</td>
</tr>
@endforeach
</tbody>
</table>
</div>

<!-- MARKS -->
<div class="bg-white p-4 rounded shadow mb-6">
<h2 class="text-xl font-semibold mb-2">Marks</h2>

<table class="w-full border">
<thead class="bg-gray-200">
<tr>
<th class="border p-2">Subject</th>
<th class="border p-2">Assessment</th>
<th class="border p-2">Marks</th>
</tr>
</thead>

<tbody>
@foreach($marks as $m)
<tr>
<td class="border p-2">{{ $m->subject_name }}</td>
<td class="border p-2">{{ $m->assessment_name }}</td>
<td class="border p-2 font-semibold">
{{ $m->marks_obtained }} / {{ $m->max_marks }}
</td>
</tr>
@endforeach
</tbody>
</table>
</div>

<!-- DOCUMENTS -->
<div class="bg-white p-4 rounded shadow">
<h2 class="text-xl font-semibold mb-2">Generated Documents</h2>

@if(count($documents))
<ul class="list-disc ml-6">
@foreach($documents as $d)
<li>
<b>{{ $d->document_type }}</b> |
Version {{ $d->current_version }} |
<span class="{{ $d->status === 'APPROVED' ? 'text-green-600' : 'text-yellow-600' }}">
{{ $d->status }}
</span>
</li>
@endforeach
</ul>
@else
<p class="text-gray-500">No documents generated</p>
@endif
</div>

@endif

<a href="{{ route('admin.dashboard') }}"
   class="inline-block mt-6 bg-gray-600 text-white px-4 py-2 rounded">
← Back to Dashboard
</a>

<!-- AUTOCOMPLETE SCRIPT -->
<script>
const input = document.getElementById('usnInput');
const list  = document.getElementById('suggestions');

input.addEventListener('keyup', () => {
    if (input.value.length < 2) {
        list.classList.add('hidden');
        return;
    }

    fetch(`/admin/search-usn?q=${input.value}`)
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            data.forEach(usn => {
                list.innerHTML += `
                <li class="p-2 hover:bg-gray-200 cursor-pointer"
                    onclick="selectUSN('${usn}')">${usn}</li>`;
            });
            list.classList.remove('hidden');
        });
});

function selectUSN(usn) {
    input.value = usn;
    list.classList.add('hidden');
}
</script>

</body>
</html>
