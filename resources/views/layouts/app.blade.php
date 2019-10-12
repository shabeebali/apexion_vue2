<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <v-app>
            <v-app-bar dense max-height="48" color="deep-purple accent-4" dark>
                <v-app-bar-nav-icon v-if="sidebar_left_items.length > 0"></v-app-bar-nav-icon>
                <v-toolbar-title>
                    <v-btn dark text link depressed href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </v-btn>
                </v-toolbar-title>
                <div class="flex-grow-1"></div>
                
                @guest
                    <v-toolbar-items>
                        <v-btn text href="{{ route('login') }}">{{ __('Login') }}</v-btn>
                        @if (Route::has('register'))
                            <v-btn text href="{{ route('register') }}">{{ __('Register') }}</v-btn>
                        @endif
                    </v-toolbar-items>
                @else
                    <v-btn text>Welcome {{ Auth::user()->name }}</v-btn>
                    <v-menu left bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on">
                                <v-icon>fa-ellipsis-v</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item>
                                <v-list-item-title onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();" href="{{ route('logout') }}">{{ __('Logout') }}</v-list-item-title>
                            </v-list-item>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </v-list>
                    </v-menu>
                @endguest
            </v-app-bar>
            <v-content>
                <v-container fluid class="pa-0">
                    @yield('content')
                </v-container>
            </v-content>
        </v-app>
    </div>
</body>
</html>
