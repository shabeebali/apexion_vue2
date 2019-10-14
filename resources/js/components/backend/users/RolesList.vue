<template>
	<v-row>
		<v-col class="mx-4">
			<v-toolbar dense color="transparent" flat>
				<v-toolbar-title>User Roles</v-toolbar-title>
			 	<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn color="primary" dense depressed to="/users/roles/create">Create</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			<v-card class="mt-2">
				<v-card-text>
					<v-data-table :headers="headers" :items="items" :loading="loading">
					</v-data-table>
				</v-card-text>
			</v-card>
		</v-col>
	</v-row>
</template>
<script>
	export default{
		data(){
			return{
				loading:false,
				headers:[
					{
						'text':'ID',
						'value':'id'
					},
					{
						'text':'Name',
						'value':'name'
					},
					{
						'text':'E-mail',
						'value':'email'
					}
				],
				items:[]
			}
		},
		mounted(){
			this.loading = true
			this.getDataFromApi()
		},
		methods:{
			getDataFromApi(){
				axios.get('roles/list').then((response)=>{
					this.loading = false
					this.items = response.data.items
				})
			}
		}
	}
</script>