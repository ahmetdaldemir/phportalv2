<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="{{asset('auth/assets/css/styles.css')}}">

    <title>Phone Hospital</title>
</head>
<body>
<div class="container">
    <div class="login__content">
        <img src="{{asset('auth/assets/img/bg-login.png')}}" alt="login image" class="login__img">

        <form action="{{ route('login') }}" method="post" class="login__form">
            @csrf
            <div>
                <h1 class="login__title">
                    <span>Hoşgeldin</span> Yönetici
                </h1>
                <p class="login__description">
                    İşlem yapmak için giriş yapınız
                </p>
            </div>

            <div>
                <div class="login__inputs">
                    <div>
                        <label for="" class="login__label">Email</label>
                        <input id="email" type="email" class="login__input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div>
                        <label for="" class="login__label">Şifre</label>

                        <div class="login__box">
                            <input type="password" name="password" id="password" placeholder="Şifre" required class="login__input  @error('password') is-invalid @enderror" id="input-pass" required autocomplete="current-password">
                            <i class="ri-eye-off-line login__eye" id="input-icon"></i>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="login__check">
                    <input class="login__check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="" class="login__check-label">  {{ __('Beni Hatırla') }}</label>
                </div>
            </div>
            <div>
                <div class="login__buttons">
                    <button class="login__button">Giriş Yap</button>
                     @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Şifremi Unuttum?') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>


<!--=============== MAIN JS ===============-->
<script src="{{asset('auth/assets/js/main.js')}}"></script>
</body>
</html>
