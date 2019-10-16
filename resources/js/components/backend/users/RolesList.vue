<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>User Roles</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn color="primary" dense depressed @click="dialog = true">Create</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			<v-card class="mt-2">
				<v-card-text>
					<v-data-table 
						:headers="headers" 
						:items="items" 
						:loading="loading">
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
		<role-create :dialog="dialog" v-on:trigger-sb="triggerSb" v-on:close-dialog="dialog = false" v-on:update-list="getDataFromApi"></role-create>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-row>
</template>
<script>
	import RoleCreate from './RoleCreate.vue'
	export default{
		components:{
			RoleCreate
		},
		data(){
			return{
				mode:'',
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				dialog:false,
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
				items:[]
			}
		},
		mounted(){
			this.loading = true
			this.getDataFromApi()
		},
		methods:{
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
			getDataFromApi(){
				axios.get('users_roles').then((response)=>{
					this.loading = false
					this.items = response.data
				})
			},
			resetCreateForm(){
				Object.keys(this.nrfd).forEach((key)=>{
					this.nrfd[key].value = ''
				})
			},
			saveNewRole(){
				this.nrslb = true
				var fD = new FormData()
				Object.keys(this.nrfd).forEach((key)=>{
					fD.append(key,this.nrfd[key].value)
				})
				axios.post('users_roles',fD).then((response)=>{
					this.nrslb = false
					this.createDialog = false
					this.resetCreateForm()
					this.snackbar = false
					this.sbText = 'User Created Successfully'
					this.sbColor = 'success'
					this.snackbar = true
					this.loading = true
					this.getDataFromApi()
				}).catch((error)=> {
					this.nrslb = false
					var errors = error.response.data.errors
					Object.keys(errors).forEach((key)=>{
						this.nrfd[key].error = errors[key]
					})
					this.snackbar = false
					this.sbText = 'There is(are) error(s) in the form submitted. Please check!!'
					this.sbColor = 'error'
					this.snackbar = true
				})
			}
		}
	}
</script>