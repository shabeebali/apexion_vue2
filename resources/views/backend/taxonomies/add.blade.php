@extends('layouts.app')
@section('content')
<v-row>
	<v-col class="mx-4">
		<v-card>
	        <v-card-title>Create Taxonomy</v-card-title>
	        <v-card-text>
				<v-row class="mx-4">
					<v-col cols="12" md="4"></v-col>
	        		<v-col cols="12" md="4">
	        			<v-form v-model="form1Val" ref="form1" v-on:submit.prevent="">
							<v-text-field 
								v-model="fd.name.value" 
								label="Name"  
								autofocus
								:error-messages="fd.name.error"
								@keydown="fd.name.error = ''"
								:rules=[rules.required]
							></v-text-field>
						</v-form>
						<v-switch v-model="fd.in_pc.value" true-value="1" false-value="0" label="Include in Product Code?"></v-switch>
						<v-select :disabled="fd.in_pc.value == '0'" label="Code Length" v-model="fd.code_length.value" :items="code_length.options" v-on:change="cTypeUpd"></v-select>
						<v-switch :disabled="fd.in_pc.value == '0'" v-model="fd.autogen.value" true-value="1" false-value="0" label="Auto Generate?"></v-switch>
						<v-select :disabled="fd.autogen.value != '1' || fd.in_pc.value == '0'" v-model="fd.code_type.value" label="Code Type" :items="code_type[fd.code_length.value]['options']"></v-select>
					</v-col>
					<v-col cols="12" md="4"></v-col>
				</v-row>
			</v-card-text>
			<v-card-actions>
				<v-row class="mx-4">
					<v-col cols="12" md="4"></v-col>
					<v-col cols="12" md="4">
						<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="save()">Save</v-btn>
						<v-btn text href="{{$prev_url}}">Cancel</v-btn>
					</v-col>
					<v-col cols="12" md="4"></v-col>
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
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                waitDialog : false,
                snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				btnloading:false,
				form1Val:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					code_length:{
	                    value:'1'
	                },
	                code_type:{
	                    value:'alpha'
	                },
	                autogen:{
	                    value:0
	                },
	                in_pc:{
	                    value:0
	                }
				},
				rules:{
					required: value=> !!value||'Required.',
				},
				code_length:{
	                'options':[
	                    {
	                        'text':'1','value':'1',
	                    },
	                    {
	                        'text':'2','value':'2',
	                    },
	                    {
	                        'text':'3','value':'3',
	                    }
	                ],
	            },
				code_type:{
	                '1':{
	                    'options':[
	                        {
	                            'text':'A','value':'alpha',
	                        },
	                        {
	                            'text':'1','value':'numeric',
	                        }
	                    ]
	                },
	                '2':{
	                    'options':[
	                        {
	                            'text':'AA','value':'alpha-alpha',
	                        },
	                        {
	                            'text':'A1','value':'alpha-numeric',
	                        },
	                        {
	                            'text':'1A','value':'numeric-alpha',
	                        },
	                        {
	                            'text':'11','value':'numeric-numeric',
	                        },
	                    ]
	                },
	                '3':{
	                    'options':[
	                        {
	                            'text':'AAA','value':'alpha-alpha-alpha',
	                        },
	                        {
	                            'text':'AA1','value':'alpha-alpha-numeric',
	                        },
	                        {
	                            'text':'A1A','value':'alpha-numeric-alpha',
	                        },
	                        {
	                            'text':'1AA','value':'numeric-alpha-alpha',
	                        },
	                        {
	                            'text':'11A','value':'numeric-numeric-alpha',
	                        },
	                        {
	                            'text':'1A1','value':'numeric-alpha-numeric',
	                        },
	                        {
	                            'text':'A11','value':'alpha-numeric-numeric',
	                        },
	                        {
	                            'text':'111','value':'numeric-numeric-numeric',
	                        },
	                    ]
	                },
	            }
            }
        },
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
			cTypeUpd(){
				this.fd.code_type.value = this.code_type[this.fd.code_length.value].options[0].value
			},
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
				fD.append('in_pc', this.fd.in_pc.value)
				fD.append('code_length', this.fd.code_length.value)
				fD.append('autogen', this.fd.autogen.value)
				fD.append('code_type', this.fd.code_type.value)
				var route = 'taxonomies'
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					this.emitSb('Taxonomy Created Successfully','success')
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