@extends('layouts.app')
@section('content')
<v-row>
	<v-col class="mx-4">
		<v-toolbar dense color="transparent" flat>
			<v-toolbar-title>User Roles</v-toolbar-title>
		 	<div class="flex-grow-1"></div>
			<v-toolbar-items>
				<v-btn  v-if="meta.create == 'true'" color="primary" dense depressed href="user_roles/add">Create</v-btn>
			</v-toolbar-items>
		</v-toolbar>
		<v-card class="mt-2">
			<v-card-text>
				<v-data-table 
					:headers="headers" 
					:items="items" 
					:loading="loading">
					<template v-slot:item.action="{item}">
						<v-tooltip bottom v-if="meta.edit == 'true'">
  							<template v-slot:activator="{ on }">
								<v-icon small @click="edit(item.id)" v-on="on">mdi-pencil</v-icon>
								</template>
							<span>Edit</span>
						</v-tooltip>
						<v-tooltip bottom v-if="meta.delete == 'true'">
  							<template v-slot:activator="{ on }">
								<v-icon small @click="deleteRole(item.id)" v-on="on">mdi-delete</v-icon>
								</template>
							<span>Delete</span>
						</v-tooltip>
					</template>
				</v-data-table>
			</v-card-text>
		</v-card>
	</v-col>
	<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
		@{{sbText}}
		<v-btn dark text @click="snackbar = false"> Close</v-btn>
	</v-snackbar>
</v-row>
@endsection
@section('script')
<script>
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				loading:false,
				headers:[
					{
						text:'ID',
						value:'id'
					},
					{
						text:'Name',
						value:'name'
					},
					{
						text:'Action',
						value:'action',
						sortable:false,
					},
				],
				items:[],
				meta:[],
            }
        },
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
            this.getDataFromApi()
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
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
			getDataFromApi(){
				this.loading = true
				axios.get('users_roles').then((response)=>{
					this.loading = false
					this.items = response.data.data
					this.meta = response.data.meta
				})
			},
			edit(id){
				window.location.href = '/admin/user_roles/edit/'+id
			},
			deleteRole(id){
				console.log(id)
				axios.delete('users_roles/'+id,{_method: 'DELETE'}).then((res)=>{
					this.triggerSb({text:'User Role is deleted from database',color:'info'})
					this.getDataFromApi()
				}).catch((error)=> {
					if(error.response.status == 403){
						this.triggerSb({
							text:'You are not authorised to do this action',
							color:'error'
						})
					}
				})
			}
		}
    }).$mount('#app')
</script>
@endsection