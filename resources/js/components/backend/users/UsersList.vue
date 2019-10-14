<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>Users</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn color="primary" dense depressed @click="createDialog = true">Create</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			<v-card class="mt-2">
				<v-card-text>
					<v-data-table 
						:headers="headers" 
						:items="items" 
						:loading="loading">
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
		<v-dialog v-model="createDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
			<v-card>
		        <v-toolbar dark color="primary">
					<v-btn icon dark @click="createDialog = false">
						<v-icon>fa-close</v-icon>
					</v-btn>
					<v-toolbar-title>Create User</v-toolbar-title>
					<div class="flex-grow-1"></div>
					<v-toolbar-items>
						<v-btn dark text :disabled="newDetailsFormValidity == false || newPasswordFormValidity == false" :loading="newUserSaveButtonLoading" @click="saveNewuser()">Save</v-btn>
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
									<v-stepper-step editable :rules="[rules.passwordValidity]" step="2">Password</v-stepper-step>
								</v-stepper-header>
								<v-stepper-items>
									<v-stepper-content step="1">
										<v-card flat class="mb-12"height="200px">
											<v-card-text>
												<v-form v-model="newDetailsFormValidity">
													<v-text-field 
														v-model="newUserName" 
														label="Name" 
														autofocus 
														:error-messages="emsg.name"
														@keydown="emsg.name = ''"
														:rules=[rules.required]
													></v-text-field>
													<v-text-field 
														v-model="newUserMail"
														label="E-mail" 
														:error-messages="emsg.email"
														:rules="[rules.required,rules.email]"
														@keydown="emsg.email = ''">
													</v-text-field>
												</v-form>
											</v-card-text>
										</v-card>
										<v-btn color="primary" :disabled="newUserName == '' || newUserMail == '' || newDetailsFormValidity == false" @click="e1 = 2">Continue</v-btn>
										<v-btn text @click="resetCreateForm(); createDialog = false">Cancel</v-btn>
									</v-stepper-content>
									<v-stepper-content step="2">
										<v-card flat class="mb-12"height="200px">
											<v-card-text>
												<v-form v-model="newPasswordFormValidity">
													<v-text-field 
														v-model="newUserPassword" 
														label="Password" 
														type="password"
														autofocus
														:error-messages="emsg.password"
														:rules="[rules.required]"
														@keydown="emsg.password = ''">
													</v-text-field>
													<v-text-field 
														v-model="newUserPasswordConfirm"
														label="Confirm Password" 
														type="password" 
														:rules="[rules.required,rules.confirm]">
													</v-text-field>
												</v-form>
											</v-card-text>
										</v-card>
										<v-btn text @click="resetCreateForm(); createDialog = false">Cancel</v-btn>
									</v-stepper-content>
								</v-stepper-items>
							</v-stepper>
						</v-col>
						<v-col cols="12" md="4"></v-col>
					</v-row>
		        </v-card-text>
		    </v-card>
		</v-dialog>
		<v-snackbar v-model="userCreateSb" right botttom :color="sbColor" :timeout="sbTimeout">
			{{sbText}}
			<v-btn dark text @click="userCreateSb = false"> Close</v-btn>
		</v-snackbar>
	</v-row>
</template>
<script>
	export default{
		data(){
			return{
				userCreateSb:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				e1:0,
				emsg:{
					name:'',
					email:'',
					password:'',
				},
				newUserSaveButtonLoading:false,
				newDetailsFormValidity:true,
				newPasswordFormValidity:true,
				newUserName:'',
				newUserMail:'',
				newUserPassword:'',
				newUserPasswordConfirm:'',
				rules:{
					required: value=> !!value||'Required.',
					confirm: value => value == this.newUserPassword || 'Passwords does not match',
					email: value => {
			            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			            return pattern.test(value) || 'Invalid e-mail.'
			        	},
			        detailsValidity: value => this.newDetailsFormValidity == true || 'Error in this section',
			        passwordValidity: value => this.newPasswordFormValidity == true || 'Error in this section',
				},
				createDialog:false,
				loading:false,
				headers:[
					{
						'text':'ID',
						'value':'id'
					},
					{
						'text':'Name',
						'value':'name'
					},
					{
						'text':'E-mail',
						'value':'email'
					}
				],
				items:[]
			}
		},
		mounted(){
			this.loading = true
			this.getDataFromApi()
		},
		methods:{
			getDataFromApi(){
				axios.get('users/list').then((response)=>{
					this.loading = false
					this.items = response.data.items
				})
			},
			resetCreateForm(){
				this.newUserName=''
				this.newUserMail=''
				this.newUserPassword=''
				this.newUserPasswordConfirm=''
				this.e1=1
			},
			saveNewuser(){
				this.newUserSaveButtonLoading = true
				var fD = new FormData()
				fD.append('name',this.newUserName)
				fD.append('email',this.newUserMail)
				fD.append('password',this.newUserPassword)
				fD.append('_method', 'PUT')
				axios.post('users/create',fD).then((response)=>{
					this.newUserSaveButtonLoading = false
					this.createDialog = false
					this.resetCreateForm()
					this.userCreateSb = false
					this.sbText = 'User Created Successfully'
					this.sbColor = 'success'
					this.userCreateSb = true
					this.loading = true
					this.getDataFromApi()
				}).catch((error)=> {
					this.newUserSaveButtonLoading = false
					var errors = error.response.data.errors
					Object.keys(errors).forEach((key)=>{
						this.emsg[key] = errors[key]
					})
					this.userCreateSb = false
					this.sbText = 'There is(are) error(s) in the form submitted. Please check!!'
					this.sbColor = 'error'
					this.userCreateSb = true
				})
			}
		}
	}
</script>