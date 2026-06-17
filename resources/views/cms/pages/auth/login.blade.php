<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{asset('cms/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('cms/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('cms/dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{asset('cms/plugins/toastr/toastr.min.css')}}">
</head>

<body class="hold-transition login-page">

<div class="login-box">

  <div class="login-logo">
    <a href="#"><b>Admin</b>LTE</a>
  </div>

  <div class="card">
    <div class="card-body login-card-body">

      <p class="login-box-msg">Sign in to start your session</p>

      <form>

        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" id="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" id="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="row">

          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">Remember Me</label>
            </div>
          </div>

          <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" onclick="login()">
              Sign In
            </button>
          </div>

        </div>

      </form>

      <p class="mb-1">
        <a href="#">I forgot my password</a>
      </p>

      <p class="mb-0">
        <a href="{{route('auth.register')}}" class="text-center">Register a new membership</a>
      </p>

    </div>
  </div>

</div>

<script src="{{asset('cms/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('cms/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('cms/dist/js/adminlte.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{asset('cms/plugins/toastr/toastr.min.js')}}"></script>

<script>

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: "3000"
};

function login(){

  axios.post('{{route('login',['guard' => $guard])}}', {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        remember: document.getElementById('remember').checked
    })
    .then(function(response){
        toastr.success(response.data.message);
        window.location.href = '/cms/admins';
    })
    .catch(function(error){
        toastr.error(error.response?.data?.message || "Login failed");
    });

}

</script>

</body>
</html>