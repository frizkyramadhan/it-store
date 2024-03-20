<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $title }} - Arkananta IT Store</title>

  <!-- Bootstrap -->
  <link href="{{ asset('assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <!-- NProgress -->
  <link href="{{ asset('assets/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
  <!-- Animate.css -->
  <link href="{{ asset('assets/vendors/animate.css/animate.min.css') }}" rel="stylesheet">

  <!-- Custom Theme Style -->
  <link href="{{ asset('assets/build/css/custom.min.css') }}" rel="stylesheet">
</head>

<body class="login">
  <div>
    <div class="login_wrapper">
      <div>
        <section class="login_content">
          <form data-parsley-validate action="{{ url('login') }}" method="post" autocomplete="on">
            @csrf
            <h1>Login Form</h1>
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ session('success') }}
            </div>
            @endif
            @if (session('errors'))
            <div class="alert alert-danger alert-dismissible fade in" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
              </button>
              {{ session('errors') }}
            </div>
            @endif
            <div>
              <input type="email" name="email" class="form-control" placeholder="Email Address" required autofocus />
            </div>
            <div>
              <input type="password" name="password" class="form-control" placeholder="Password" required />
            </div>
            <div>
              <button type="submit" class="btn btn-default">Log in</button>
            </div>

            <div class="clearfix"></div>

            <div class="separator">
              <p class="change_link">New to site?
                <a href="{{ url('register') }}" class="to_register"> Create Account </a>
              </p>

              <div class="clearfix"></div>
              <br />

              <div>
                <h1><img src="{{ asset('assets/images/logo3.png') }}" alt="Logo" width="30%"> IT Store</h1>
                <p>©2024 All Rights Reserved.</p>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="{{ asset('assets/vendors/jquery/dist/jquery.min.js') }}"></script>
  <!-- Bootstrap -->
  <script src="{{ asset('assets/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <!-- Parsley -->
  <script src="{{ asset('assets/vendors/parsleyjs/dist/parsley.min.js') }}"></script>
</body>
</html>
