@extends('layouts.app')

@section('content')
<v-dialog v-model="waitDialog" persistent width="300">
	<v-card color="primary" dark>
		<v-card-text>
			Please stand by
			<v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
		</v-card-text>
	</v-card>
</v-dialog>
@endsection

@section('script')
<script>
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                waitDialog:false,
            }
        },
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
            var message = window.localStorage.getItem('message')
			var message_status = window.localStorage.getItem('message_status')
			if(message && message_status){
				this.triggerSb({text:message,color:message_status})
				window.localStorage.removeItem('message')
				window.localStorage.removeItem('message_status')
			}
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
        	emitSb(text,color){
                window.localStorage.setItem('message',text)
                window.localStorage.setItem('message_status',color)
            },
        }
    }).$mount('#app')
</script>
@endsection
v-on:submit.prevent=""