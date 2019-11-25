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
	        	<v-form v-model="form1Val" ref="form1">
					<v-row class="mx-4">
		        		<v-col cols="12" md="4">
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
						<v-col cols="12" md="8"></v-col>
					</v-row>
					<v-row class="mx-4">
						<v-col cols="12"><div class="headline">Permissions</div></v-col>
						<v-col cols="12">
							<v-btn color="info" @click="fd.permissions = allPermissions">Select All</v-btn>
							<v-btn coloe='error' @click="fd.permissions = ''">Deselect All</v-btn>
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
								<template  v-slot:item.tally="{item}">
									<v-checkbox v-if="item.tally" v-model="fd.permissions" :value="item.tally"></v-checkbox>
								</template>
							</v-data-table>
						</v-col>
					</v-row>
				</v-form>
			</v-card-text>
			<v-card-actions>
				<v-row class="mx-4">
					<v-col>
						<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="saveNewRole()">{{submitTxt}}</v-btn>
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
					this.formTitle = 'Create Role'
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Role'
					this.passFormVal = true
					axios.get('users_roles/'+this.roleId).then((response)=>{
						var dd = response.data.data
						this.fd.name.value = dd.name
						this.fd.permissions = dd.permissions
					})
				}
			}
		},
		mounted(){
			this.formLoading = true
			axios.get('users/roles/permissions').then((res)=>{
				this.formItems = res.data.data
				this.allPermissions = res.data.permissions
				this.formLoading = false
			})
		},
		props:['mode','dialog','roleId'],
		data(){
			return{
				snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
				submitTxt:'',
				formTitle:'',
				formLoading:false,
				allPermissions:[],
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
					{
						text:'Update to Tally',
						value:'tally'
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
			triggerSb(val){
				this.snackbar = false
				this.sbText = val.text
				this.sbColor = val.color
				this.snackbar = true
			},
			saveNewRole(){
				this.btnloading = true
				var fD = new FormData()
				fD.append('name',this.fd.name.value)
				fD.append('permissions',this.fd.permissions)
				if(this.mode == 'edit'){
					fD.append('_method','PUT')
					var route = 'users_roles/'+this.roleId
				}
				else{
					var route = 'users_roles'
				}
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					if(this.mode == 'edit'){
						this.emitSb('Role Updated Successfully','success')
					}
					else{
						this.emitSb('Role Created Successfully','success')
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
