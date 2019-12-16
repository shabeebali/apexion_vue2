@extends('layouts.app')
@section('content')
<v-card>
	<v-card-title>
		Create User
	</v-card-title>
    <v-card-text>
    	<v-row>
    		<v-col cols="12" md="4"></v-col>
    		<v-col cols="12" md="4">
	        	<v-stepper class="mt-4"v-model="e1">
					<v-stepper-header>
						<v-stepper-step editable :rules="[rules.detailsValidity]" :complete="e1 > 1" step="1">Details</v-stepper-step>
						<v-divider></v-divider>
						<v-stepper-step editable :rules="[rules.passwordValidity]" step="2" :complete="e1 > 2">Password</v-stepper-step>
						<v-divider></v-divider>
						<v-stepper-step editable step="3">Role</v-stepper-step>
					</v-stepper-header>
					<v-stepper-items>
						<v-stepper-content step="1">
							<v-card flat class="mb-12"height="200px">
								<v-card-text>
									<v-form v-model="detailsFormVal" ref="detailsForm" v-on:submit.prevent="">
										<v-text-field 
											name="name"
											id="name"
											v-model="nufd.name.value" 
											label="Name" 
											autofocus 
											:error-messages="nufd.name.error"
											@keydown="nufd.name.error = ''"
											:rules=[rules.required]
										></v-text-field>
										<v-text-field 
											name="email"
											id="email"
											v-model="nufd.email.value"
											label="E-mail" 
											:error-messages="nufd.email.error"
											:rules="[rules.required,rules.email]"
											@keydown="nufd.email.error = ''">
										</v-text-field>
									</v-form>
								</v-card-text>
							</v-card>
							<v-btn color="primary" :disabled="nufd.name.value == '' || nufd.email.value == '' || detailsFormVal == false" @click="e1 = 2">Continue</v-btn>
							<v-btn text href="{{$prev_url}}">Cancel</v-btn>
						</v-stepper-content>
						<v-stepper-content step="2">
							<v-card flat class="mb-12"height="200px">
								<v-card-text>
									<v-form v-model="passFormVal" ref="passForm" v-on:submit.prevent="">
										<v-text-field 
											v-model="nufd.password.value" 
											label="Password" 
											type="password"
											autofocus
											:error-messages="nufd.password.error"
											:rules="[rules.required]"
											@keydown="nufd.password.error = ''">
										</v-text-field>
										<v-text-field 
											v-model="nufd.password_confirm.value"
											label="Confirm Password" 
											type="password" 
											:rules="[rules.required,rules.confirm]">
										</v-text-field>
									</v-form>
								</v-card-text>
							</v-card>
							<v-btn color="primary" :disabled="(nufd.password.value == '' || nufd.password_confirm.value == '' || passFormVal == false)" @click="e1 = 3">Continue</v-btn>
							<v-btn text href="{{$prev_url}}">Cancel</v-btn>
						</v-stepper-content>
						<v-stepper-content step="3">
							<v-card flat class="mb-12"height="200px">
								<v-card-text>
									<v-form ref="rolesForm" v-on:submit.prevent="">
										<v-select v-model="nufd.roles.value" multiple item-text="name" item-value="id" :items="nufd.roles.items" label="Select Role"
										hint="You can assign multiple roles for a user"></v-select>
									</v-form>
								</v-card-text>
							</v-card>
							<v-btn color="primary" :disabled="detailsFormVal == false || passFormVal == false" :loading="btnLoading" @click="saveNewuser()">Save</v-btn>
							<v-btn text href="{{$prev_url}}">Cancel</v-btn>
						</v-stepper-content>
					</v-stepper-items>
				</v-stepper>
			</v-col>
			<v-col cols="12" md="4"></v-col>
		</v-row>
    </v-card-text>
</v-card>
<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
	@{{sbText}}
	<v-btn dark text @click="snackbar = false"> Close</v-btn>
</v-snackbar>
@endsection
@section('script')
<script>
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				passChangeDialog : false,
				e1:0,
				btnLoading:false,
				detailsFormVal:false,
				passFormVal:false,
				passChVal:false,
				passCh:'',
				passChError:'',
				passChConfirm:'',
				nufd:{
					name:{
						'error':'',
						'value':''
					},
					email:{
						'error':'',
						'value':''
					},
					password:{
						'error':'',
						'value':''
					},
					password_confirm:{
						'value':''
					},
					roles:{
						'value':[],
						'items':[],
					},
				},
				rules:{
					required: value=> !!value||'Required.',
					confirm: value => value == this.nufd.password.value || 'Passwords does not match',
					confirm2: value => value == this.passCh || 'Passwords does not match',
					email: value => {
			            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			            return pattern.test(value) || 'Invalid e-mail.'
			        	},
			        detailsValidity: value => this.detailsFormVal == true || 'Error in this section',
			        passwordValidity: value => this.passFormVal == true || 'Error in this section',
				},
            }
        },
        watch:{
			passCh:function(){
				this.$refs.passChForm.validate()
			}
		},
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
            axios.get('users_roles').then((response)=>{
				this.nufd.roles.items = response.data.data
			})
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
			resetCreateForm(){
				this.$refs.detailsForm.reset()
				this.$refs.passForm.reset()
				this.$refs.rolesForm.reset()
				this.e1=1
			},
			saveNewuser(){
				this.btnLoading = true
				var fD = new FormData()
				Object.keys(this.nufd).forEach((key)=>{
					fD.append(key,this.nufd[key].value)
				})
				axios.post('users',fD).then((response)=>{
					this.btnLoading = false
					window.localStorage.setItem('message','User created successfully')
					window.localStorage.setItem('message_status','success')
					window.location.href = '{{$prev_url}}'
				}).catch((error)=> {
					if(error.response.status == 422){
						this.btnLoading = false
						var errors = error.response.data.errors
						Object.keys(errors).forEach((key)=>{
							this.nufd[key].error = errors[key]
						})
						this.triggerSb({text:'There are errors in the form submitted. Please check!!',color:'error'})
					}
					if(error.response.status == 403){
						this.btnLoading = false
						this.triggerSb({text:'You are not authorised to do this action',color:'error'})
					}
				})
			},
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
		}
    }).$mount('#app')
</script>
@endsection