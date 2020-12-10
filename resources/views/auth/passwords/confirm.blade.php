@extends('site')

@section('content')
<div class="container">
    @include('partials.title',['title' => __('Confirm Password')])
    <div class="row">
        <div class="col-lg-5">
            {{ __('Please confirm your password before continuing.') }}
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                @include('form.fields.text-input',['name' => 'password', 'label' => __('form.password'), 'value' => '', 'type' => 'password', 'required' => true])
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <!--<div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    </div>
                </div>-->

                <button type="submit" class="btn_yellow" id="btnConfirm" value="{{ __('form.confirm_btn') }}">{{ __('form.confirm_btn') }}</button>
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif                
                <!--<div class="form-group row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Confirm Password') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </div>-->
            </form>

        </div>
    </div>
</div>
@endsection
