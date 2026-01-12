<!DOCTYPE html>
<html>
<head>
    <title>College Tracking Login</title>
    <style>
        body { font-family: Arial; background:#f4f6f8; }
        .login-box {
            width: 420px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            border: none;
            cursor: pointer;
            color: white;
        }
        .btn-student { background:#2563eb; }
        .btn-teacher { background:#059669; }
        .btn-admin   { background:#7c3aed; }
        .error { color: red; margin-top: 10px; }
        .role-buttons button { margin-top: 8px; }
    </style>

    <script>
        function fillUSN(usn) {
            document.getElementById('usn').value = usn;
            document.getElementById('dob').focus();
        }
    </script>
</head>
<body>

<div class="login-box">
    <h2 style="margin-bottom:10px;">College Tracking System</h2>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <label>USN</label>
        <input type="text" id="usn" name="usn" required>

        <label>Date of Birth</label>
        <input type="date" id="dob" name="dob" required>

        <button type="submit" class="btn-student">Login</button>
    </form>

    <hr style="margin:20px 0;">

    <div class="role-buttons">
        <button class="btn-student" onclick="fillUSN('BBA1A001')">
            Student Login
        </button>

        <button class="btn-teacher" onclick="fillUSN('TCH001')">
            Teacher Login
        </button>

        <button class="btn-admin" onclick="fillUSN('ADMIN001')">
            Admin Login
        </button>
    </div>
</div>

</body>
</html>
