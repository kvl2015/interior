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

                <button type="submit" class="btn btn-info hvr-sweep-to-top" id="btnLogin" value="{{ __('form.login_btn') }}">{{ __('form.login_btn') }}</button>
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
        <div class="col-lg-2">
            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button>
        </div>
    </div>
   
</div>
@endsection

@section ('javascript-facebook')
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '320112832331922',
      cookie     : true,
      xfbml      : true,
      version    : 'v7.0'
    });
      
    /*FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });*/
    //FB.AppEvents.logPageView(); 
  };

  
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
@endsection

@section ('javascript')
<script>
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    function testAPI2() {
        FB.api('/me', { fields: 'id,name' },
            function (response) {
                var user_id = response.id;//name
                var name = response.name;
                var type = 1;
                /*$.ajax({
                    type: 'post',
                    url: "services/services.php",
                    //dataType : 'json',
                    data: {
                        type: 'facebookLogin',
                        facebookId: user_id
                    },
                    async: true,
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        setTimeout(function () {
                        }, 1000);
                    },
                    error: function (data) {
                        console.log(data);
                        console.log("An error occurred");
                    }
                });*/
            }
        );
    }    

    function statusChangeCallback(response) {
        console.log(statusChangeCallback);
        if (accessToken) {
                var accessToken = response.authResponse.accessToken;
            }
            if (response.status === 'connected') {
                testAPI2(accessToken);
            } else if (response.status === 'not_authorized') {
                console.log("Err");
            } else {
                console.log("Err");
            }        
    }

    $(document).ready(function(){
        /*FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });*/
    })
</script>
@endsection