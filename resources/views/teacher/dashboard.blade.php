<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Teacher Dashboard</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="bg-red-600 text-white px-4 py-2 rounded">
            Logout
        </button>
    </form>
</div>

@if(session('success'))
<div class="bg-green-200 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@foreach($assignments as $a)
<div class="bg-white p-4 rounded shadow mb-6">

    <h2 class="text-xl font-semibold mb-2">
        {{ $a->subject_name }} — Sem {{ $a->semester_number }} (Section {{ $a->section }})
    </h2>

    <!-- ATTENDANCE -->
    <form method="POST" action="{{ route('teacher.attendance') }}">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $a->subject_id }}">

        <label class="block mt-2 font-semibold">Attendance Date</label>
        <input type="date" name="date" required class="border p-1 mt-1 mb-3">

        <table class="w-full border mb-4">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">USN</th>
                    <th class="border p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($a->students as $st)
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

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Save Attendance
        </button>
    </form>

    <!-- MARKS -->
    <form method="POST" action="{{ route('teacher.marks') }}" class="mt-6">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $a->subject_id }}">

        <label class="font-semibold">Assessment</label>
        <select name="assessment_id" class="border p-1 ml-2">
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
                @foreach($a->students as $st)
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

        <button class="mt-3 bg-green-600 text-white px-4 py-2 rounded">
            Save Marks
        </button>
    </form>

</div>
@endforeach

</body>
</html>
