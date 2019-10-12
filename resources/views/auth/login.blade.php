@extends('layouts.app')

@section('content')
<v-row>
    <v-col cols="12" md="4"></v-col>
    <v-col cols="12" md="4">
<<<<<<< HEAD
        <v-card class="mx-auto" max-width="344" outlined>
            <v-card-title>{{ __('Login') }}</v-card-title>
            <form method="POST" action="{{ route('login') }}">
                <v-card-text>
                    
                    @csrf
                    <v-text-field id="email" name="email" label="{{ __('E-Mail Address') }}" required autofocus @error('email') error @enderror error-messages="@error('email') {{ $message }} @enderror"></v-text-field>

                    <v-text-field id="password" name="password" label="{{ __('Password') }}" required type="password" @error('password') error @enderror error-messages="@error('password') {{ $message }} @enderror"></v-text-field>
                    <v-checkbox label="{{ __('Remember Me') }}" name="remember" id="remember"></v-checkbox>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit">{{ __('Login') }}</v-btn>
                    @if (Route::has('password.request'))
                        <a class="" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
=======
        <v-card>
            <v-card-title>{{ __('Login') }}</v-card-title>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <v-card-text>
                    <v-text-field required autofocus name="email" id="email" label="{{ __('E-Mail Address') }}" @error('email') error @enderror error-messages=" @error('email') {{$message}} @enderror"></v-text-field>
                    <v-text-field name="password" type="password" id="password" label="{{ __('Password') }}" @error('password') error @enderror error-messages=" @error('password') {{$message}} @enderror"></v-text-field>
                    <v-checkbox name="remember" id="remember" label="{{ __('Remember Me') }}"></v-checkbox>
                </v-card-text>
                <v-card-actions>
                    <v-btn text type="submit" color="primary">{{ __('Login') }}</v-btn>
                    @if (Route::has('password.request'))
                        <v-btn  href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </v-btn>
>>>>>>> origin/master
                    @endif
                </v-card-actions>
            </form>
        </v-card>
    </v-col>
    <v-col cols="12" md="4"></v-col>
</v-row>
@endsection
