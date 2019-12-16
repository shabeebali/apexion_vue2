@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-toolbar id="top" flat color="transparent">
                <v-toolbar-title>Create Customer</v-toolbar-title>
                <div class="flex-grow-1"></div>
                <v-toolbar-items>
                    <v-btn text href="{{$prev_url}}">Back</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text>
                <v-form ref="form" v-on:submit.prevent="">
                    <v-text-field label="Name" v-model="fd.name.value" :error-messages="fd.name.error" @keydown="fd.name.error=''" :rules="[rules.required]"></v-text-field>
                    <div class="title">Accounts</div>
                
                    <template v-for="(address,index) in fd.addresses">
                        <v-card class="ma-4" color="grey lighten-4" elevation="4">
                            <v-toolbar flat>
                                <v-toolbar-title>
                                    Account @{{index+1}}
                                </v-toolbar-title>
                                <div class="flex-grow-1"></div>
                                <v-toolbar-items>
                                    <v-btn v-if="fd.addresses.length > 1" text @click.stop="deleteAddress(index)">Remove</v-btn>
                                </v-toolbar-items>
                            </v-toolbar>
                            <v-card-text>
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field label="Tag Name" :rules="[rules.required]" v-model="fd.addresses[index].tag_name.value" :error-messages="fd.addresses[index].tag_name.error" @keydown="fd.addresses[index].tag_name.error = ''"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field label="Line1" :rules="[rules.required]" v-model="fd.addresses[index].line_1"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field label="Line2" v-model="fd.addresses[index].line_2"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field label="PIN" v-model="fd.addresses[index].pin"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-autocomplete label="Country" v-model="fd.addresses[index].country_id" :items="countries" item-text="name" item-value="id" v-on:change="updState(index)"></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-autocomplete :disabled="fd.addresses[index].country_id == 0" label="State" v-model="fd.addresses[index].state_id" :items="fd.addresses[index].states" item-text="name" item-value="id" v-on:change="updCity(index)" :loading="stateLoading"></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-autocomplete :disabled="fd.addresses[index].state_id == 0" label="City" v-model="fd.addresses[index].city_id" :items="fd.addresses[index].cities" item-text="name" item-value="id" :loading="cityLoading"></v-autocomplete>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="6" md="3">
                                        <v-text-field label="Initial Balance" prepend-icon="mdi-currency-inr" :rules="[rules.price]" v-model="fd.addresses[index].init_bal"></v-text-field>
                                    </v-col>
                                    <v-col cols="6" md="3">
                                        <v-menu
                                            v-model="fd.addresses[index].date_menu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="290px"
                                            >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="fd.addresses[index].init_bal_date"
                                                    label="Due Date"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-on="on"
                                                    ></v-text-field>
                                            </template>
                                            <v-date-picker v-model="fd.addresses[index].init_bal_date"  @input="fd.addresses[index].date_menu = false">
                                            </v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select multiple label="Salepersons" v-model="fd.addresses[index].salepersons" :items="salepersons" item-text="name" item-value="id" :rules="[rules.arr]"></v-select>
                                    </v-col>
                                </v-row>
                                <template v-for="(phone,index2) in fd.addresses[index].phones">
                                    <v-row>
                                        <v-col cols="4" md="3">
                                            <v-select :items="phone_countries" item-text="name" item-value="id"
                                            v-model="fd.addresses[index].phones[index2].country_id" persistent-hint hint="Country Code">
                                            </v-select>
                                        </v-col>
                                        <v-col cols="8" md="4">
                                            <v-text-field :label="'Phone '+(index2+1)" v-model="fd.addresses[index].phones[index2].value" :append-outer-icon="fd.addresses[index].phones.length > 1 ? 'mdi-minus-circle':''" v-on:click:append-outer="deletePhone(index,index2)">
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="5"></v-col>
                                    </v-row>
                                </template>
                                <v-btn text @click="addPhone(index)">Add Phone</v-btn>
                                <v-row>
                                    <v-col cols="12" md="3" v-if="user.meta.approve_customer">
                                        <v-switch v-model="fd.addresses[index].approved" label="Approved?" true-value=1 false-value=0>
                                        </v-switch>
                                    </v-col>
                                    <v-col cols="12" md="3" v-if="user.meta.tally_customer">
                                        <v-switch v-model="fd.addresses[index].tally" label="Tally updated?" true-value=1 false-value=0>
                                        </v-switch>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </template>
                    <v-btn text @click="addAddress">Add Account</v-btn>
                    <v-row justify="space-around">
                        <v-col>
                            <v-btn tile color="primary" @click="save()" :loading="btnloading">Save</v-btn>
                            <v-btn tile color="secondary" @click="closeConfirm = true">Cancel</v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
        <v-dialog v-model ="closeConfirm" persistent width="500">
            <v-card>
                <v-card-text class="pt-4">
                    <v-alert dark color="error">
                        All your unsaved changes will be lost. Do you want to continue? 
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-btn text color="error" @click.stop="closeDialog">Yes</v-btn>
                    <v-btn text color="success" @click.stop="closeConfirm = false">NO</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model ="duplicateConfirm" persistent width="600">
            <v-card>
                <v-card-text class="pt-4">
                    <div v-html="duplicateMessage"></div>
                </v-card-text>
                <v-card-actions>
                    <v-btn text color="error" @click.stop="submitData">Yes</v-btn>
                    <v-btn text color="success" @click.stop="duplicateConfirm = false">NO</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="waitDialog" persistent width="300">
            <v-card color="primary" dark>
                <v-card-text>
                    Please stand by
                    <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
            @{{sbText}}
            <v-btn dark text @click.stop="snackbar = false"> Close</v-btn>
        </v-snackbar>
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
                stateLoading:false,
                cityLoading:false,
                e1:1,
                submitTxt:'',
                formTitle:'',
                closeConfirm:false,
                btnloading:false,
                sbColor:'',
                sbText:'',
                sbTimeout:10000,
                snackbar:false,
                fd:{
                    name:{
                        value:'',
                        error:'',
                    },
                    addresses:[
                        {
                            date_menu:false,
                            tag_name:{
                                value:'',error:'',
                            },
                            line_1:'',
                            line_2:'',
                            pin:'',
                            country_id:0,
                            state_id:0,
                            city_id:0,
                            states:[],
                            cities:[],
                            init_bal:'',
                            init_bal_date: new Date().toISOString().substr(0, 10),
                            phones:[
                                {
                                    value:'',
                                    country_id:101
                                }
                            ],
                            approved:0,
                            tally:0,
                            salepersons:[],
                        }
                    ]
                },
                salepersons:[],
                countries:[],
                states:[],
                cities:[],
                phone_countries:[],
                rules:{
                    required: value=> !!value||'Required.',
                    price: value => {
                        const pattern = /^\d{0,8}(\.\d{1,2})?$/
                        return pattern.test(value) || 'Invalid value.'
                        },
                    arr: value => value.length > 0 || 'Must not empty',
                },
                fD:null,
                route:'',
                checkRoute:'',
                duplicateConfirm:false,
                duplicateMessage:'',
                user:{
                    meta:{
                        tally_customer:false,
                        approve_customer:false,
                    }
                },
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('places').then((res)=>{
                    this.countries = res.data.countries
                    this.phone_countries = res.data.phone_countries
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                }),
                axios.get('users?role=Sale').then((res)=>{
                    this.salepersons = res.data
                }),
                axios.get('user?with_permissions=1').then((res)=>{
                    res.data.roles.forEach((role)=>{
                        role.permissions.forEach((permission)=>{
                            if(permission.name == 'approve_customer'){
                                this.user.meta.approve_customer = true
                            }
                            if(permission.name == 'tally_customer'){
                                this.user.meta.tally_customer = true
                            }
                        })
                    })
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
            triggerSb(text,color){
                this.sbText = text
                this.sbColor = color
                this.snackbar = true
            },
        	emitSb(text,color){
                window.localStorage.setItem('message',text)
                window.localStorage.setItem('message_status',color)
            },
            updState(index){
                this.waitDialog = true
                this.stateLoading = true
                axios.get('places?country_id='+this.fd.addresses[index].country_id).then((res)=>{
                    this.fd.addresses[index].states = res.data
                    this.fd.addresses[index].state_id = 0
                    this.stateLoading = false
                    this.updCity(index)
                    this.waitDialog = false
                })
            },
            closeDialog(){
                window.location.href = '{{$prev_url}}'
            },
            updCity(index){
                if(this.fd.addresses[index].state_id != 0){
                    this.waitDialog = true
                    this.cityLoading = true
                    axios.get('places?state_id='+this.fd.addresses[index].state_id).then((res)=>{
                        this.fd.addresses[index].cities = res.data
                        this.fd.addresses[index].city_id = 0
                        this.cityLoading = false
                        this.waitDialog = false
                    })
                }
                else{
                    this.fd.addresses[index].cities = []
                    this.fd.addresses[index].city_id = 0
                }
            },
            getCatName(item){
                if(item.value){
                    const val = item.value
                    var grouped = _.groupBy(item.categories,'id')
                    var index = Object.keys(grouped).indexOf(item.value.toString())
                    return item.categories[index].name
                }
                return ''
            },
            addPhone(index){
                this.fd.addresses[index].phones.push(
                    {
                        value:'',
                        country_id:101
                    })
            },
            addAddress(){
                this.fd.addresses.push({
                    date_menu:false,
                    tag_name:{
                        value:'',error:'',
                    },
                    line_1:'',
                    line_2:'',
                    pin:'',
                    country_id:0,
                    state_id:0,
                    city_id:0,
                    states:[],
                    cities:[],
                    init_bal:'',
                    init_bal_date: new Date().toISOString().substr(0, 10),
                    phones:[
                        {
                            value:'',
                            country_id:101
                        }
                    ],
                    approved:0,
                    tally:0,
                })
            },
            deletePhone(index,index2){
                this.fd.addresses[index].phones.splice(index2,1)
            },
            deleteAddress(index){
                this.fd.addresses.splice(index,1)
            },
            submitData(){
                axios.post(this.route,this.fD).then((response)=>{
                    this.btnloading = false
                    this.emitSb('Customer Created Successfully','success')
                    window.location.href = '{{$prev_url}}'
                }).catch((error)=> {
                    if(error.response.status == 422){
                        this.btnloading = false
                        var errors = error.response.data.errors
                        this.fd.name.error = errors.name
                        Object.keys(errors).forEach((key)=>{
                            var keys = key.split(".")
                            if(keys[1] in this.fd.addresses[0]){
                                this.fd.addresses[keys[0]][keys[1]].error = errors[key]
                            }
                        })
                        this.triggerSb('There are errors in the form submitted. Please check!!','error')
                    }
                    if(error.response.status == 403){
                        this.btnloading = false
                        this.triggerSb('You are not authorised to do this action','error')
                    }
                })
            },
            save(){
                this.btnloading = true
                if(!this.$refs.form.validate()){
                    this.$vuetify.goTo(0)
                    this.triggerSb('There are errors in the form submitted. Please check!!','error')
                    this.btnloading = false
                }
                else{
                    this.fD = new FormData()
                    this.fD.append('name',this.fd.name.value)
                    this.fD.append('addresses',JSON.stringify(this.fd.addresses))
                    this.route = 'customers'
                    this.checkRoute = 'customers/check'
                    axios.post(this.checkRoute,this.fD).then((res)=>{
                        if(res.data.message == 'warning'){
                            this.duplicateMessage = res.data.warning
                            this.duplicateConfirm = true
                        }
                        else{
                            this.submitData()
                        }
                    })
                }
            }
        }
    }).$mount('#app')
</script>
@endsection
