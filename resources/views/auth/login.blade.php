@extends('site')

@section('content')
<div class="container">
    @include('partials.title',['title' => __('Login')])
    <div class="row">
        <div class="col-lg-5">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @include('form.fields.text-input',['name' => 'email', 'label' => __('form.email'), 'value' => old('email'), 'type' => 'email', 'required' => true])
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                @include('form.fields.text-input',['name' => 'password', 'label' => __('form.password'), 'value' => '', 'type' => 'password', 'required' => true])
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <button type="submit" class="btn-yellow" id="btnLogin" value="{{ __('form.login_btn') }}">{{ __('form.login_btn') }}</button>
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif


                <!--<div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>

                        
                    </div>
                </div>-->
            </form>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <a href="{{url('/facebook/redirect')}}" class="btn btn-facebook"><i class="fab fa-facebook-f"></i> Signin with Facebook</a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <a href="{{url('/facebook/redirect')}}" class="btn btn-google"><i class="fab fa-google"></i> Signin with Google</a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    <a href="{{url('/facebook/redirect')}}" class="btn btn-twitter"><i class="fab fa-twitter"></i> Signin with Twitter</a>
                </div>
            </div>

        </div>
    </div>
   
</div>
@endsection
