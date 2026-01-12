<!DOCTYPE html>
<html>
<head>
    <title>Document Approvals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

<h1 class="text-2xl font-bold mb-4">Pending Documents</h1>

@if(session('success'))
<div class="bg-green-200 p-3 mb-4 rounded">{{ session('success') }}</div>
@endif

<table class="w-full bg-white border">
    <tr class="bg-gray-200">
        <th class="border p-2">USN</th>
        <th class="border p-2">Type</th>
        <th class="border p-2">Version</th>
        <th class="border p-2">Action</th>
    </tr>

    @foreach($documents as $d)
    <tr>
        <td class="border p-2">{{ $d->usn }}</td>
        <td class="border p-2">{{ $d->document_type }}</td>
        <td class="border p-2">{{ $d->current_version }}</td>
        <td class="border p-2">
            <a href="{{ route('admin.document.approve', $d->document_id) }}"
               class="bg-blue-600 text-white px-3 py-1 rounded">
               Approve
            </a>
        </td>
    </tr>
    @endforeach
</table>

</body>
</html>
