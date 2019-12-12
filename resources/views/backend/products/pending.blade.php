@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-toolbar dense color="transparent" flat>
            <v-toolbar-title>Products pending approval</v-toolbar-title>
            <div class="flex-grow-1"></div>
            <v-toolbar-items>
                <div>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" color="transparent" dense depressed @click.stop="filterToggle">
                                <v-icon>mdi-filter</v-icon>
                            </v-btn>
                        </template>
                        <span>Filter</span>
                    </v-tooltip>
                </div>
                <div v-if="meta.import == 'true'">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" color="transparent" dense depressed @click.stop="importDialog = true">
                                <v-icon>mdi-upload</v-icon>
                            </v-btn>
                        </template>
                        <span>Import</span>
                    </v-tooltip>
                </div>
                <div>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" color="transparent" dense depressed @click.stop="exportData">
                                <v-icon>mdi-download</v-icon>
                            </v-btn>
                        </template>
                        <span>Export</span>
                    </v-tooltip>
                </div>
                <div>
                    <v-btn color="transparent" class="mr-2" dense depressed @click.stop="getDataFromApi">
                        <v-icon>mdi-refresh</v-icon>
                    </v-btn>
                </div>
                <div>
                    <v-btn v-if="meta.create == 'true'" color="primary" dense depressed :href="baseUrl+'/admin/products/add'">Create</v-btn>
                </div>
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
                                <v-btn text color="primary" @click.stop="applyFilter">Apply</v-btn>
                            </v-toolbar-items>
                        </v-toolbar>
                        <v-card-text>
                            <v-form ref="filterForm">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-autocomplete
                                            label="Category"
                                            multiple
                                            v-model="filterdata.categories"
                                            :items="filterables.categories"
                                            item-text="name"
                                            item-value="id"
                                            chips
                                            dense
                                            >
                                            <template v-slot:selection="data">
                                                <v-chip
                                                    v-bind="data.attrs"
                                                    :input-value="data.selected"
                                                    close
                                                    @click="data.select"
                                                    @click:close="removeCat(data.item)"
                                                    >
                                                    @{{ data.item.name }}
                                                </v-chip>
                                            </template>
                                            <template v-slot:item="data">
                                                <v-list-item-content>
                                                <v-list-item-title v-html="     data.item.name"></v-list-item-title>
                                                <v-list-item-subtitle> @{{data.item.taxonomy.name}}</v-list-item-subtitle>
                                                </v-list-item-content>
                                            </template>
                                        </v-autocomplete>   
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-select
                                            label="GST"
                                            multiple
                                            v-model="filterdata.gst"
                                            :items="filterables.gst"
                                            ></v-select>
                                    </v-col>
                                    <v-col cols="12" md="3">
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
                        <v-chip close v-on:click:close="updFilter(item)">@{{item.text}}</v-chip>
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
                        <a @click.stop="showProduct(item.id)">@{{item.name}}</a>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </v-col>
    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
        @{{sbText}}
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
                <v-btn text color="error" @click.stop="deleteProduct">Yes</v-btn>
                <v-btn text color="success" @click.stop="confirmDialog = false">No</v-btn>
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
                    <v-btn text @click.stop="$refs.importForm.reset(); importAlert=false;importDialog =false">Cancel</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text>
                <v-form ref="importForm">
                    <v-row class="mx-4">
                        <v-col>
                            <v-file-input label="Click Here to select file.." persistent-hint hint="Please upload Excel file (.xslx) only" @change="fileUpdate"></v-file-input>
                            <v-select label="Method" :items="methods" v-model="importMethod"></v-select>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-row class="mx-4">
                    <v-col>
                        <v-btn text :disabled="file == '' || file == undefined || importMethod == ''" @click.stop="upload">Submit</v-btn>
                        <v-btn text  @click.stop="$refs.importForm.reset();importAlert=false; importDialog = false">Cancel</v-btn>
                    </v-col>
                </v-row>
            </v-card-actions>
            <v-row class="mx-4">
                <v-col>
                    <v-alert v-model="importAlert" type="error" dismissible>
                        <span v-html="importErrors"></span>
                    </v-alert>
                </v-col>
            </v-row>
        </v-card>
    </v-dialog>
</v-row>
@endsection

@section('script')
<script>
    new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                search:'',
                filterPanel:-1,
                confirmDialog:false,
                importDialog:false,
                importProduct:'',
                importMethod:'',
                importErrors:'',
                importAlert:false,
                waitDialog:false,
                showDialog:false,
                file:'',
                delete_id : 0,
                pId : 0,
                snackbar:false,
                sbTimeout:3000,
                sbText:'',
                sbColor:'',
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
                        text:'SKU',
                        value:'sku',
                    },
                    {
                        text:'MRP',
                        value:'mrp',
                    },
                    {
                        text:'Landing Price',
                        value:'landing_price',
                    },
                    {
                        text:'GST',
                        value:'gst',
                    },
                    {
                        text:'Action',
                        value:'action',
                        sortable:false,
                    },
                ],
                items:[],
                filterables:{
                    categories:[],
                    gst:[
                        {text:'5%', value:'5'},
                        {text:'12%', value:'12'},
                        {text:'18%', value:'18'},
                    ],
                    taxonomies:[],
                },
                filterdata:{
                    categories:[],
                    gst:[],
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
        },
        mounted(){
            this.options.page = 1
            this.waitDialog = true
            axios.all([
                axios.get('categories').then((res)=>{
                    this.filterables.categories = res.data.data
                }),
                axios.get('taxonomies').then((res)=>{
                    this.filterables.taxonomies = _.groupBy(res.data.data,'id')
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                })
            ]).then(()=>{
                this.deboucedSearch = _.debounce(()=>{
                    this.applyFilter()
                },300)
                this.waitDialog = false
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
            removeCat(item) {
                const index = this.filterdata.categories.indexOf(item.id)
                if (index >= 0) this.filterdata.categories.splice(index, 1)
            },
            upload(){
                this.importErrors = ''
                this.importAlert = false
                this.waitDialog = true
                this.type_error = ''
                var fD = new FormData()
                fD.append('file',this.file)
                fD.append('method',this.importMethod)
                axios.post('/products/import',fD,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then((response)=>{
                    this.waitDialog = false
                    this.importDialog = false
                    this.triggerSb({text:'Products Imported Successfully',color:'success'})
                    this.getDataFromApi()
                }).catch((error)=>{
                    this.waitDialog = false
                    if(error.response.status == 422){
                        var str = '<ul>';
                        Object.keys(error.response.data.errors).forEach((key)=>{
                            var key_terms = key.split(".")
                            str+='<li>Error in Line'+parseInt(key_terms[0]+1)+', Column '+key_terms[1]+', Message:'+error.response.data.errors[key]+'</li>'
                        })
                        str+='</ul>'
                        this.importErrors = str;
                        this.importAlert = true
                    }
                })
            },
            fileUpdate(file){
                this.file = file
            },
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
                this.applyFilter()
            },
            filterToggle(){
                this.filterPanel = (this.filterPanel == -1) ? 0: -1
            },
            exportData(){
                this.waitDialog = true
                axios.get('products/export').then((res)=>{
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
                var params = '?pending=1&page='+page+'&rpp='+itemsPerPage+'&search='+this.search+'&'
                if(sortBy.length == 1){
                    params = params+'sortby='+sortBy[0]+'&'
                }
                if(sortDesc.length == 1 && sortDesc[0] == true){
                    params = params+'descending=1&'
                }
                if(this.filterdata.categories.length > 0){
                    params = params+'categories='
                    this.filterdata.categories.forEach((id)=>{
                        params = params+id+'-'
                    })
                    params = params.substring(0,params.length -1)
                    params = params+'&'
                }
                if(this.filterdata.gst.length > 0){
                    params = params+'gst='
                    this.filterdata.gst.forEach((id)=>{
                        params = params+id+'-'
                    })
                    params = params.substring(0,params.length -1)
                    params = params+'&'
                }
                axios.get('products'+params).then((response)=>{
                    this.loading = false
                    this.items = response.data.data
                    this.meta = response.data.meta
                    this.totalItems = response.data.total
                })
            },
            edit(id){
                window.location.href = this.baseUrl+'/admin/products/edit/'+id
            },
            deleteConfirm(id){
                this.delete_id = id
                this.confirmDialog = true
            },
            deleteProduct(){
                this.confirmDialog = false
                axios.delete('products/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
                    this.triggerSb({text:'Product is deleted from database',color:'info'})
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
            showProduct(id){
                window.location.href = this.baseUrl+'/admin/products/view/'+id
            },
            applyFilter(){
                this.options.page == 1 ? this.getDataFromApi() : this.options.page = 1
            }
        }
    }).$mount('#app')
</script>
@endsection