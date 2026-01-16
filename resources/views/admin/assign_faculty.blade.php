<!DOCTYPE html>
<html>
<head>
    <title>Assign Faculty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Assign Faculty to Subject</h1>

    <!-- BACK BUTTON -->
    <a href="{{ route('admin.dashboard') }}"
       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
        ← Back to Dashboard
    </a>
</div>

@if(session('success'))
<div class="bg-green-200 p-3 mb-4 rounded">
    {{ session('success') }}
</div>
@endif

<form method="POST"
      action="{{ route('admin.assign.faculty.save') }}"
      class="bg-white p-6 rounded shadow grid grid-cols-2 gap-4">
@csrf

<!-- FACULTY -->
<div>
    <label class="font-semibold">Faculty</label>
    <select name="teacher_id" class="border p-2 w-full" required>
        <option value="">Select Faculty</option>
        @foreach($faculties as $f)
            <option value="{{ $f->teacher_id }}">{{ $f->name }}</option>
        @endforeach
    </select>
</div>

<!-- COURSE -->
<div>
    <label class="font-semibold">Course</label>
    <select id="course" class="border p-2 w-full" required>
        <option value="">Select Course</option>
        @foreach($courses as $c)
            <option value="{{ $c->course_id }}">{{ $c->course_name }}</option>
        @endforeach
    </select>
</div>

<!-- SEMESTER -->
<div>
    <label class="font-semibold">Semester</label>
    <select id="semester" class="border p-2 w-full" required></select>
</div>

<!-- CLASS -->
<div>
    <label class="font-semibold">Class / Section</label>
    <select name="class_id" id="classSelect" class="border p-2 w-full" required></select>
</div>

<!-- SUBJECT -->
<div class="col-span-2">
    <label class="font-semibold">Subject</label>
    <select name="subject_id" id="subject" class="border p-2 w-full" required></select>
</div>

<!-- ACTION BUTTONS -->
<div class="col-span-2 flex gap-4">
    <button class="bg-indigo-600 text-white px-6 py-2 rounded w-full hover:bg-indigo-700">
        Assign Faculty
    </button>

    <a href="{{ route('admin.dashboard') }}"
       class="bg-red-500 text-white px-6 py-2 rounded w-full text-center hover:bg-red-600">
        Cancel
    </a>
</div>

</form>

<script>
const semester = document.getElementById('semester');
const classSelect = document.getElementById('classSelect');
const subject = document.getElementById('subject');

document.getElementById('course').addEventListener('change', e => {
    fetch(`/admin/semesters/${e.target.value}`)
        .then(r => r.json())
        .then(data => {
            semester.innerHTML = '<option value="">Select Semester</option>';
            data.forEach(s => {
                semester.innerHTML += `<option value="${s.semester_id}">Sem ${s.semester_number}</option>`;
            });
        });
});

semester.addEventListener('change', e => {
    fetch(`/admin/classes/${e.target.value}`)
        .then(r => r.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select Section</option>';
            data.forEach(c => {
                classSelect.innerHTML += `<option value="${c.class_id}">Section ${c.section}</option>`;
            });
        });

    fetch(`/admin/subjects/${e.target.value}`)
        .then(r => r.json())
        .then(data => {
            subject.innerHTML = '<option value="">Select Subject</option>';
            data.forEach(s => {
                subject.innerHTML += `<option value="${s.subject_id}">${s.subject_name}</option>`;
            });
        });
});
</script>

</body>
</html>
