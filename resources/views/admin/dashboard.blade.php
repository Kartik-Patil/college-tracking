<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-gray-500">Students</h2>
            <p class="text-3xl font-bold">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-gray-500">Teachers</h2>
            <p class="text-3xl font-bold">{{ $totalTeachers }}</p>
        </div>
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-gray-500">Classes</h2>
            <p class="text-3xl font-bold">{{ $totalClasses }}</p>
        </div>
        <div class="bg-white p-5 rounded shadow">
            <h2 class="text-gray-500">Subjects</h2>
            <p class="text-3xl font-bold">{{ $totalSubjects }}</p>
        </div>
    </div>

    <!-- CR CONFIRMATION SUMMARY -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-semibold mb-4">CR Confirmation Summary</h2>

        <table class="w-full border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($crSummary as $row)
                <tr>
                    <td class="border p-2 text-center">{{ $row->confirmation_status }}</td>
                    <td class="border p-2 text-center">{{ $row->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- RECENT CR CONFIRMATIONS -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Recent CR Confirmations</h2>

        <table class="w-full border text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">USN</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Remarks</th>
                    <th class="border p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentCR as $row)
                <tr>
                    <td class="border p-2">{{ $row->usn }}</td>
                    <td class="border p-2 font-semibold">{{ $row->confirmation_status }}</td>
                    <td class="border p-2">{{ $row->remarks }}</td>
                    <td class="border p-2">{{ $row->confirmation_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
