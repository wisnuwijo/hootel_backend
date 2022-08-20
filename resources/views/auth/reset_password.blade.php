<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body style="background-color:#ececec">
    <div class="container" style="">
        <div class="row">
            <div class="col-md-2 col-sm-1"></div>
            <div class="col-md-8 col-sm-10">
                <div class="card" style="background-color: #fff;margin-top:20px;">
                    <div class="card-body">
                        <form action="{{ url('/reset_password') }}" method="post">
                            @csrf
                            <h3>Atur ulang kata sandi</h3>

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="passwordRetype" class="form-label">Retype Password</label>
                                <input type="password" class="form-control" id="passwordRetype" name="password_retyped" required>
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="submit" class="btn btn-md btn-primary" value="Save" />
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-1"></div>
        </div>
    </div>
</body>
</html>