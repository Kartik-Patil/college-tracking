<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

<h2 style="text-align:center;">GLOBAL BUSINESS SCHOOL</h2>
<p style="text-align:center;">OFFICIAL MARKS CARD</p>

<p>
<b>Name:</b> {{ $student->first_name }} {{ $student->last_name }}<br>
<b>USN:</b> {{ $student->usn }}
</p>

<table>
    <tr>
        <th>Subject</th>
        <th>Assessment</th>
        <th>Marks</th>
    </tr>

    @foreach($marks as $m)
    <tr>
        <td>{{ $m->subject_name }}</td>
        <td>{{ $m->assessment_name }}</td>
        <td>{{ $m->marks_obtained }} / {{ $m->max_marks }}</td>
    </tr>
    @endforeach
</table>

<p style="margin-top:40px;">
<b>Controller of Examinations</b><br>
Global Business School
</p>

</body>
</html>
