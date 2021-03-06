<!DOCTYPE html>
<!--[if IE 9]> <html class="no-js lt-ie10" lang="pt-BR"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="pt-BR"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ config('app.name', 'Bets') }} - Entrar no sistema</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(file_exists(public_path('css/theme.css')))
        <link rel="stylesheet" href="/css/theme.css">
    @else
        <link rel="stylesheet" href="/theme.css">
    @endif

    <!-- Stylesheets -->
    @if(config('app.env') !== 'production')
        <link href="/css/app.css" rel="stylesheet">
        <link rel="stylesheet" href="/css/plugins.css">
    @else
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/plugins.css') }}" rel="stylesheet">
    @endif

    @yield('styles')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app"></div>
    <!-- Login Container -->
    <div id="login-container">
        <!-- Login Header -->
        <h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
            @if(file_exists(public_path('storage/logo.jpeg')))
                <img width="220" src="/storage/logo.jpeg">
            @else
                <i class="fa fa-futbol-o"></i> <strong>Bem-vindo ao {{ config('app.name') }}</strong>
            @endif
        </h1>
        <!-- END Login Header -->

        <!-- Login Block -->
        <div class="block full bordered animation-fadeInQuickInv">
            <!-- Login Title -->
            <div class="block-title">
                <div class="block-options pull-right">
                    <a id="clean-data" href="javascript:" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Limpar dados">
                        <i class="fa fa-exclamation-circle"></i>
                    </a>
                </div>
                <h2>Por favor, faça login</h2>
            </div>
            <!-- END Login Title -->

            <!-- Login Form -->
            <form id="form-login" action="{{ url('/login') }}" method="post" class="form-horizontal">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }} p-tb p-lr">
                    <div class="col-xs-12">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Seu nome de usuário&hellip;" value="{{ old('username') }}" required autofocus>
                        @if ($errors->has('username'))
                            <div id="username-error" class="help-block animation-slideUp">{{ $errors->first('username') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} p-lr">
                    <div class="col-xs-12">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Sua senha&hellip;">
                        @if ($errors->has('password'))
                            <div id="password-error" class="help-block animation-slideUp">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group form-actions p-lr p-tb">
                    <div class="col-xs-6">
                        <label class="csscheckbox csscheckbox-primary">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span></span>
                            Lembrar-me?
                        </label>
                    </div>
                    <div class="col-xs-6 text-right">
                        <button type="submit" class="btn btn-effect-ripple btn-sm btn-primary">
                            <i class="fa fa-check"></i> Acessar
                        </button>
                    </div>
                </div>
            </form>
            <!-- END Login Form -->
        </div>
        <!-- END Login Block -->
    </div>
    <!-- END Login Container -->

    <!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
    @if(config('app.env') !== 'production')
        <script src="/js/app.js"></script>
        <script src="/js/plugins.js"></script>
        <script src="/js/theme.js"></script>
    @else
        <script src="{{ elixir('js/app.js') }}"></script>
        <script src="{{ elixir('js/plugins.js') }}"></script>
        <script src="{{ elixir('js/theme.js') }}"></script>
    @endif

    <!-- Load and execute javascript code used only in this page -->
    <script>
        const ReadyLogin = function() {
            return {
                init: function() {
                    /*
                     *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
                     */

                    /* Login form - Initialize Validation */
                    $('#form-login').validate({
                        errorClass: 'help-block animation-slideUp', // You can change the animation class for a different entrance animation - check animations page
                        errorElement: 'div',
                        errorPlacement: function(error, e) {
                            e.parents('.form-group > div').append(error);
                        },
                        highlight: function(e) {
                            $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                            $(e).closest('.help-block').remove();
                        },
                        success: function(e) {
                            e.closest('.form-group').removeClass('has-success has-error');
                            e.closest('.help-block').remove();
                        },
                        rules: {
                            'username': {
                                required: true
                            },
                            'password': {
                                required: true,
                                minlength: 3
                            }
                        },
                        messages: {
                            'username': 'Por favor, digite seu nome de usuário',
                            'password': {
                                required: 'Por favor, digite a sua senha',
                                minlength: 'Sua senha deve ter mais de 3 caracteres'
                            }
                        }
                    });
                }
            };
        }();

        $(function(){
          const form = $('#form-login'),
            inputUsername = form.find('#username'),
            inputPassword = form.find('#password'),
            checkRemember = form.find('#remember');

          if (typeof(Storage) !== "undefined" && localStorage.remember) {
            inputUsername.val(localStorage.username);
            inputPassword.val(localStorage.password);
            checkRemember.prop('checked', true);
          }

          // ReadyLogin.init();

          form.on('click', 'button', function (e) {
            e.preventDefault();
            let username = inputUsername.val();
            let password = inputPassword.val();

            if (typeof(Storage) !== "undefined" && checkRemember.is(':checked')) {
              localStorage.setItem('username', username);
              localStorage.setItem('password', password);
              localStorage.setItem('remember', true);
            }

            form.submit();
          });

          $('#clean-data').on('click', function () {
            if (typeof(Storage) !== "undefined") {
              localStorage.removeItem("username");
              localStorage.removeItem("password");
              localStorage.removeItem("remember");
              inputUsername.val('');
              inputPassword.val('');
              checkRemember.prop('checked', false);
            }
          });
        });
    </script>
</body>
</html>
