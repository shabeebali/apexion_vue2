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
	    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		watch:{
			dialog:function(){
				this.$vuetify.goTo(0)
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Pricelist'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Pricelist'
					this.passFormVal = true
					axios.get('pricelists/'+this.plId).then((response)=>{
						var dd = response.data.data
						this.fd.name.value = dd.name
					})
				}
			}
		},
		props:['mode','dialog','plId'],
		data(){
			return{
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
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
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
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
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'pricelists/'+this.plId
				}
				else{
					var route = 'pricelists'
				}
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					if(this.mode == 'edit'){
						this.emitSb('Pricelist Updated Successfully','success')
					}
					else{
						this.emitSb('Pricelist Created Successfully','success')
					}
					this.closeDialog()
					this.$emit('update-list')
				}).catch((error)=> {
					if(error.response.status == 422){
						this.btnloading = false
						var errors = error.response.data.errors
						this.fd.name.error = errors.name
						this.triggerSb({text:'There are errors in the form submitted. Please check!!',color:'error'})
					}
					if(error.response.status == 403){
						this.btnloading = false
						this.triggerSb({text:'You are not authorised to do this action',color:'error'})
					}
				})
			}
		}
	}
</script>
