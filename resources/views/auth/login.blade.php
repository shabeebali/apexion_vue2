@extends('layouts.app')

@section('content')
<v-row>
    <v-col cols="12" md="4"></v-col>
    <v-col cols="12" md="4">
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
                    @endif
                </v-card-actions>
            </form>
        </v-card>
    </v-col>
    <v-col cols="12" md="4"></v-col>
</v-row>
@endsection
