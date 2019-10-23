<template>
	<div>
		<router-view></router-view>
		<v-snackbar v-model="notify" color="teal darken-4" top right>{{notify_message}}<v-btn
        dark text @click="notify = false" > Close </v-btn></v-snackbar>
	</div>
</template>

<script>
    export default {
    	data(){
    		return{
    			notify:false,
    			notify_message:'',
    		}
    	},
        mounted() {

            Echo.private('App.User.1')
		    .notification((notification) => {
		        this.notify_message = notification.message
		        this.notify = true
		    });
        }
    }
</script>
