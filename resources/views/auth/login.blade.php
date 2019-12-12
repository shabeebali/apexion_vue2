@extends('layouts.app')

@section('content')
<v-row>
    <v-col cols="12" md="4"></v-col>
    <v-col cols="12" md="4">
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
                    @endif
                </v-card-actions>
            </form>
        </v-card>
    </v-col>
    <v-col cols="12" md="4"></v-col>
</v-row>
<script>
    new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
            }
        },
        mounted(){
            //axios.get('menu').then((res)=>{
            //    this.sidebar_left_items = res.data
            //})
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
    }).$mount('#app')
</script>
@endsection
