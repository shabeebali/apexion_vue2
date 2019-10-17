<template>
	<v-dialog v-model="dialog" persistent width="290" transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar id="top" dark color="primary">
				<v-toolbar-title>{{formTitle}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click="closeDialog">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
	        	<v-form v-model="form1Val" ref="form1">
					<v-row class="mx-4">
		        		<v-col cols="12" >
							<v-text-field 
								v-model="fd.name.value" 
								label="Name" 
								v-if="dialog" 
								autofocus
								:error-messages="fd.name.error"
								@keydown="fd.name.error = ''"
								:rules=[rules.required]
							></v-text-field>
						</v-col>
					</v-row>
				</v-form>
			</v-card-text>
			<v-card-actions>
				<v-row class="mx-4">
					<v-col>
						<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="save()">{{submitTxt}}</v-btn>
						<v-btn text @click="closeDialog">Cancel</v-btn>
					</v-col>
				</v-row>
			</v-card-actions>
	    </v-card>
	</v-dialog>
</template>
<script>
	export default{
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Warehouse'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Warehouse'
					this.passFormVal = true
					axios.get('warehouses/'+this.whId).then((response)=>{
						var dd = response.data.data
						this.fd.name.value = dd.name
					})
				}
			}
		},
		props:['mode','dialog','whId'],
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
				},
				rules:{
					required: value=> !!value||'Required.',
				},
			}
		},
		methods:{
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
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'warehouses/'+this.plId
				}
				else{
					var route = 'warehouses'
				}
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					if(this.mode == 'edit'){
						this.emitSb('Warehouse Updated Successfully','success')
					}
					else{
						this.emitSb('Warehouse Created Successfully','success')
					}
					this.closeDialog()
					this.$emit('update-list')
				}).catch((error)=> {
					if(error.response.status == 422){
						this.btnloading = false
						var errors = error.response.data.errors
						this.fd.name.error = errors.name
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
