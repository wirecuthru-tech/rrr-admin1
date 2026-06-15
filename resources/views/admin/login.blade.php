<!DOCTYPE html>
<html>
<head>
    <title>RRR Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:linear-gradient(135deg,#111827,#1e3a8a);height:100vh;display:flex;align-items:center;justify-content:center;">

<div class="card shadow" style="width:380px;border-radius:18px;">
    <div class="card-body p-4">

        <h3 class="text-center mb-1">RRR CHAT</h3>
        <p class="text-center text-muted mb-4">Owner Admin Login</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">
                Login
            </button>
        </form>

    </div>
</div>

</body>
</html>