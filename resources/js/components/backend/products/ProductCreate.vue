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
			</v-card-text>
	    </v-card>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			this.updateTaxonomy()
		},
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Category'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Category'
					this.passFormVal = true
					axios.get('categories/'+this.catId).then((response)=>{
						var dd = response.data.data
						this.fd.name.value = dd.name
						this.fd.taxonomy.value = dd.taxonomy_id
						this.fd.code.value = dd.code
					})
				}
			}
		},
		props:['mode','dialog','catId'],
		data(){
			return{
				submitTxt:'',
				formTitle:'',
				btnloading:false,
				form1Val:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					taxonomy:{
	                    value:''
	                },
	                code:{
	                    value:'',
	                    error:'',
	                    size:1,
	                    disable:false,
	                },
				},
				rules:{
					required: value=> !!value||'Required.',
				},
				taxonomy:{
					options:[],
				}
			}
		},
		methods:{
			updateTaxonomy(){
				axios.get('taxonomies').then((res)=>{
					this.taxonomy.options = res.data.data
				})
			},
			sizeUpd(){
				var arr = this.taxonomy.options
				this.fd.code.value = ''
				arr.forEach((item)=>{
					if(item.id == this.fd.taxonomy.value){
						this.fd.code.size = item.code_length
						if(item.in_pc != 1){
							this.fd.code.disable = true
						}
						else if(item.autogen == 1){
							this.fd.code.value = item.next_code
							this.fd.code.disable = true
						}
						else{
							this.fd.code.disable = false
						}
					}
				})
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
			save(){
				this.btnloading = true
				var fD = new FormData()
				fD.append('name',this.fd.name.value)
				fD.append('taxonomy_id', this.fd.taxonomy.value)
				fD.append('code', this.fd.code.value)
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'categories/'+this.txId
				}
				else{
					var route = 'categories'
				}
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					if(this.mode == 'edit'){
						this.emitSb('Category Updated Successfully','success')
					}
					else{
						this.updateTaxonomy()
						this.emitSb('Category Created Successfully','success')
					}
					this.closeDialog()
					this.$emit('update-list')
				}).catch((error)=> {
					if(error.response.status == 422){
						this.btnloading = false
						var errors = error.response.data.errors
						this.fd.name.error = errors.name
						this.fd.code.error = errors.code
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
</script>
