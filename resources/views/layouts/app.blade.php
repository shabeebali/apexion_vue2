<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url('/') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/vuetify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/vuetify.min.js') }}"></script>
    <script src="{{ asset('js/lodash.min.js') }}"></script>
</head>
<body>
    <script>
        window.axios = axios;

        window.base_url = document.head.querySelector('meta[name="base-url"]');
        window.axios.defaults.baseURL = window.base_url.content+'/api';

        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.interceptors.response.use((response)=> {
            return response;
        },(error)=> {
            if (401 === error.response.status) {
                window.location.reload(true)
            } else {
                return Promise.reject(error);
            }
        });
    </script>
    <div id="app">
        <v-app>
            <v-app-bar absolute app color="transparent" flat>
                <v-app-bar-nav-icon  @click="sidebar_left = !sidebar_left"></v-app-bar-nav-icon>
                <v-toolbar-title>
                    <h3>@yield('page-title')</h3>
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
                    <div>
                        @yield('top-menu1')
                    </div>
                    <v-menu left bottom offset-y min-width="200">
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on">
                                <v-icon>mdi-account-circle</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item href="/admin/profile">
                                <v-list-item-title >Profile</v-list-item-title>
                            </v-list-item>
                            <v-list-item onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();" href="{{ route('logout') }}">
                                <v-list-item-title >{{ __('Logout') }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                @endguest
            </v-app-bar>
            <v-navigation-drawer app color="blue darken-4" dark  width="250" v-model="sidebar_left" >
                <template v-slot:img="attrs">
                    <v-img v-bind="attrs" gradient="to top, rgba(0, 0, 0, .7), rgba(0, 0, 0, .7)"/>
                </template>
                <v-img contain max-width="200" class="mx-auto mt-3" :src="baseUrl+'/images/apexion_logo.svg'"></v-img>
                <v-list class="mt-4" nav dense color="blue darken-4">
                    
                    <template v-for="(item,index) in sidebar_left_items">
                        <v-list-group dark no-action subgroup v-if="item.children" color="#fdfdfd">
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title>@{{item.title}}</v-list-item-title>
                                </v-list-item-content>
                            </template>
                            <v-icon slot="prependIcon">@{{item.icon}}</v-icon>
                            <v-list-item dark exact v-for="(it,index) in item.children" :key="index" :href="'/admin'+it.target">
                                <v-list-item-content>
                                    <v-list-item-title>@{{it.title}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>
                        <v-list-item dark exact v-else :href="item.target">
                            <v-list-item-icon>
                                <v-icon>@{{item.icon}}</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>@{{item.title}}</v-list-item-title>
                        </v-list-item>
                    </template>
                </v-list>
            </v-navigation-drawer>
            <v-content>
                <v-container fluid class="pa-0">
                    <main>
                        @yield('content')
                    </main>
                </v-container>
            </v-content>
        </v-app>
    </div>
    @yield('script')
    <script src="{{ asset('js/theme.js') }}" defer></script>
</body>
</html>
