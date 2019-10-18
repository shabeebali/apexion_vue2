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

Vue.component('app-component', require('./components/AppComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import VueRouter from 'vue-router'
import {routes} from './router.js'
Vue.use(VueRouter)
const router = new VueRouter({
  routes,
  base:'/admin/',
  mode:'history',
});
import vuetify from './vuetify'
new Vue({
  delimiters: ['${', '}$'],
  vuetify,
  router:router,
  data(){
  	return{
  		'sidebar_left':false,
  		'sidebar_left_items':[
        {
          title:'Dashboard',
          target:'/',
          icon:'mdi-gauge'
        },
        {
          title:'Products',
          target:'/products',
          icon:'mdi-inbox-multiple',

        },
        {
          title:'Categories',
          target:'/categories',
          icon:'mdi-book-variant',
          children:[
            {
              title:'Categories',
              target:'/categories',
            },
            {
              title:'Taxonomy',
              target:'/taxonomies',
            },
          ]
        },
        {
          title:'Inventory',
          target:'/inventory',
          icon:'mdi-warehouse',
          children:[
            {
              title:'Inventory',
              target:'/inventory',
            },
            {
              title:'Warehouse',
              target:'/warehouses',
            },
          ]
        },
        {
          title:'Sales',
          target:'/sales',
          icon:'mdi-printer-pos',
          children:[
            {
              title:'Sales',
              target:'/sales',
            },
            {
              title:'Pricelist',
              target:'/pricelists',
            },
          ]
        },
        {
          title:'Users',
          target:'/users',
          icon:'mdi-account',
          children:[
            {
              title:'Users',
              target:'/users',
            },
            {
              title:'Roles',
              target:'/users/roles',
            },
          ]
        },
      ],
  	}
  },
  mounted(){
  }
}).$mount('#app')
