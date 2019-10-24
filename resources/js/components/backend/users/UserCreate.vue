<template>
	<v-dialog v-model="openDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar dark color="primary">
				<v-btn icon dark @click="closeDialog">
					<v-icon>fa-close</v-icon>
				</v-btn>
				<v-toolbar-title>{{formTitle}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click="closeDialog">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
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
											<v-form v-model="detailsFormVal" ref="detailsForm">
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
									<v-btn text @click="closeDialog">Cancel</v-btn>
								</v-stepper-content>
								<v-stepper-content step="2">
									<v-card flat class="mb-12"height="200px">
										<v-card-text>
											<v-form v-model="passFormVal" ref="passForm">
												<template v-if="mode=='create'">
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
												</template>
												<template v-if="mode=='edit'">
													<div class="caption">If you don't want to change the password click continue</div>
													<v-btn color="info" @click="passChangeDialog = true">Change Password</v-btn>
												</template>
											</v-form>
										</v-card-text>
									</v-card>
									<v-btn color="primary" :disabled="(nufd.password.value == '' || nufd.password_confirm.value == '' || passFormVal == false) && mode == 'create'" @click="e1 = 3">Continue</v-btn>
									<v-btn text @click="closeDialog">Cancel</v-btn>
								</v-stepper-content>
								<v-stepper-content step="3">
									<v-card flat class="mb-12"height="200px">
										<v-card-text>
											<v-form ref="rolesForm">
												<v-select v-model="nufd.roles.value" multiple item-text="name" item-value="id" :items="nufd.roles.items" label="Select Role"
												hint="You can assign multiple roles for a user"></v-select>
											</v-form>
										</v-card-text>
									</v-card>
									<v-btn color="primary" :disabled="detailsFormVal == false || passFormVal == false" :loading="btnLoading" @click="saveNewuser()">{{submitTxt}}</v-btn>
									<v-btn text @click="closeDialog">Cancel</v-btn>
								</v-stepper-content>
							</v-stepper-items>
						</v-stepper>
					</v-col>
					<v-col cols="12" md="4"></v-col>
				</v-row>
	        </v-card-text>
	    </v-card>
	    <v-dialog v-model="passChangeDialog" max-width="290" persistent>
	    	<v-card>
	    		<v-card-title class="headline">Change Password</v-card-title>
	    		<v-card-text>
	    			<v-form v-model="passChVal" ref="passChForm">
		    			<v-text-field :rules="[rules.required]" v-model="passCh" :error-messages="passChError" label="New Password" @keydown="passChError = ''"></v-text-field>
		    			<v-text-field :rules="[rules.required,rules.confirm2]" v-model="passChConfirm" label="Confirm New Password"></v-text-field>
		    		</v-form>
	    		</v-card-text>
	    		<v-card-actions>
	    			<v-btn color="primary" :disabled="passChVal == false" @click="changePass">Submit</v-btn>
	    			<v-btn text @click="passCh = ''; passChConfirm = ''; passChangeDialog = false">Cancel</v-btn>
	    		</v-card-actions>
	    	</v-card>
	    </v-dialog>
	    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			axios.get('users_roles').then((response)=>{
				this.nufd.roles.items = response.data.data
			})
		},
		watch:{
			openDialog:function(){
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create User'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit User'
					this.passFormVal = true
					axios.get('users/'+this.userId).then((response)=>{
						var dd = response.data.data
						this.nufd.name.value = dd.name
						this.nufd.email.value = dd.email
						this.nufd.roles.value = response.data.roles
					})
				}
			},
			passCh:function(){
				this.$refs.passChForm.validate()
			}
		},
		data(){
			return{
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				passChangeDialog : false,
				submitTxt:'',
				formTitle:'',
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
		props:['openDialog','mode','userId'],
		methods:{
			resetCreateForm(){
				this.$refs.detailsForm.reset()
				this.$refs.passForm.reset()
				this.$refs.rolesForm.reset()
				this.e1=1
			},
			saveNewuser(){
				if(this.mode == 'create'){
					this.btnLoading = true
					var fD = new FormData()
					Object.keys(this.nufd).forEach((key)=>{
						fD.append(key,this.nufd[key].value)
					})
					axios.post('users',fD).then((response)=>{
						this.btnLoading = false
						this.closeDialog()
						this.emitSb('User Created Successfully','success')
						this.loading = true
						this.$emit('update-list');
					}).catch((error)=> {
						if(error.response.status == 422){
							this.btnLoading = false
							var errors = error.response.data.errors
							Object.keys(errors).forEach((key)=>{
								this.nufd[key].error = errors[key]
							})
							this.emitSb('There are errors in the form submitted. Please check!!','error')
						}
						if(error.response.status == 403){
							this.btnLoading = false
							this.emitSb('You are not authorised to do this action','error')
						}
						
					})
				}
				if(this.mode == 'edit'){
					this.btnLoading = true
					var fD = new FormData()
					Object.keys(this.nufd).forEach((key)=>{
						fD.append(key,this.nufd[key].value)
					})
					fD.append('_method','PUT')
					axios.post('users/'+this.userId,fD).then((response)=>{
						this.btnLoading = false
						this.closeDialog()
						this.emitSb('User Updated Successfully','success')
						this.loading = true
						this.$emit('update-list');
					}).catch((error)=> {
						if(error.response.status == 422){
							this.btnLoading = false
							var errors = error.response.data.errors
							Object.keys(errors).forEach((key)=>{
								this.nufd[key].error = errors[key]
							})
							this.emitSb('There are errors in the form submitted. Please check!!','error')
						}
						if(error.response.status == 403){
							this.btnLoading = false
							this.emitSb('You are not authorised to do this action','error')
						}
					})
				}
			},
			closeDialog(){
				this.resetCreateForm()
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
			changePass(){
				var fD = new FormData()
				fD.append('password',this.passCh)
				axios.post('users/chpass/'+this.userId,fD).then((response)=>{
					this.passChangeDialog = false
					this.emitSb('The Password has been changed successfully','success')
				}).catch((error)=>{
					if(error.response.status == 422){
						var errors = error.response.data.errors
						this.passChError = errors.password
						this.triggerSb({text:'There are errors in the form submitted. Please check!!',color:'error'})
					}
					if(error.response.status == 403){
						this.triggerSb({text:'You are not authorised to do this action',color:'error'})
					}
				})
			}
		}
	}
</script>
