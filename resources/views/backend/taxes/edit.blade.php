@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-card-text>
                <v-form ref="form" v-on:submit.prevent="">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field label="Name" v-model="fd.name" :rules="[rules.required2]" :error-messages="errorMsgs.name"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-select :items="typeItems" label="Type" v-model="fd.type"></v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field label="Value" v-model="fd.value" :rules="[rules.required2]" :error-messages="errorMsgs.value"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-switch v-model="fd.applyToAll" label="Apply To All" ></v-switch>
                        </v-col>
                    </v-row>
                    <v-row v-if="!fd.applyToAll">
                        <v-col cols="12" class="subtitle-2">Rules</v-col>
                        <template v-for="(item,index) in fd.rules">
                            <v-row class="mx-2">
                                <v-col cols="12" md="3">
                                    <v-select prefix="If" :items="entityItems" label="Entity" v-model="fd.rules[index].ruleEntity" :rules="[rules.required]" @change="getAttributes(index)"></v-select>
                                </v-col>
                                <v-col cols="12" md="3">
                                     <v-select :items="attributeItems" label="Attribute" v-model="fd.rules[index].ruleAttribute" :rules="[rules.required]" @change="updRuleValue(index)"></v-select>
                                </v-col>
                                <v-col cols="12" md="3">
                                     <v-select :items="comparatorItems" label="Comparator" v-model="fd.rules[index].ruleComparator" :rules="[rules.required]"></v-select>
                                </v-col>
                                <v-col cols="12" md="3">
                                     <v-autocomplete v-if="fd.rules[index].value_type == 'select'" :items="ruleValueItems" label="Value" multiple v-model="fd.rules[index].ruleValue" :rules="[rules.required]"></v-autocomplete>
                                     <v-text-field v-if="fd.rules[index].value_type == 'text'" label="Value" v-model="fd.rules[index].ruleValue" :rules="[rules.required]"></v-text-field>
                                </v-col>
                                <v-row justify="end" v-if="fd.rules.length > 1">
                                    <v-col cols="12" md="2">
                                        <v-btn text @click="deleteRule(index)">Delete Rule</v-btn>
                                    </v-col>
                                </v-row>
                            </v-row>
                        </template>
                        <v-row>
                            <v-col>
                                <v-btn text @click="addRule" color="secondary">Add Rule</v-btn>
                            </v-col>
                        </v-row>
                    </v-row>
                    <v-row justify="space-around">
                        <v-btn @click="save" color="primary" depressed tile>Save</v-btn>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
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
                fd:{
                    applyToAll:true,
                    name:'',
                    type:'percentage',
                    value:'',
                    rules:[
                    ],
                    delete_rules_ids : [],
                },
                errorMsgs:{
                    name:'',
                    value:'',
                },
                typeItems:[
                    {value:'percentage',text:'Percentage'},
                    {value:'fixed',text:'Fixed Value'}
                ],
                entityItems:[
                    {value:'products',text:'Product'},
                    {value:'customers',text:'Customer'}
                ],
                attributeItems:[],
                ruleValueItems:[],
                comparatorItems:[
                    {text:'Is Equal','value':'eq'},
                    {text:'Is Greater Than','value':'gt'},
                    {text:'Is Greater Than or Equal','value':'gte'},
                    {text:'Is Less Than','value':'lt'},
                    {text:'Is Less Than or Equal','value':'lte'},
                    {text:'Is Not Equal','value':'neq'},
                ],
                rules:{
                    required: value=> (!!value && this.fd.applyToAll == false) ||'Required.',
                    required2: value=> !!value ||'Required.',
                },
                sbColor:'',
                sbText:'',
                sbTimeout:3000,
                snackbar:false,
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                }),
                axios.get('taxes/{{$id}}').then((res)=>{
                    this.fd.name = res.data.name
                    this.fd.value = res.data.value
                    this.fd.type = res.data.type
                    this.fd.applyToAll = res.data.apply_to_all == 1 ? true : false
                    if(res.data.rules.length > 0){
                        res.data.rules.forEach((item,index)=>{
                            this.fd.rules.push({
                                ruleEntity: item.rule_entity,
                                ruleAttribute: item.rule_attribute,
                                ruleComparator: item.rule_comparator,
                                ruleValue: isNaN(item.rule_value) ? item.rule_value : parseInt(item.rule_value),
                                value_type : 'text',
                                id:item.id
                            })
                            this.waitDialog = true
                            axios.get(this.fd.rules[index].ruleEntity+'/get_attributes').then((res)=>{
                                this.attributeItems = res.data
                            }).finally(()=>{
                                this.attributeItems.forEach((item)=>{
                                    if(item.value == this.fd.rules[index].ruleAttribute){
                                        if(item.type == 'select'){
                                            this.ruleValueItems = item.items
                                            this.fd.rules[index].ruleValue = this.fd.rules[index].ruleValue.toString().split(",").map((item)=>{
                                                return isNaN(item) ? item : parseInt(item)
                                            })
                                        }
                                        this.fd.rules[index].value_type = item.type
                                    }
                                })
                                this.waitDialog = false
                            })
                        })
                    }
                    else{
                        this.fd.rules.push(
                            {
                                ruleEntity:null,
                                ruleAttribute:null,
                                ruleComparator:null,
                                ruleValue:null,
                                value_type : 'text'
                            }
                        )
                    }
                }).finally(()=>{
                    this.waitDialog = false
                })
            ])
            
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
            triggerSb(text,color){
                this.sbText = text
                this.sbColor = color
                this.snackbar = true
            },
            getAttributes(index){
                this.waitDialog = true
                axios.get(this.fd.rules[index].ruleEntity+'/get_attributes').then((res)=>{
                    this.attributeItems = res.data
                }).finally(()=>{
                    this.waitDialog = false
                })
            },
            addRule(){
                this.attributeItems = []
                this.ruleValueItems = []

                this.fd.rules.push(
                    {
                        ruleEntity:null,
                        ruleAttribute:null,
                        ruleComparator:null,
                        ruleValue:null,
                    }
                )
            },
            deleteRule(index){
                this.fd.delete_rules_ids.push(this.fd.rules[index].id)
                this.fd.rules.splice(index,1)
            },
            updRuleValue(index){
                this.attributeItems.forEach((item)=>{
                    if(item.value == this.fd.rules[index].ruleAttribute){
                        if(item.type == 'select'){
                            this.ruleValueItems = item.items
                            
                        }
                        this.fd.rules[index].value_type = item.type
                    }
                })
            },
            save(){
                if(this.$refs.form.validate()){
                    this.waitDialog = true
                    var fD = new FormData()
                    fD.append('name',this.fd.name)
                    fD.append('type',this.fd.type)
                    fD.append('apply_to_all',this.fd.applyToAll)
                    fD.append('value',this.fd.value)
                    fD.append('delete_rules_ids',JSON.stringify(this.fd.delete_rules_ids))
                    fD.append('_method','PUT')
                    if(!this.fd.applyToAll){
                        fD.append('rules',JSON.stringify(this.fd.rules))
                    }
                    axios.post('taxes/{{$id}}',fD).then((res)=>{
                        this.waitDialog = false
                        this.emitSb('Tax Created Successfully','success')
                        window.location.href = '{{$prev_url}}'
                    }).catch((error)=> {
                        if(error.response.status == 422){
                            this.waitDialog = false
                            var errors = error.response.data.errors
                            Object.keys(errors).forEach((key)=>{
                                this.errorMsgs[key] = errors[key]
                            })
                            this.triggerSb('There are errors in the form submitted. Please check!!','error')
                            this.$vuetify.goTo(0)
                        }
                        if(error.response.status == 403){
                            this.waitDialog = false
                            this.triggerSb('You are not authorised to do this action','error')
                        }
                    })
                }
            }
        }
    }).$mount('#app')
</script>
@endsection
