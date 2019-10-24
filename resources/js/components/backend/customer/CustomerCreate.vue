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
						fD.append('taxonomy_'+item.slug,item.value)
					})
					this.pricelists.forEach((item)=>{
						fD.append('pricelist_'+item.slug,item.value)
					})
					this.warehouses.forEach((item)=>{
						fD.append('warehouse_'+item.slug,item.value)
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
