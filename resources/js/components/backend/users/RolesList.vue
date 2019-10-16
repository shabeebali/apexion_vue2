<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>User Roles</v-toolbar-title>
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
					<v-toolbar-title>Create Role</v-toolbar-title>
					<div class="flex-grow-1"></div>
					<v-toolbar-items>
						<v-btn text @click="resetCreateForm(); createDialog = false">Cancel</v-btn>
					</v-toolbar-items>
		        </v-toolbar>
		        <v-card-text>
		        	<v-row>
		        		<v-col cols="12" md="4"></v-col>
		        		<v-col cols="12" md="4">
							<v-card class="mb-12"height="200px">
								<v-card-text>
									<v-form v-model="nrf1val">
										<v-text-field 
											v-model="nrfd.name.value" 
											label="Name" 
											autofocus 
											:error-messages="nrfd.name.error"
											@keydown="nrfd.name.error = ''"
											:rules=[rules.required]
										></v-text-field>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" :disabled="nrf1val == false" :loading="nrslb" @click="saveNewRole()">Save</v-btn>
									<v-btn text @click="resetCreateForm(); createDialog = false">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-col>
						<v-col cols="12" md="4"></v-col>
					</v-row>
		        </v-card-text>
		    </v-card>
		</v-dialog>
		<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout">
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-row>
</template>
<script>
	export default{
		data(){
			return{
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				nrslb:false,
				nrf1val:false,
				nrfd:{
					name:{
						'error':'',
						'value':''
					},
				},
				rules:{
					required: value=> !!value||'Required.',
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
				axios.get('users_roles').then((response)=>{
					this.loading = false
					this.items = response.data
				})
			},
			resetCreateForm(){
				Object.keys(this.nrfd).forEach((key)=>{
					this.nrfd[key].value = ''
				})
			},
			saveNewRole(){
				this.nrslb = true
				var fD = new FormData()
				Object.keys(this.nrfd).forEach((key)=>{
					fD.append(key,this.nrfd[key].value)
				})
				axios.post('users_roles',fD).then((response)=>{
					this.nrslb = false
					this.createDialog = false
					this.resetCreateForm()
					this.snackbar = false
					this.sbText = 'User Created Successfully'
					this.sbColor = 'success'
					this.snackbar = true
					this.loading = true
					this.getDataFromApi()
				}).catch((error)=> {
					this.nrslb = false
					var errors = error.response.data.errors
					Object.keys(errors).forEach((key)=>{
						this.nrfd[key].error = errors[key]
					})
					this.snackbar = false
					this.sbText = 'There is(are) error(s) in the form submitted. Please check!!'
					this.sbColor = 'error'
					this.snackbar = true
				})
			}
		}
	}
</script>