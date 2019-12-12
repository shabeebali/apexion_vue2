@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-card-title>Edit Warehouse</v-card-title>
            <v-card-text>
                <v-form v-model="form1Val" ref="form1" v-on:submit.prevent="">
                    <v-row class="mx-4">
                        <v-col cols="12" >
                            <v-text-field 
                                v-model="fd.name.value" 
                                label="Name" 
                                autofocus
                                :error-messages="fd.name.error"
                                @keydown="fd.name.error = ''"
                                :rules=[rules.required]
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-row class="mx-4">
                    <v-col>
                        <v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="save()">Update</v-btn>
                        <v-btn text href="{{$prev_url}}">Cancel</v-btn>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>
        <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
            @{{sbText}}
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
    </v-col>
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
                waitDialog:false,
                snackbar:false,
                sbTimeout:3000,
                sbText:'',
                sbColor:'',
                submitTxt:'',
                formTitle:'',
                btnloading:false,
                form1Val:false,
                fd:{
                    name:{
                        value:'',
                        error:'',
                    },
                },
                rules:{
                    required: value=> !!value||'Required.',
                },
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('warehouses/{{$id}}').then((response)=>{
                    var dd = response.data.data
                    this.fd.name.value = dd.name
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                })
            ]).then(()=>{
                this.waitDialog = false
            })
            
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
        	emitSb(text,color){
                window.localStorage.setItem('message',text)
                window.localStorage.setItem('message_status',color)
            },
            triggerSb(val){
                this.snackbar = false
                this.sbText = val.text
                this.sbColor = val.color
                this.snackbar = true
            },
            save(){
                this.btnloading = true
                var fD = new FormData()
                fD.append('name',this.fd.name.value)
                fD.append('_method','PUT')
                var route = 'warehouses/{{$id}}'
                axios.post(route,fD).then((response)=>{
                    this.btnloading = false
                    this.emitSb('Warehouse Updated Successfully','success')
                    window.location.href = '{{$prev_url}}'
                }).catch((error)=> {
                    if(error.response.status == 422){
                        this.btnloading = false
                        var errors = error.response.data.errors
                        this.fd.name.error = errors.name
                        this.triggerSb({text:'There are errors in the form submitted. Please check!!',color:'error'})
                    }
                    if(error.response.status == 403){
                        this.btnloading = false
                        this.triggerSb({text:'You are not authorised to do this action',color:'error'})
                    }
                })
            }
        }
    }).$mount('#app')
</script>
@endsection
