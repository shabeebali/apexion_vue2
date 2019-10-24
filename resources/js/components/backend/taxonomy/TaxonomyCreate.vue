<template>
	<v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar id="top" dark color="primary">
				<v-toolbar-title>{{formTitle}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click="closeDialog">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
				<v-row class="mx-4">
					<v-col cols="12" md="4"></v-col>
	        		<v-col cols="12" md="4">
	        			<v-form v-model="form1Val" ref="form1">
							<v-text-field 
								v-model="fd.name.value" 
								label="Name" 
								v-if="dialog" 
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
						<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="save()">{{submitTxt}}</v-btn>
						<v-btn text @click="closeDialog">Cancel</v-btn>
					</v-col>
					<v-col cols="12" md="4"></v-col>
				</v-row>
			</v-card-actions>
	    </v-card>
	    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Taxonomy'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Taxonomy'
					this.passFormVal = true
					axios.get('taxonomies/'+this.txId).then((response)=>{
						var dd = response.data.data
						this.fd.name.value = dd.name
						this.fd.in_pc.value = dd.in_pc
						this.fd.autogen.value = dd.autogen
						this.fd.code_length.value = dd.code_length
						this.fd.code_type.value = dd.code_type
					})
				}
			}
		},
		props:['mode','dialog','txId'],
		data(){
			return{
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
		methods:{
			cTypeUpd(){
				this.fd.code_type.value = this.code_type[this.fd.code_length.value].options[0].value
			},
			closeDialog(){
				this.$refs.form1.reset()
				this.$emit('close-dialog')
			},
			emitSb(text,color){
				var val = {
					'text':text,
					'color':color
				}
				this.$emit('trigger-sb',val)
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
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'taxonomies/'+this.txId
				}
				else{
					var route = 'taxonomies'
				}
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					if(this.mode == 'edit'){
						this.emitSb('Taxonomy Updated Successfully','success')
					}
					else{
						this.emitSb('Taxonomy Created Successfully','success')
					}
					this.closeDialog()
					this.$emit('update-list')
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
	}
</script>
