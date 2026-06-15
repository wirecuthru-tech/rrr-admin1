<!DOCTYPE html>
<html>
<head>
    <title>RRR Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f7fe">

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-md-4">

            <div class="card shadow">

                <div class="card-body">

                    <h3 class="text-center mb-4">
                        RRR Admin Login
                    </h3>

                    <form method="POST" action="/login">

                        @csrf

                        <div class="mb-3">
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Email">
                        </div>

                        <div class="mb-3">
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Password">
                        </div>

                        <button class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>