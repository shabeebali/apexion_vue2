<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Categories</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn v-on="on" color="transparent" dense depressed @click="filterToggle">
								<v-icon>mdi-filter</v-icon>
							</v-btn>
						</template>
						<span>Filter</span>
					</v-tooltip>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn v-on="on" color="transparent" dense depressed @click="importDialog = true">
								<v-icon>mdi-upload</v-icon>
							</v-btn>
						</template>
						<span>Import</span>
					</v-tooltip>
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
			<v-expansion-panels class="mt-2" v-model="filterPanel">
				<v-expansion-panel>
					<v-expansion-panel-content>
						<v-card flat>
							<v-toolbar flat>
								<v-toolbar-title  class="subtitle-1">Filter Options</v-toolbar-title>
								<div class="flex-grow-1"></div>
								<v-toolbar-items>
									<v-btn text @click="$refs.filterForm.reset(); getDataFromApi()">Reset</v-btn>
									<v-btn text color="primary" @click="getDataFromApi">Apply</v-btn>
								</v-toolbar-items>
					        </v-toolbar>
							<v-card-text>
								<v-row>
									<v-col cols="12" md="4">
										<v-form ref="filterForm">
											<v-select
												label="taxonomy"
												multiple
												v-model="filterdata.taxonomy"
												:items="filterables.taxonomy"
												item-text="name"
												item-value="id"
												></v-select>
										</v-form>
									</v-col>
									<v-col cols="12" md="4"></v-col>
									<v-col cols="12" md="4"></v-col>
								</v-row>
							</v-card-text>
						</v-card>
					</v-expansion-panel-content>
				</v-expansion-panel>
			</v-expansion-panels>
			<v-card class="mt-2">
		    <v-card-title>
				<v-text-field
					v-model="search"
					append-icon="mdi-magnify"
					label="Search"
					single-line
					hide-details
					></v-text-field>
		    </v-card-title>
				<v-card-text>
					<div class="text-center">
						<template v-for="(item,index) in meta.filtered">
							<v-chip close v-on:click:close="updFilter(item)">{{item.text}}</v-chip>
						</template>
					</div>
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
		<v-dialog v-model="importDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
			<v-card>
				<v-toolbar dark color="primary">
					<v-toolbar-title>
						Upload File to Import
					</v-toolbar-title>
					<div class="flex-grow-1"></div>
					<v-toolbar-items>
						<v-btn text @click="$refs.importForm.reset(); importAlert=false;importDialog =false">Cancel</v-btn>
					</v-toolbar-items>
				</v-toolbar>
				<v-card-text>
					<v-form ref="importForm">
						<v-row class="mx-4">
							<v-col>
								<v-file-input label="Click Here to select file.." persistent-hint hint="Please upload Excel file (.xslx) only" @change="fileUpdate"></v-file-input>
								<v-select label="Taxonomy" :items="filterables.taxonomy" item-text="name" item-value="id" v-model="importTaxonomy"></v-select>
								<v-select label="Method" :items="methods" v-model="importMethod"></v-select>
							</v-col>
						</v-row>
					</v-form>
				</v-card-text>
				<v-card-actions>
					<v-row class="mx-4">
						<v-col>
							<v-btn text :disabled="file == '' || file == undefined || importTaxonomy == '' || importMethod == ''" @click="upload">Submit</v-btn>
							<v-btn text  @click="$refs.importForm.reset();importAlert=false; importDialog = false">Cancel</v-btn>
						</v-col>
					</v-row>
				</v-card-actions>
				<v-row class="mx-4">
					<v-col>
						<v-alert v-model="importAlert" type="error" dismissible>
							<span v-html="importErrors"></span>
						</v-alert>
					</v-col>
				</v-row>.
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
	      	},
			search: {
				handler () {
				    this.deboucedSearch();
				},
				deep: true
			},
		},

		data(){
			return{
				search:'',
				filterPanel:-1,
				confirmDialog:false,
				importDialog:false,
				importTaxonomy:'',
				importMethod:'',
				importErrors:'',
				importAlert:false,
				waitDialog:false,
				file:'',
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
						text:'Code',
						value:'code'
					},
					{
						text:'Action',
						value:'action',
						sortable:false,
					},
				],
				items:[],
				filterables:{
					taxonomy:[]
				},
				filterdata:{
					taxonomy:[]
				},
				filtered:[],
				totalItems:0,
				meta:[
					{
						filtered:[],
					}
				],
				methods:[
					{
						'text':'Create',
						'value':'create'
					},
					{
						'text':'Update',
						'value':'update'
					}
				],
			}
		},
		mounted(){
			this.options.page=1
			this.deboucedSearch = _.debounce(()=>{
	            this.getDataFromApi()
	        },300);
			this.getFilterables()
		},
		methods:{
			upload(){
				if(this.file == '' || this.file === undefined){
					alert('Please Select a file first')
				}
				else{
					this.importErrors = ''
					this.importAlert = false
					this.waitDialog = true
					this.type_error = ''
					var fD = new FormData()
					fD.append('file',this.file)
					fD.append('taxonomy_id',this.importTaxonomy)
					fD.append('method',this.importMethod)
					axios.post('/categories/import',fD,{
						headers: {
					        'Content-Type': 'multipart/form-data'
					    }
					}).then((response)=>{
						this.waitDialog = false
					}).catch((error)=>{
						this.waitDialog = false
						if(error.response.status == 422){
							var str = '';
							Object.keys(error.response.data.messages).forEach((key)=>{
								str += '<p>Error in Line:'+(parseInt(key)+1)+'<br><ul>'
								Object.keys(error.response.data.messages[key]).forEach((item)=>{
									str+='<li>'+error.response.data.messages[key][item].message+'</li>'
								})
								str+='</ul></p>'
							})
							this.importErrors = str;
							this.importAlert = true
						}
					})
				}
			},
			fileUpdate(file){
				this.file = file
			},
			updFilter(item){
				console.log(item)
				Object.keys(this.filterdata).forEach((key)=>{
					if(key == item.filter){
						if(item.type == 'array'){	
							for( var i = 0; i < this.filterdata[key].length; i++){ 
							   if ( this.filterdata[key][i] == item.value) {
							     this.filterdata[key].splice(i, 1); 
							   }
							}
						}
					}
				})
				this.getDataFromApi()
			},
			filterToggle(){
				this.filterPanel = (this.filterPanel == -1) ? 0: -1
			},
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
			getFilterables(){
				axios.get('taxonomies').then((res)=>{
					this.filterables.taxonomy = res.data.data
				})
			},
			getDataFromApi(){
				this.loading = true
				const { sortBy, sortDesc, page, itemsPerPage } = this.options
				var params = '?page='+page+'&rpp='+itemsPerPage+'&search='+this.search+'&'
				if(sortBy.length == 1){
					params = params+'sortby='+sortBy[0]+'&'
				}
				if(sortDesc.length == 1 && sortDesc[0] == true){
					params = params+'descending=1&'
				}
				if(this.filterdata.taxonomy.length > 0){
					params = params+'filterby='
					this.filterdata.taxonomy.forEach((id)=>{
						params = params+id+'-'
					})
					params = params.substring(0,params.length -1)
					params = params+'&'
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