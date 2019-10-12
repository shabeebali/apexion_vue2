<template>
	<div>
		<page-header></page-header>
		<v-data-table :headers="headers" :items="items">
			<template v-slot:no-data>
		      <v-alert type="info"> No data Availabele </v-alert>
		    </template>
		</v-data-table>
	</div>
</template>
<script>
	import pageHeader from '../common/pageHeader.vue'
	export default{
		components:{
			'page-header':pageHeader
		},
		data(){
			return{
				headers:[
					{
						text:'#',
						value:'id',
					},
					{
						text:'Name',
						value:'name',
					}
				],
				items:[]
			}
		}
	}
</script>