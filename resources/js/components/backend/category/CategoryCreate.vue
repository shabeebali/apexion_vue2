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
							<v-select label="Taxonomy" 
								v-model="fd.taxonomy.value" 
								:items="taxonomy.options"
								item-text="name"
								item-value="id"
								:rules=[rules.required]
								:disabled="mode == 'edit'"
								v-on:change="sizeUpd"></v-select>
							<v-text-field
								v-model="fd.code.value"
								:maxlength="fd.code.size"
								label="Code"
								:disabled="fd.code.disable"
								:error-messages="fd.code.error"
								@keydown="fd.code.error = ''">
							</v-text-field>
						</v-form>
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
	    <v-snackbar bottom right v-model="snackbar" timeout=3000 :color="sbColor">{{sbText}} <v-btn text @click="snackbar = false">CLOSE</v-btn></v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			
		},
		watch:{
			dialog:function(){
				this.updateTaxonomy()
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
						this.fd.code.disable = dd.taxonomy.autogen == 1 ? true : false
					})
				}
			}
		},
		props:['mode','dialog','catId'],
		data(){
			return{
				snackbar:false,
				sbColor:'',
				sbText:'',
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
			triggerSb(text,color){
				this.sbText = text
				this.sbColor = color
				this.snackbar = true
			},
			save(){
				this.btnloading = true
				var fD = new FormData()
				fD.append('name',this.fd.name.value)
				fD.append('taxonomy_id', this.fd.taxonomy.value)
				fD.append('code', this.fd.code.value)
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'categories/'+this.catId
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
						this.triggerSb('There are errors in the form submitted. Please check!!','error')
					}
					if(error.response.status == 403){
						this.btnloading = false
						this.triggerSb('You are not authorised to do this action','error')
					}
				})
			}
		}
	}
</script>
