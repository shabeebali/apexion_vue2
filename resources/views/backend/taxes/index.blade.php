@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-toolbar transparent flat>
            <v-toolbar-title>Taxes</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-btn tile color="primary" depressed v-if="meta.create == 'true'" :href="baseUrl+'/admin/taxes/add'">Create</v-btn>
            </v-toolbar-items>
        </v-toolbar>
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
                </v-data-table>
            </v-card-text>
        </v-card>
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
                <v-btn text color="error" @click.stop="deleteTax">Yes</v-btn>
                <v-btn text color="success" @click.stop="confirmDialog = false">No</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    </v-col>
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
                waitDialog:false,
                snackbar:false,
                sbTimeout:3000,
                sbText:'',
                sbColor:'',
                items:[],
                meta:[],
                loading:false,
                search:null,
                headers:[
                    {
                        text:'ID',
                        value:'id'
                    },
                    {
                        text:'Name',
                        value:'name',
                    },
                    {
                        text:'Action',
                        value:'action',
                        sortable:false,
                    },
                ],
                confirmDialog:false,
                delete_id:0,
            }
        },
        mounted(){
            axios.all([
                axios.get('taxes').then((res)=>{
                    this.items = res.data.data
                    this.meta = res.data.meta
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                })
            ]).then(()=>{
                this.deboucedSearch = _.debounce(()=>{
                    this.getDataFromApi()
                },300);
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
            edit(id){
                window.location.href = this.baseUrl+'/admin/taxes/edit/'+id
            },
            deleteConfirm(id){
                this.delete_id = id
                this.confirmDialog = true
            },
            deleteTax(){
                this.confirmDialog = false
                axios.delete('taxes/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
                    this.triggerSb({text:'The Tax is deleted from database',color:'info'})
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
        }
    }).$mount('#app')
</script>
@endsection