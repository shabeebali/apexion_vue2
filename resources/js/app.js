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
import Vuex from 'vuex'
Vue.use(Vuex)
Vue.use(VueRouter)
const router = new VueRouter({
  routes,
  base:'/admin/',
  mode:'history',
});
const store = new Vuex.Store({
  state: {
    user: {
      id:0
    },
    permissions:[],
  },
  getters:{
    hasPermission:(state)=>(q)=>{
      const index = state.permissions.findIndex((p)=>{
        if(p.name == q) return true
      })
      return index == -1 ? false : true
    }
  },
  mutations: {
    setUser (state,user) {
      state.user = user
      var permissions = []
      user.roles.forEach((role)=>{
        role.permissions.forEach((permission)=>{
          permissions.push(permission)
        })
      })
      state.permissions = permissions
    }
  }
})
import vuetify from './vuetify'
new Vue({
  delimiters: ['${', '}$'],
  vuetify,
  store,
  router:router,
  data(){
  	return{
  		'sidebar_left':false,
  		'sidebar_left_items':[
        
      ],
  	}
  },
  mounted(){
    if(this.$router.currentRoute.path != '/login'){
      axios.get('menu').then((res)=>{
        this.sidebar_left_items = res.data
      })
      axios.get('user?with_permissions=1').then((res)=>{
        this.$store.commit('setUser',res.data)
        Echo.private('App.User.'+this.$store.state.user.id)
          .notification((notification) => {
              this.notify_message = notification.message
              this.notify = true
          });
      })
    }
  },
  computed:{
    baseUrl(){
      return window.base_url.content
    },
  }
}).$mount('#app')
