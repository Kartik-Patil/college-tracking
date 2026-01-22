<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-2xl font-bold mb-4">Edit Student</h1>

@if(session('success'))
<div class="bg-green-200 p-3 mb-4 rounded">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.student.update', $student->student_id) }}"
      class="bg-white p-4 rounded shadow w-96">
@csrf

<label>USN</label>
<input value="{{ $student->usn }}" disabled class="border p-2 w-full mb-2">

<label>First Name</label>
<input name="first_name" value="{{ $student->first_name }}" class="border p-2 w-full mb-2">

<label>Last Name</label>
<input name="last_name" value="{{ $student->last_name }}" class="border p-2 w-full mb-2">

<label>DOB</label>
<input type="date" name="dob" value="{{ $student->dob }}" class="border p-2 w-full mb-4">

<button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
Save Changes
</button>
</form>

<form method="POST" action="{{ route('admin.student.toggle', $student->student_id) }}"
      class="mt-4">
@csrf
<button class="w-96 px-4 py-2 rounded text-white
{{ $student->is_active ? 'bg-red-600' : 'bg-green-600' }}">
{{ $student->is_active ? 'Deactivate Student' : 'Activate Student' }}
</button>
</form>

</body>
</html>
