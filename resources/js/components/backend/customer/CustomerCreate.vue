<template>
	<v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar id="top" dark color="primary">
				<v-toolbar-title>{{formTitle}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
	        	<v-stepper class="mt-4" v-model="e1">
					<v-stepper-header>
						<v-stepper-step :complete="e1 > 1" step="1" :rules="[rules.detailsFormVal]" editable>Details</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step :complete="e1 > 2" step="2" :rules="[rules.addressFormVal]" editable>Address</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step step="3" editable>Review</v-stepper-step>
					</v-stepper-header>
					<v-stepper-items>
						<v-stepper-content step="1">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formDetails" v-model="detailsFormVal">
										<v-text-field label="Name" v-model="fd.name.value" :error-messages="fd.name.error" @keydown="fd.name.error=''" :rules="[rules.required]"></v-text-field>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=2">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
					</v-stepper-items>
					<v-stepper-items>
						<v-stepper-content step="2">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formAddress" v-model="addressFormVal">
										<template v-for="(address,index) in fd.addresses">
											<v-card class="ma-4">
												<v-toolbar flat>
													<v-toolbar-title>
														Address {{index+1}}
													</v-toolbar-title>
													<div class="flex-grow-1"></div>
													<v-toolbar-items>
														<v-btn v-if="fd.addresses.length > 1" text @click.stop="deleteAddress(index)">Remove</v-btn>
													</v-toolbar-items>
												</v-toolbar>
												<v-card-text>
													<v-row>
														<v-col cols="12">
															<v-text-field label="Tag Name" :rules="[rules.required]" v-model="fd.addresses[index].tag_name"></v-text-field>
														</v-col>
														<v-col cols="12">
															<v-text-field label="Line1" :rules="[rules.required]" v-model="fd.addresses[index].line1"></v-text-field>
														</v-col>
														<v-col cols="12">
															<v-text-field label="Line2" v-model="fd.addresses[index].line2"></v-text-field>
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
														<v-col cols="6" md="4">
															<v-menu
																v-model="date_menu"
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
																<v-date-picker v-model="fd.addresses[index].init_bal_date"  @input="date_menu = false">
																</v-date-picker>
															</v-menu>
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
												</v-card-text>
											</v-card>
										</template>
										<v-btn text @click="addAddress">Add Address</v-btn>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=3">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
					</v-stepper-items>
					<v-stepper-items>
						<v-stepper-content step="3">
							<v-card class="pt-4">
								<v-card-text>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="save">Save</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
					</v-stepper-items>
				</v-stepper>
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
	    <v-dialog v-model="waitDialog" persistent width="300">
			<v-card color="primary" dark>
				<v-card-text>
					Please stand by
					<v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
				</v-card-text>
			</v-card>
	    </v-dialog>
	    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click.stop="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			axios.get('places').then((res)=>{
				this.countries = res.data.countries
				this.phone_countries = res.data.phone_countries
			})
		},
		computed:{
			baseUrl(){
				return window.base_url.content
			},
		},
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Customer'
					this.e1=1
					this.fd = {
						name:{
							value:'',
							error:'',
						},
						addresses:[
							{
								line1:'',
								line2:'',
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
								]
							}
						]
					}
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Customer'
					this.passFormVal = true
					axios.get('customers/'+this.cId).then((response)=>{
						var dd = response.data.data
					})
				}
			},
			detailsFormVal:{
				handler(){
					this.$refs.formDetails.validate()
				},
				deep:true
			},
			addressFormVal:{
				handler(){
					this.$refs.formAddress.validate()
				},
				deep:true
			},

		},
		props:['mode','dialog','cId'],
		data(){
			return{
				stateLoading:false,
				cityLoading:false,
				e1:1,
				date_menu:false,
				submitTxt:'',
				formTitle:'',
				closeConfirm:false,
				waitDialog:false,
				btnloading:false,
				detailsFormVal:null,
				addressFormVal:null,
				sbColor:'',
				sbText:'',
				sbTimeout:3000,
				snackbar:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					addresses:[
						{
							line1:'',
							line2:'',
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
							]
						}
					]
				},
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
			        detailsFormVal: value=> this.detailsFormVal || 'Error',
			        addressFormVal: value=> this.addressFormVal || 'Error',
				},
			}
		},
		methods:{
			updState(index){
				this.stateLoading = true
				axios.get('places?country_id='+this.fd.addresses[index].country_id).then((res)=>{
					this.fd.addresses[index].states = res.data
					this.fd.addresses[index].state_id = 0
					this.stateLoading = false
					this.updCity(index)
				})
			},
			updCity(index){
				if(this.fd.addresses[index].state_id != 0){
					this.cityLoading = true
					axios.get('places?state_id='+this.fd.addresses[index].state_id).then((res)=>{
						this.fd.addresses[index].cities = res.data
						this.fd.addresses[index].city_id = 0
						this.cityLoading = false
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
					line1:'',
					line2:'',
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
					]
				})
			},
			deletePhone(index,index2){
				this.fd.addresses[index].phones.splice(index2,1)
			},
			deleteAddress(index){
				this.fd.addresses.splice(index,1)
			},
			closeDialog(){
				this.$refs.formDetails.reset()
				this.$refs.formAddress.reset()
				this.$emit('close-dialog')
			},
			emitSb(text,color){
				this.sbText = text
				this.sbColor = color
				this.snackbar = true
			},
			save(){
				this.btnloading = true
				this.$refs.formDetails.validate();
				this.$refs.formAddress.validate();
				if(this.detailsFormVal == false || this.addressFormVal == false ){
					this.emitSb('There are errors in the form submitted. Please check!!','error')
					this.btnloading = false
				}
				else{
					var fD = new FormData()
					fD.append('name',this.fd.name.value)
					fD.append('addresses',JSON.stringify(this.fd.addresses))
					if(this.mode == 'edit'){
						fD.append('_method','PUT')
						var route = 'customers/'+this.cId
					}
					else{
						var route = 'customers'
					}
					axios.post(route,fD).then((response)=>{
						this.btnloading = false
						if(this.mode == 'edit'){
							this.$emit('trigger-sb',{text:'Category Updated Successfully',color:'success'})
						}
						else{
							this.$emit('trigger-sb',{text:'Category Created Successfully',color:'success'})
						}
						this.closeDialog()
						this.$emit('update-list')
					}).catch((error)=> {
						if(error.response.status == 422){
							this.btnloading = false
							this.emitSb('There are errors in the form submitted. Please check!!','error')
						}
						if(error.response.status == 403){
							this.btnloading = false
							this.emitSb('You are not authorised to do this action','error')
						}
					})
				}
			}
		}
	}
</script>
