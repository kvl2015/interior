@extends('site')

@section('content')
<div class="container">
    @include('partials.title',['title' => __('Reset Password')])
    <div class="row">
        <div class="col-lg-5">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                @include('form.fields.text-input',['name' => 'email', 'label' => __('form.email'), 'value' => old('email'), 'type' => 'email', 'required' => true])
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <button type="submit" class="btn-yellow" id="btnResetLink" value="{{ __('form.resetlink_btn') }}">{{ __('form.resetlink_btn') }}</button>

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

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </div>-->
            </form>
        </div>
    </div>
</div>
@endsection
