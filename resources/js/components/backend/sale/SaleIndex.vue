<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>{{pageTitle}}</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }">
							<v-btn v-on="on" color="transparent" dense depressed @click.stop="filterToggle">
								<v-icon>mdi-filter</v-icon>
							</v-btn>
						</template>
						<span>Filter</span>
					</v-tooltip>
					<!-- user.id ==1 means Super User -->
					<v-btn color="transparent" class="mr-2" dense depressed @click.stop="getDataFromApi">
						<v-icon>mdi-refresh</v-icon>
					</v-btn>
					<v-btn v-if="meta.create == 'true'" color="primary" dense depressed @click.stop="mode = 'create'; dialog = true">Create</v-btn>
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
									<v-btn text @click.stop="$refs.filterForm.reset(); getDataFromApi()">Reset</v-btn>
									<v-btn text color="primary" @click.stop="getDataFromApi">Apply</v-btn>
								</v-toolbar-items>
					        </v-toolbar>
							<v-card-text>
								<v-form ref="filterForm">
									<v-row>
										<v-col cols="12" md="6">
											<v-autocomplete
												label="Customer Address Tag"
												multiple
												:search-input.sync="addSearch"
												placeholder="Start typing to Search"
												v-model="filterdata.addresses"
												:items="filterables.addresses"
												item-text="tag_name"
												cache-items
												item-value="id"
												dense
												chips
												deletable-chips
												>
											</v-autocomplete>	
										</v-col>
										<v-col cols="12" md="6">
											<v-autocomplete
												label="Saleperson"
												multiple
												v-model="filterdata.salepersons"
												:items="filterables.salepersons"
												item-text="name"
												item-value="id"
												chips
												dense
												deletable-chips
												>
											</v-autocomplete>
										</v-col>
									</v-row>
								</v-form>
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
									<v-icon small @click.stop="edit(item.id)" v-on="on">mdi-pencil</v-icon>
									</template>
								<span>Edit</span>
							</v-tooltip>
							<v-tooltip bottom v-if="meta.delete == 'true'">
      							<template v-slot:activator="{ on }">
									<v-icon small @click.stop="deleteConfirm(item.id)" v-on="on">mdi-delete</v-icon>
									</template>
								<span>Delete</span>
							</v-tooltip>
						</template>
						<template v-slot:item.name="{item}">
							<a @click.stop="showSale(item.id)">{{item.name}}</a>
						</template>
						<template v-slot:item.tag_name="{item}">
							<div v-html="item.tag_name"></div>
						</template>
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
		<sale-create :dialog="dialog" :mode="mode" :saleId="saleId" v-on:trigger-sb="triggerSb" v-on:close-dialog="mode= ''; saleId = 0; dialog = false" v-on:update-list="getDataFromApi"></sale-create>
		<sale-show :dialog="showDialog" :saleId="saleId" v-on:close-dialog="showDialog = false"></sale-show>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click.stop="snackbar = false"> Close</v-btn>
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
					<v-btn text color="error" @click.stop="deleteSale">Yes</v-btn>
					<v-btn text color="success" @click.stop="confirmDialog = false">No</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-row>
</template>
<script>
	import SaleCreate from './SaleCreate.vue'
	import SaleShow from './SaleShow.vue'
	export default{
		components:{
			SaleCreate,SaleShow
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
				    if(this.search.length != 1) this.deboucedSearch();
				},
				deep: true
			},
			addSearch(val){

				if(val != '' && val != null)
					axios.get('customers/add_search?search='+val+'&rpp=5').then((res)=>{
						this.filterables.addresses = res.data
					})
			}
		},
		mounted(){
			axios.get('users?role=Sale').then((res)=>{
				this.filterables.salepersons = res.data
			})
			this.deboucedSearch = _.debounce(()=>{
	            this.getDataFromApi()
	        },300);
		},
		data(){
			return{
				addSearch:'',
				pageTitle:'',
				search:'',
				filterPanel:-1,
				confirmDialog:false,
				waitDialog:false,
				showDialog:false,
				delete_id : 0,
				mode:'',
				saleId : 0,
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				dialog:false,
				loading:false,
				options:{},
				headers:[
					{
						text:'Order ID',
						value:'order_id'
					},
					{
						text:'Address Tag',
						value:'address_id',
						sortable:false,
					},
					{
						text:'Saleperson',
						value:'saleperson_id',
						sortable:false,
					},
					{
						text:'Action',
						value:'action',
						sortable:false,
					},
				],
				items:[],
				filterables:{
					salepersons:[],
					addresses:[],
				},
				filterdata:{
					salepersons:[],
					addresses:[],
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
		methods:{
			updFilter(item){
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
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
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
				if(this.filterdata.salepersons.length > 0){
					params = params+'salepersons='
					this.filterdata.salepersons.forEach((id)=>{
						params = params+id+'-'
					})
					params = params.substring(0,params.length -1)
					params = params+'&'
				}
				if(this.filterdata.addresses.length > 0){
					params = params+'addresses='
					this.filterdata.addresses.forEach((id)=>{
						params = params+id+'-'
					})
					params = params.substring(0,params.length -1)
					params = params+'&'
				}
				axios.get('sales'+params).then((response)=>{
					this.loading = false
					this.items = response.data.data
					this.meta = response.data.meta
					this.totalItems = response.data.total
				})
			},
			edit(id){
				this.saleId = id
				this.mode = 'edit'
				this.dialog = true
			},
			deleteConfirm(id){
				this.delete_id = id
				this.confirmDialog = true
			},
			deleteSale(){
				this.confirmDialog = false
				axios.delete('sales/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
					this.triggerSb({text:'The Order is deleted from database',color:'info'})
					this.getDataFromApi()
				}).catch((error)=> {
					if(error.response.status == 403){
						this.triggerSb({
							text:'You are not authorised to do this action',
							color:'error'
						})
					}
				})
			},
			showCustomer(id){
				this.saleId = id
				this.showDialog = true
			}
		}
	}
</script>