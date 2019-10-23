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

						<v-stepper-step :complete="e1 > 2" step="2" :rules="[rules.categoryFormVal]" editable>Category</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step :complete="e1 > 3" step="3" :rules="[rules.plFormVal]" editable>Pricelist</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step :complete="e1 > 4" step="4" :rules="[rules.stockFormVal]" editable>Stock</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step :complete="e1 > 5" step="5" :rules="[rules.mediaFormVal]" editable>Media</v-stepper-step>

						<v-divider></v-divider>

						<v-stepper-step step="6" editable>Review</v-stepper-step>
					</v-stepper-header>

					<v-stepper-items>
						<v-stepper-content step="1">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formDetails" v-model="detailsFormVal">
										<v-row>
											<v-col>
												<v-text-field
													label="Name"
													v-model="fd.name.value"
													:rules="[rules.required]"
													:error-messages="fd.name.error"
													@keydown="fd.name.error = ''">
												</v-text-field>
											</v-col>
										</v-row>
										<v-row>
											<v-col cols="12" md="3">
												<v-text-field 
													label="HSN Code"
													v-model="fd.hsn.value">
												</v-text-field>
											</v-col>
											<v-col cols="12" md="3">
												<v-text-field 
													label="MRP"
													v-model.number="fd.mrp.value"
													:rules="[rules.price,rules.required]"
													:error-messages="fd.mrp.error"
													@keydown="fd.mrp.error = ''"
													prepend-inner-icon="mdi-currency-inr">
												</v-text-field>
											</v-col>
											<v-col cols="12" md="3">
												<v-text-field 
													label="GSP Customer"
													v-model.number="fd.gsp_customer.value"
													:rules="[rules.price,rules.required]"
													:error-messages="fd.gsp_customer.error"
													@keydown="fd.gsp_customer.error = ''"
													hint="General Selling Price Customer"
													persistent-hint
													prepend-inner-icon="mdi-currency-inr">
												</v-text-field>
											</v-col>
											<v-col cols="12" md="3">
												<v-text-field 
													label="GSP Dealer"
													v-model.number="fd.gsp_dealer.value"
													:rules="[rules.price,rules.required]"
													:error-messages="fd.gsp_dealer.error"
													@keydown="fd.gsp_dealer.error = ''"
													hint="General Selling Price Dealer"
													persistent-hint
													prepend-inner-icon="mdi-currency-inr">
												</v-text-field>
											</v-col>
										</v-row>
										<v-row>
											<v-col cols="12" md="3">
												<v-text-field 
													label="Weight"
													v-model.number="fd.weight.value"
													:rules="[rules.weight,rules.required]"
													:error-messages="fd.weight.error"
													@keydown="fd.weight.error = ''"
													hint="weight in grams"
													persistent-hint
													suffix="grams">
												</v-text-field>
											</v-col>
											<v-col cols="12" md="3">
												<v-text-field 
													label="Landing Price"
													v-model.number="fd.landing_price.value"
													:rules="[rules.price,rules.required]"
													:error-messages="fd.landing_price.error"
													@keydown="fd.landing_price.error = ''"
													prepend-inner-icon="mdi-currency-inr">
												</v-text-field>
											</v-col>
											<v-col cols="12" md="3">
												<v-select
													label="GST"
													v-model="fd.gst.value"
													suffix="%"
													:items="fd.gst.items"
													:rules="[rules.required]">
												</v-select>
											</v-col>
											<v-col cols="12" md="3"></v-col>
										</v-row>
										<v-row>
											<v-col cols="12" md="4">
												<v-text-field v-for="(item,index) in aliases" :key="index" :label="aliases[index].label" v-model="aliases[index].value" :error-messages="aliases[index].error"
												@keydown="aliases[index].error = ''" :append-outer-icon="aliases.length > 1 ? 'mdi-minus-circle':''" v-on:click:append-outer="deleteAlias(index)">
												</v-text-field>
												<v-btn text @click.stop="addAlias">Add Alias</v-btn>
											</v-col>
										</v-row>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=2">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
						<v-stepper-content step="2">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formCategory" v-model="categoryFormVal">
										<v-row>
											<v-col cols="12" md="4"></v-col>
											<v-col cols="12" md="4">
												<template v-for="(item,index) in taxonomies">
													<v-select 
														:label="taxonomies[index].name"
														:items="taxonomies[index].categories"
														item-text="name"
														item-value="id"
														v-model="taxonomies[index].value"
														:rules="[rules.required]"></v-select>
												</template>
											</v-col>
											<v-col cols="12" md="4"></v-col>
										</v-row>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=3">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
						<v-stepper-content step="3">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formPl" v-model="plFormVal">
										<v-row>
											<v-col cols="12" md="4"></v-col>
											<v-col cols="12" md="4">
												<template v-for="(item,index) in pricelists">
													<v-row>
														<v-col cols="6">
															<v-text-field
																v-model.number="pricelists[index].value"
																:label="pricelists[index].name"
																:rules="[rules.price,rules.required]"
																hint="Margin"
																persistent-hint
																suffix="%">
															</v-text-field>
														</v-col>
														<v-col cols="6">
															<v-text-field
																:value="calculatePrice(pricelists[index].value)"
																readonly
																hint="Estimated Selling Price"
																persistent-hint>
															</v-text-field>
														</v-col>
													</v-row>
												</template>
											</v-col>
											<v-col cols="12" md="4"></v-col>
										</v-row>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=4">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
						<v-stepper-content step="4">
							<v-card class="pt-4">
								<v-card-text>
									<v-form ref="formStock" v-model="stockFormVal">
										<v-row>
											<v-col cols="12" md="4"></v-col>
											<v-col cols="12" md="4">
												<template v-for="(item,index) in warehouses">
													<v-text-field 
														:label="'Warehouse: ' + warehouses[index].name"
														v-model="warehouses[index].value"
														:rules="[rules.whole,rules.required]"></v-text-field>
												</template>
											</v-col>
											<v-col cols="12" md="4"></v-col>
										</v-row>
									</v-form>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=5">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
						<v-stepper-content step="5">
							<v-card class="pt-4">
								<v-card-text>
									<v-row>
										<v-col cols="12" md="4">
											<v-form v-model="mediaFormVal" ref="formMedia">
												<v-file-input 
													prepend-icon="mdi-camera" 
													v-model="imgFile"
			    									accept="image/png, image/jpeg, image/bmp"
			    									placeholder="Select an image to upload"
			    									hint="Supported formats: png, jpg, bmp"
			    									persistent-hint
			    									append-outer-icon="mdi-cloud-upload"
			    									@click:append-outer="uploadImg"></v-file-input>
		    								</v-form>
										</v-col>
										<v-col cols="12" md="4"></v-col>
										<v-col cols="12" md="4"></v-col>
									</v-row>
									<v-row>
										<v-col cols="6" md="2" v-for="(url,index) in medias" :key="index">
											<v-img max-height="250" max-width="250" :src="baseUrl+'/'+url" @click.stop="imgModal(baseUrl+'/'+url)" style="cursor:pointer"></v-img>
											<v-btn text color="info" @click="deleteMedia(url)">Delete</v-btn>
										</v-col>
									</v-row>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" @click.stop="e1=6">Continue</v-btn>
									<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
								</v-card-actions>
							</v-card>
						</v-stepper-content>
						<v-stepper-content step="6">
							<v-card class="pt-4">
								<v-card-text>
									<v-row>
						        		<v-col cols="12" md="6">
						        			<v-row>
						        				
						        				<v-col cols="12">
								        			<v-simple-table>
							    						<template v-slot:default>
							    							 <thead>
																<tr>
																	<th class="text-left">Details</th>
																	<th class="text-left"></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td>Name</td>
																	<td>{{fd.name.value}}</td>
																</tr>
																<tr>
																	<td>HSN Code</td>
																	<td>{{fd.hsn.value}}</td>
																</tr>
																<tr>
																	<td>MRP</td>
																	<td><v-icon>mdi-currency-inr</v-icon> {{fd.mrp.value}}</td>
																</tr>
																<tr>
																	<td>Landing Price</td>
																	<td><v-icon>mdi-currency-inr</v-icon> {{fd.landing_price.value}}</td>
																</tr>
																<tr>
																	<td>GSP Customer</td>
																	<td><v-icon>mdi-currency-inr</v-icon> {{fd.gsp_customer.value}}</td>
																</tr>
																<tr>
																	<td>GSP Dealer</td>
																	<td><v-icon>mdi-currency-inr</v-icon> {{fd.gsp_dealer.value}}</td>
																</tr>
																<tr>
																	<td>GST</td>
																	<td>{{fd.gst.value}} %</td>
																</tr>
																<template v-if="aliases.length > 0">
																	<tr v-for="(item,index) in aliases" :key="index">
																		<td>Alias {{index+1}}</td>
																		<td>{{aliases[index].value}}</td>
																	</tr>
																</template>
																</tr>
															</tbody>
							    						</template>
							    					</v-simple-table>
							    				</v-col>
							    				<v-col cols="12">
								        			<v-simple-table>
							    						<template v-slot:default>
							    							<thead>
																<tr>
																	<th class="text-left">Pricelist</th>
																	<th class="text-left">Margin(%)</th>
																	<th class="text-left">Price</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="item in pricelists">
																	<td>{{item.name}}</td>
																	<td>{{item.value}} %</td>
																	<td><v-icon>mdi-currency-inr</v-icon>{{calculatePrice(item.value)}}</td>
																</tr>
															</tbody>
							    						</template>
							    					</v-simple-table>
							    				</v-col>
							    			</v-row>
					    				</v-col>
					    				<v-col cols="12" md="6">
					    					<v-row>
					    						<v-col cols="12">
						        					<v-carousel v-if="medias.length > 0">
														<v-carousel-item
														v-for="(item,i) in medias"
														:key="i"
														:src="baseUrl+'/'+item"
														reverse-transition="fade-transition"
														transition="fade-transition"
														></v-carousel-item>
													</v-carousel>
												</v-col>
					    						
							    				<v-col cols="12">
								        			<v-simple-table>
							    						<template v-slot:default>
							    							<thead>
																<tr>
																	<th class="text-left">Categories</th>
																	<th class="text-left"></th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="item in taxonomies">
																	<td>{{item.name}}</td>
																	<td>{{getCatName(item)}}</td>
																</tr>
															</tbody>
							    						</template>
							    					</v-simple-table>
							    				</v-col>
							    				<v-col cols="12">
								        			<v-simple-table>
							    						<template v-slot:default>
							    							<thead>
																<tr>
																	<th class="text-left">Warehouses</th>
																	<th class="text-left">Stock</th>
																</tr>
															</thead>
															<tbody>
																<tr v-for="item in warehouses">
																	<td>{{item.name}}</td>
																	<td>{{item.value}}</td>
																</tr>
																<tr>
																	<td>Total</td>
																	<td>{{getTotalStock}}</td>
																</tr>
															</tbody>
							    						</template>
							    					</v-simple-table>
							    				</v-col>
							    			</v-row>
					    				</v-col>
					    			</v-row>
								</v-card-text>
								<v-card-actions>
									<v-btn color="primary" :loading="btnloading" @click.stop="save"
										>{{submitTxt}}</v-btn>
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
	    <v-dialog v-model ="imgDialog" width="700">
	    	<v-card>
	    		<v-img :src="imgUrl" :lazy-src="imgUrl"></v-img>
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
			axios.get('taxonomies?withcat=1').then((res)=>{
				this.taxonomies = res.data.data
			})
			axios.get('pricelists').then((res)=>{
				var data = res.data.data
				data.forEach((item,index)=>{
					data[index].value = '0'
				})
				this.pricelists = data
			})
			axios.get('warehouses').then((res)=>{
				var data = res.data.data
				data.forEach((item,index)=>{
					data[index].value = '0'
				})
				this.warehouses = data
			})
		},
		computed:{
			baseUrl(){
				return window.base_url.content
			},
			getTotalStock(){
				var total = 0
				this.warehouses.forEach((item)=>{
					total = total + parseInt(item.value)
				})
				return total
			}
		},
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Product'
					this.e1=1
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Product'
					this.passFormVal = true
					axios.get('products/'+this.pId).then((response)=>{
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
			plFormVal:{
				handler(){
					this.$refs.formPl.validate()
				},
				deep:true
			},
			categoryFormVal:{
				handler(){
					this.$refs.formCategory.validate()
				},
				deep:true
			},
			stockFormVal:{
				handler(){
					this.$refs.formStock.validate()
				},
				deep:true
			},
			mediaFormVal:{
				handler(){
					this.$refs.formMedia.validate()
				},
				deep:true
			},
		},
		props:['mode','dialog','pId'],
		data(){
			return{
				e1:1,
				imgFile:null,
				submitTxt:'',
				formTitle:'',
				closeConfirm:false,
				waitDialog:false,
				imgDialog:false,
				btnloading:false,
				detailsFormVal:null,
				categoryFormVal:null,
				stockFormVal:null,
				plFormVal:null,
				mediaFormVal:null,
				imgUrl:null,
				sbColor:'',
				sbText:'',
				sbTimeout:3000,
				snackbar:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					hsn:{
						value:'',
						error:'',
					},
					mrp:{
						value:'',
						error:'',
					},
					landing_price:{
						value:'',
						error:'',
					},
					gsp_customer:{
						value:'',
						error:'',
					},	
					gsp_dealer:{
						value:'',
						error:'',
					},
					weight:{
						value:'',
						error:'',
					},
					gst:{
						value:'',
						error:'',
						items:[
							{text:'5%', value:'5'},
							{text:'12%', value:'12'},
							{text:'18%', value:'18'},
						],
					},
				},
				aliases:[
					{label:'Alias 1',value:'',error:''}
				],
				taxonomies:[],
				pricelists:[],
				warehouses:[],
				medias:[],
				rules:{
					required: value=> !!value||'Required.',
					price: value => {
			            const pattern = /^\d{0,8}(\.\d{1,2})?$/
			            return pattern.test(value) || 'Invalid value.'
			        	},
			        weight: value => {
			            const pattern = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/
			            return pattern.test(value) || 'Invalid value.'
			        	},
			        whole: value => {
			            const pattern = /^\d+$/
			            return pattern.test(value) || 'Invalid value.'
			        	},
			        img: value => !value || value.size < 2000000 || 'Image size should be less than 2 MB!',
			        detailsFormVal: value=> this.detailsFormVal || 'Error',
			        categoryFormVal: value=> this.categoryFormVal || 'Error',
			        stockFormVal: value=> this.stockFormVal || 'Error',
			        plFormVal: value=> this.plFormVal || 'Error',
			        mediaFormVal: value=> this.mediaFormVal || 'Error',
				},
				taxonomy:{
					options:[],
				}
			}
		},
		methods:{
			getCatName(item){
				if(item.value){
					const val = item.value
					var grouped = _.groupBy(item.categories,'id')
					var index = Object.keys(grouped).indexOf(item.value.toString())
					return item.categories[index].name
				}
				return ''
			},
			addAlias(){
				var newLabel = 'Alias '+(this.aliases.length + 1)
				this.aliases.push({label:newLabel,value:'',erroe:''})
			},
			deleteAlias(index){
				this.aliases.splice(index,1)
				Object.keys(this.aliases).forEach((key,index)=>{
					this.aliases[key].label = 'Alias '+(index+1)
				})
			},
			deleteMedia(url){
				const index = this.medias.indexOf(url)
				if (index >= 0) this.medias.splice(index, 1)
			},
			calculatePrice(el){
				const val = ((parseFloat(this.fd.landing_price.value) * (1+(parseFloat(this.fd.gst.value)/100)))*(1+(parseFloat(el)/100))).toFixed(2)
				return isNaN(val) ? '-': val.toString()
			},
			uploadImg(){
				this.waitDialog = true
				var fD = new FormData()
				fD.append('file',this.imgFile)
				axios.post('/products/upload',fD,{
					headers: {
				        'Content-Type': 'multipart/form-data'
				    }
				}).then((response)=>{
					this.$refs.formMedia.reset()
					this.$refs.formMedia.resetValidation()
					this.waitDialog = false
					this.medias.push(response.data)
				}).catch((error)=>{
					this.$refs.formMedia.reset()
					this.$refs.formMedia.resetValidation()
					this.waitDialog = false
					this.emitSb('Something went wrong!','error')
				})
			},
			imgModal(url){
				this.imgUrl = url
				this.imgDialog = true
			},
			closeDialog(){
				this.$refs.formDetails.reset()
				this.$refs.formCategory.reset()
				this.$refs.formPl.reset()
				this.$refs.formStock.reset()
				this.$refs.formMedia.reset()
				this.aliases = [
					{label:'Alias 1',value:'',error:''}
				]
				this.medias = []
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
				this.$refs.formCategory.validate();
				this.$refs.formPl.validate();
				this.$refs.formStock.validate();
				this.$refs.formMedia.validate();
				if(this.detailsFormVal == false || this.categoryFormVal == false || this.plFormVal == false || this.stockFormVal  == false){
					this.emitSb('There are errors in the form submitted. Please check!!','error')
					this.btnloading = false
				}
				else{
					var fD = new FormData()
					Object.keys(this.fd).forEach((key)=>{
						fD.append(key,this.fd[key].value)
					})
					this.taxonomies.forEach((item)=>{
						fD.append(item.slug,item.value)
					})
					this.pricelists.forEach((item)=>{
						fD.append(item.slug,item.value)
					})
					this.warehouses.forEach((item)=>{
						fD.append(item.slug,item.value)
					})
					fD.append('medias',this.medias)
					var aliasArr = []
					this.aliases.forEach((item)=>{
						aliasArr.push(item.value)
					})
					fD.append('aliases',JSON.stringify(aliasArr))
					if(this.mode == 'edit'){
						fD.append('_method','PUT')
						var route = 'products/'+this.pId
					}
					else{
						var route = 'products'
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
							var errors = error.response.data.errors
							Object.keys(errors).forEach((key)=>{
								this.fd[key].error = errors[key]
							})
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
