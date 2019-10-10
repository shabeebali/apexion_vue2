/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

<<<<<<< HEAD
Vue.component('app-component', require('./components/AppComponent.vue').default);
=======
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
>>>>>>> 107eb19a9e088a73f123e976eee2b4a80d5da5b6

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
<<<<<<< HEAD
import vuetify from './vuetify'
new Vue({
  vuetify,
}).$mount('#app')
=======

const app = new Vue({
    el: '#app',
});
>>>>>>> 107eb19a9e088a73f123e976eee2b4a80d5da5b6
