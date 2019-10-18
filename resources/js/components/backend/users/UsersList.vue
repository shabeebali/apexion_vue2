<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Users</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn color="primary" dense depressed @click="mode='create';openDialog = true">Create 
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
		<user-create :openDialog="openDialog" :userId="user_id" :mode="mode" v-on:update-list="getDataFromApi" v-on:close-dialog="openDialog = false ; mode ='' ; user_id = 0" v-on:trigger-sb="triggerSb"></user-create>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-row>
</template>
<script>
	import userCreate from './UserCreate.vue'
	export default{
		components:{
			userCreate
		},
		data(){
			return{
				mode:'',
				user_id:0,
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
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
			this.getDataFromApi()
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
				this.user_id = val
				this.mode="edit"
				this.openDialog = true
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
	}
</script>