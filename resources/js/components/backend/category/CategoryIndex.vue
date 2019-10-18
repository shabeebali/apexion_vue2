<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Categories</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn v-on="on" color="transparent" dense depressed @click="exportData">
								<v-icon>mdi-download</v-icon>
							</v-btn>
						</template>
						<span>Export</span>
					</v-tooltip>
					<v-btn color="transparent" class="mr-2" dense depressed @click="getDataFromApi">
						<v-icon>mdi-refresh</v-icon>
					</v-btn>
					<v-btn color="primary" dense depressed @click="mode = 'create'; dialog = true">Create</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			<v-card class="mt-2">
				<v-card-text>
					<v-data-table 
						:headers="headers" 
						:items="items" 
						:loading="loading"
						:options.sync="options"
						:server-items-length="totalItems"
						:footer-props="{
							itemsPerPageOptions:[10,20,50,100]
						}">
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
		<category-create :dialog="dialog" :mode="mode" :catId="catId" v-on:trigger-sb="triggerSb" v-on:close-dialog="mode= ''; txId = 0; dialog = false" v-on:update-list="getDataFromApi"></category-create>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
		<v-dialog v-model="waitDialog" persistent width="300">
			<v-card color="primary" dark>
				<v-card-text>
					Please stand by
					<v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
				</v-card-text>
			</v-card>
	    </v-dialog>
		<v-dialog v-model="confirmDialog" persistent width="500">
			<v-card>
				<v-card-title>
					Warning
				</v-card-title>
				<v-card-text>
					<v-alert dark color="error">
					Do you really Want to continue?</v-alert>
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
	import CategoryCreate from './CategoryCreate.vue'
	export default{
		components:{
			CategoryCreate
		},
		watch:{
			options: {
	        handler () {
	          this.getDataFromApi()
	        },
	        deep: true,
	      }
		},
		data(){
			return{
				confirmDialog:false,
				waitDialog:false,
				delete_id : 0,
				mode:'',
				catId : 0,
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				dialog:false,
				loading:false,
				options:{},
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
				totalItems:0,
				meta:[],
			}
		},
		mounted(){
			this.getDataFromApi()
		},
		methods:{
			exportData(){
				this.waitDialog = true
				axios.get('categories/export').then((res)=>{
					this.waitDialog = false
					window.location = res.data
				})
			},
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
			getDataFromApi(){
				this.loading = true
				const { sortBy, sortDesc, page, itemsPerPage } = this.options
				var params = '?page='+page+'&rpp='+itemsPerPage+'&'
				if(sortBy.length == 1){
					params = params+'sortby='+sortBy[0]+'&'
				}
				if(sortDesc.length == 1 && sortDesc[0] == true){
					params = params+'descending=1&'
				}
				axios.get('categories'+params).then((response)=>{
					this.loading = false
					this.items = response.data.data
					this.meta = response.data.meta
					this.totalItems = response.data.total
				})
			},
			edit(id){
				this.catId = id
				this.mode = 'edit'
				this.dialog = true
			},
			deleteConfirm(id){
				this.delete_id = id
				this.confirmDialog = true
			},
			deleteTx(){
				this.confirmDialog = false
				axios.delete('categories/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
					this.triggerSb({text:'Category is deleted from database',color:'info'})
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