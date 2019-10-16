<template>
	<v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar dark color="primary">
				<v-toolbar-title>Create Role</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click="closeDialog">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
				<v-row class="mx-4">
	        		<v-col cols="12" md="4">
						<v-form v-model="form1Val" ref="form1">
							<v-text-field 
								v-model="fd.name.value" 
								label="Name" 
								autofocus 
								:error-messages="fd.name.error"
								@keydown="fd.name.error = ''"
								:rules=[rules.required]
							></v-text-field>
						</v-form>
					</v-col>
					<v-col cols="12" md="8"></v-col>
				</v-row>
				<v-row class="mx-4">
					<v-col cols="12"><div class="headline">Permissions</div></v-col>
					<v-col cols="12">
						<v-data-table
							:headers="formHeaders"
							:items="formItems"
							:loading="formLoading"
							disable-filtering
							disable-sort
							disable-pagination
							hide-default-footer>
							<template v-slot:item.create="{item}">
								<v-checkbox v-model="fd.permissions" :value="item.create"></v-checkbox>
							</template>
							<template v-slot:item.edit="{item}">
								<v-checkbox v-model="fd.permissions" :value="item.edit"></v-checkbox>
							</template>
							<template v-slot:item.delete="{item}">
								<v-checkbox v-model="fd.permissions" :value="item.delete"></v-checkbox>
							</template>
							<template v-slot:item.view="{item}">
								<v-checkbox v-model="fd.permissions" :value="item.view"></v-checkbox>
							</template>
							<template  v-slot:item.approve="{item}">
								<v-checkbox v-if="item.approve" v-model="fd.permissions" :value="item.approve"></v-checkbox>
							</template>
						</v-data-table>
					</v-col>
				</v-row>
			</v-card-text>
			<v-card-actions>
				<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="saveNewRole()">Save</v-btn>
				<v-btn text @click="closeDialog">Cancel</v-btn>
			</v-card-actions>
	    </v-card>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			this.formLoading = true
			axios.get('users/roles/permissions').then((res)=>{
				this.formItems = res.data
				this.formLoading = false
			})
		},
		props:['mode','dialog'],
		data(){
			return{
				formLoading:false,
				formHeaders:[
					{
						text:'',
						value:'model'
					},
					{
						text:'Create',
						value:'create'
					},
					{
						text:'Edit',
						value:'edit'
					},
					{
						text:'View',
						value:'view'
					},
					{
						text:'Delete',
						value:'delete'
					},
					{
						text:'Approve',
						value:'approve'
					},
				],
				formItems:[],
				btnloading:false,
				form1Val:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					permissions:[],
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
			resetCreateForm(){
				Object.keys(this.nrfd).forEach((key)=>{
					this.nrfd[key].value = ''
				})
			},
			saveNewRole(){
				this.btnloading = true
				var fD = new FormData()
				fD.append('name',fd.name.value)
				fD.append('permissions',fd.permissions)
				axios.post('users_roles',fD).then((response)=>{
					this.btnloading = false
					this.emitSb('Role Created Successfully','success')
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
