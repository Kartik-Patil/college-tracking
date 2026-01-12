<!DOCTYPE html>
<html>
<head>
    <title>CR Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold mb-6">Class Representative Dashboard</h1>

@if(session('success'))
<div class="bg-green-200 p-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<table class="w-full bg-white rounded shadow border">
    <thead class="bg-gray-200">
        <tr>
            <th class="border p-2">Chapter</th>
            <th class="border p-2">Planned End Date</th>
            <th class="border p-2">Teacher Status</th>
            <th class="border p-2">Your Confirmation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chapters as $ch)
        <tr>
            <td class="border p-2">{{ $ch->chapter_name }}</td>
            <td class="border p-2">{{ $ch->planned_end_date }}</td>
            <td class="border p-2 font-semibold">{{ $ch->teacher_status }}</td>
            <td class="border p-2">
                <form method="POST" action="{{ route('cr.confirm') }}">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $ch->chapter_id }}">
                    <input type="hidden" name="cr_id" value="{{ $cr->cr_id }}">

                    <select name="confirmation_status" class="border p-1 w-full mb-2">
                        <option value="CONFIRMED">Confirmed</option>
                        <option value="DELAYED">Delayed</option>
                        <option value="NOT_COMPLETED">Not Completed</option>
                    </select>

                    <textarea
                        name="remarks"
                        class="border p-1 w-full mb-2"
                        placeholder="Remarks (optional)"
                    ></textarea>

                    <button class="bg-blue-600 text-white px-3 py-1 rounded w-full">
                        Submit
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
