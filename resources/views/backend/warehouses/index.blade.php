@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-toolbar dense color="transparent" flat>
            <v-toolbar-title>Warehouses</v-toolbar-title>
            <div class="flex-grow-1"></div>
            <v-toolbar-items>
                <v-btn  v-if="meta.create == 'true'" color="primary" dense depressed :href="baseUrl+'/admin/warehouses/add'">Create</v-btn>
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
    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
        @{{sbText}}
        <v-btn dark text @click="snackbar = false"> Close</v-btn>
    </v-snackbar>
    <v-dialog v-model="confirmDialog" persistent width="500">
        <v-card>
            <v-card-title>
                Warning
            </v-card-title>
            <v-card-text>
                <v-alert dark color="error">
                Do you really Want to continue?. All the stock values under this warehouse will be gone.</v-alert>
            </v-card-text>
            <v-card-actions>
                <v-btn text color="error" @click="deleteWh">Yes</v-btn>
                <v-btn text color="success" @click="confirmDialog = false">No</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
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
                confirmDialog:false,
                delete_id : 0,
                whId : 0,
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
                axios.get('warehouses').then((response)=>{
                    this.loading = false
                    this.items = response.data.data
                    this.meta = response.data.meta
                })
            },
            edit(id){
                window.location.href = this.baseUrl+'/admin/warehouses/edit/'+id
            },
            deleteConfirm(id){
                this.delete_id = id
                this.confirmDialog = true
            },
            deleteWh(){
                this.confirmDialog = false
                axios.delete('warehouses/'+this.delete_id,{_method: 'DELETE'}).then((res)=>{
                    this.triggerSb({text:'Warehouse is deleted from database',color:'info'})
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