@extends('layouts.app')

@section('content')
<h1 class="display-1">Content coming soon</h1>
@endsection
@section('script')
<script>
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                }),
            ]).then(()=>{
                this.waitDialog = false
            })
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },       
        },
    }).$mount('#app')
</script>
@endsection
