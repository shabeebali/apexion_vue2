<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Taxonomies</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn color="primary" dense depressed @click="mode = 'create'; dialog = true">Create</v-btn>
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
									<v-icon small @click="deleteConfirm(item.id)" v-on="on">mdi-delete</v-icon>
									</template>
								<span>Delete</span>
							</v-tooltip>
						</template>
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
		<taxonomy-create :dialog="dialog" :mode="mode" :txId="txId" v-on:trigger-sb="triggerSb" v-on:close-dialog="mode= ''; txId = 0; dialog = false" v-on:update-list="getDataFromApi"></taxonomy-create>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
		<v-dialog v-model="confirmDialog" persistent width="500">
			<v-card>
				<v-card-title>
					Warning
				</v-card-title>
				<v-card-text>
					<v-alert dark color="error">
					Do you really Want to continue?. All the categories values under this taxonomy will be gone.</v-alert>
				</v-card-text>
				<v-card-actions>
					<v-btn text color="error" @click="deleteTx">Yes</v-btn>
					<v-btn text color="success" @click="confirmDialog = false">No</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-row>
</template>
<script>
	import TaxonomyCreate from './TaxonomyCreate.vue'
	export default{
		components:{
			TaxonomyCreate
		},
		data(){
			return{
				confirmDialog:false,
				delete_id : 0,
				mode:'',
				txId : 0,
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
				items:[],
				meta:[],
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
				axios.get('taxonomies').then((response)=>{
					this.loading = false
					this.items = response.data.data
					this.meta = response.data.meta
				})
			},
			edit(id){
				this.txId = id
				this.mode = 'edit'
				this.dialog = true
			},
			deleteConfirm(id){
				this.delete_id = id
				this.confirmDialog = true
			},
			deleteTx(){
				this.confirmDialog = false
				axios.delete('taxonomies/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
					this.triggerSb({text:'Taxonomy is deleted from database',color:'info'})
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