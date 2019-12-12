@extends('layouts.app')
@section('content')
<v-card>
    <v-card-title>Create Role</v-card-title>
    <v-card-text>
    	<v-form v-model="form1Val" ref="form1" v-on:submit.prevent="">
			<v-row class="mx-4">
        		<v-col cols="12" md="4">
					<v-text-field 
						v-model="fd.name.value" 
						label="Name" 
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
				<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="saveNewRole()">Save</v-btn>
				<v-btn text href="{{$prev_url}}">Cancel</v-btn>
			</v-col>
		</v-row>
	</v-card-actions>
</v-card>
<v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
	@{{sbText}}
	<v-btn dark text @click="snackbar = false"> Close</v-btn>
</v-snackbar>
<v-dialog v-model="waitDialog" persistent width="300">
	<v-card color="primary" dark>
		<v-card-text>
			Please stand by
			<v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
		</v-card-text>
	</v-card>
</v-dialog>
@endsection
@section('script')
<script>
    new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                waitDialog : false,
                snackbar:false,
				sbTimeout:3000,
				sbText:'',
				sbColor:'',
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
        mounted(){
        	this.waitDialog = true
        	this.formLoading = true
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
            axios.get('users/roles/permissions').then((res)=>{
            	axios.get('users_roles/{{$id}}').then((response)=>{
					var dd = response.data.data
					this.fd.name.value = dd.name
					this.fd.permissions = dd.permissions
					this.waitDialog = false
					this.formLoading = false
				})
				this.formItems = res.data.data
				this.allPermissions = res.data.permissions
			})
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
			emitSb(text,color){
				window.localStorage.setItem('message',text)
				window.localStorage.setItem('message_status',color)
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
				fD.append('_method','PUT')
				var route = 'users_roles/{{$id}}'
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					this.emitSb('Role Updated Successfully','success')
					window.location.href = '{{$prev_url}}'
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
    }).$mount('#app')
</script>

@endsection