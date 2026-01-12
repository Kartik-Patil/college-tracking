<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold mb-2">Student Dashboard</h1>

<p class="mb-6 text-gray-700">
    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->usn }})
</p>

<!-- ATTENDANCE -->
<h2 class="text-xl font-semibold mb-3">Attendance</h2>

<table class="w-full bg-white border mb-8">
    <thead class="bg-gray-200">
        <tr>
            <th class="border p-2">Subject</th>
            <th class="border p-2">Present</th>
            <th class="border p-2">Total</th>
            <th class="border p-2">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendance as $a)
        <tr>
            <td class="border p-2">{{ $a->subject_name }}</td>
            <td class="border p-2">{{ $a->present_days }}</td>
            <td class="border p-2">{{ $a->total_days }}</td>
            <td class="border p-2 font-semibold">
                {{ round(($a->present_days / $a->total_days) * 100, 2) }} %
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- MARKS -->
<h2 class="text-xl font-semibold mb-3">Internal Assessment Marks</h2>

<table class="w-full bg-white border">
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
<a href="{{ route('student.markscard.pdf') }}"
   class="bg-blue-600 text-white px-4 py-2 rounded inline-block mb-6">
   Download Markscard (PDF)
</a>

</body>
</html>
