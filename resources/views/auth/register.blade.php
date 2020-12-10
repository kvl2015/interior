@extends('site')

@section('content')
<div class="container">
    @include('partials.title',['title' => __('Register')])
    @php //dd($errors) @endphp
    <div class="row">
        <div class="col-lg-5">
            <div class="">
            @if (count($errors))
    <ul>
        @foreach($errors as $error)       
            <li>{!! $error !!}</li>
        @endforeach
    </ul>
@endif
            </div>
            <form method="POST" id="register-form">
                @csrf

                @include('form.fields.text-input',['name' => 'name', 'label' => __('form.name'), 'value' => old('name'), 'type' => 'text', 'required' => true])

                @include('form.fields.text-input',['name' => 'email', 'label' => __('form.email'), 'value' => old('email'), 'type' => 'email', 'required' => true])
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>!!!{{ $message }}</strong>
                    </span>
                @enderror

                @include('form.fields.text-input',['name' => 'password', 'label' => __('form.password'), 'value' => '', 'type' => 'password', 'required' => true])
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                @include('form.fields.text-input',['name' => 'password_confirmation', 'label' => __('form.password-confirm'), 'value' => '', 'type' => 'password', 'required' => true])

                <button type="submit" class="btn-yellow" value="{{ __('form.regist_btn') }}">{{ __('form.regist_btn') }}</button>

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
