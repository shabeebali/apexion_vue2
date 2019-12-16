@extends('layouts.app')
@section('content')
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Users</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn  v-if="meta.create == 'true'" color="primary" dense depressed href="users/add">Create 
						<v-icon right>mdi-account-plus</v-icon></v-btn>
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
									<v-icon small @click="editUser(item.id)" v-on="on">mdi-pencil</v-icon>
									</template>
								<span>Edit</span>
							</v-tooltip>
							<v-tooltip bottom v-if="meta.delete == 'true'">
      							<template v-slot:activator="{ on }">
									<v-icon small @click="deleteUser(item.id)" v-on="on">mdi-delete</v-icon>
									</template>
								<span>Delete</span>
							</v-tooltip>
						</template>
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
		<!--<user-create :openDialog="openDialog" :userId="user_id" :mode="mode" v-on:update-list="getDataFromApi" v-on:close-dialog="openDialog = false ; mode ='' ; user_id = 0" v-on:trigger-sb="triggerSb"></user-create>-->
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
				mode:'',
				user_id:0,
				snackbar:@if (session('success') || session('info') || session('error')) true @else false @endif,
				sbTimeout:3000,
				sbText: '{{session('success')}}',
				sbColor:@if (session('success')) 'success' @elseif(session('info')) 'info' @else 'error' @endif,
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
						text:'E-mail',
						value:'email'
					},
					{
						text:'Action',
						value:'action',
						sortable: false
					}
				],
				items:[],
				meta:[],
				openDialog:false,
				loading:false,
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
			getDataFromApi(){
				this.loading = true
				axios.get('users').then((response)=>{
					this.loading = false
					this.items = response.data.data
					this.meta = response.data.meta
				})
			},
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
			editUser(val){
				window.location.href= '/admin/users/edit/'+val
			},
			deleteUser(id){
				axios.delete('users/'+id).then((res)=>{
					this.triggerSb({text:'User is deleted from database',color:'info'})
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