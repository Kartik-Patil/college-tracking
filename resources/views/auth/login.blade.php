<!DOCTYPE html>
<html>
<head>
    <title>College Tracking Login</title>
    <style>
        body { font-family: Arial; background:#f4f6f8; }
        .login-box {
            width: 400px;
            margin: 120px auto;
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
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <label>USN</label>
        <input type="text" name="usn" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
