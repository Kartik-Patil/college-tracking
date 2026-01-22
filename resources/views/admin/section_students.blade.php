<!DOCTYPE html>
<html>
<head>
    <title>Section Wise Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold mb-6">Section Wise Student List</h1>

<!-- FILTERS -->
<div class="bg-white p-4 rounded shadow mb-6 grid grid-cols-4 gap-4">

    <!-- COURSE -->
    <select id="course" class="border p-2">
        <option value="">Select Course</option>
        @foreach($courses as $c)
            <option value="{{ $c->course_id }}">{{ $c->course_name }}</option>
        @endforeach
    </select>

    <!-- SEMESTER -->
    <select id="semester" class="border p-2">
        <option value="">Select Semester</option>
    </select>

    <!-- SECTION -->
    <select id="section" class="border p-2">
        <option value="">Select Section</option>
        <option>A</option>
        <option>B</option>
        <option>C</option>
        <option>D</option>
    </select>

    <button onclick="loadStudents()"
            class="bg-indigo-600 text-white px-4 py-2 rounded">
        Load Students
    </button>
</div>

<!-- STUDENT TABLE -->
<div class="bg-white rounded shadow p-4">
<table class="w-full border">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">USN</th>
    <th class="border p-2">Name</th>
    <th class="border p-2">Action</th>
</tr>
</thead>

<tbody id="studentTable">
<tr>
    <td colspan="3" class="text-center p-4 text-gray-500">
        Select filters to view students
    </td>
</tr>
</tbody>
</table>
</div>

<!-- STUDENT DETAILS PANEL -->
<div id="studentDetails"
     class="hidden bg-white mt-6 p-4 rounded shadow border-l-4 border-blue-600">
</div>

<a href="{{ route('admin.dashboard') }}"
   class="inline-block mt-6 bg-gray-600 text-white px-4 py-2 rounded">
← Back to Dashboard
</a>

<!-- SCRIPT -->
<script>
const semesterSelect = document.getElementById('semester');
const studentPanel  = document.getElementById('studentDetails');

document.getElementById('course').addEventListener('change', e => {
    fetch(`/admin/semesters/${e.target.value}`)
        .then(r => r.json())
        .then(data => {
            semesterSelect.innerHTML = '<option value="">Select Semester</option>';
            data.forEach(s => {
                semesterSelect.innerHTML +=
                    `<option value="${s.semester_id}">Sem ${s.semester_number}</option>`;
            });
        });
});

function loadStudents() {
    const course   = document.getElementById('course').value;
    const semester = document.getElementById('semester').value;
    const section  = document.getElementById('section').value;

    if (!course || !semester || !section) return;

    // Clear old panel
    studentPanel.classList.add('hidden');
    studentPanel.innerHTML = '';

    fetch(`/admin/section-wise-students/list?course_id=${course}&semester_id=${semester}&section=${section}`)
        .then(r => r.json())
        .then(data => {
            const table = document.getElementById('studentTable');
            table.innerHTML = '';

            if (!data.length) {
                table.innerHTML =
                    `<tr><td colspan="3" class="text-center p-4">No students found</td></tr>`;
                return;
            }

            data.forEach(st => {
                table.innerHTML += `
                <tr>
                    <td class="border p-2 font-mono">${st.usn}</td>
                    <td class="border p-2">${st.first_name} ${st.last_name}</td>
                    <td class="border p-2 text-center">
                        <button onclick="viewStudent('${st.usn}')"
                                class="bg-blue-600 text-white px-3 py-1 rounded">
                            View
                        </button>
                    </td>
                </tr>`;
            });
        });
}

function viewStudent(usn) {
    fetch(`/admin/student-info/${usn}`)
        .then(res => res.json())
        .then(st => {
            studentPanel.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Student Details</h2>
                    <button onclick="closePanel()"
                            class="text-red-600 font-bold">✕</button>
                </div>

                <p><b>Name:</b> ${st.first_name} ${st.last_name}</p>
                <p><b>USN:</b> ${st.usn}</p>
                <p><b>DOB:</b> ${st.dob}</p>
                <p><b>Course:</b> ${st.course_name}</p>
                <p><b>Semester:</b> ${st.semester_number}</p>
                <p><b>Section:</b> ${st.section}</p>
                <p><b>Academic Year:</b> ${st.academic_year}</p>
            `;

            studentPanel.classList.remove('hidden');
            studentPanel.scrollIntoView({ behavior: 'smooth' });
        });
}

function closePanel() {
    studentPanel.classList.add('hidden');
    studentPanel.innerHTML = '';
}
</script>

</body>
</html>
