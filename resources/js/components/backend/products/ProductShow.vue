<template>
	<v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar id="top" dark color="primary">
				<v-toolbar-title>Product View: {{data.name}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click.stop="closeDialog">Close</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
	        	<v-row>
	        		<v-col cols="12" md="6">
	        			<v-row>
	        				<v-col cols="12">
			        			<v-simple-table>
		    						<template v-slot:default>
		    							 <thead>
											<tr>
												<th class="text-left">Details</th>
												<th class="text-left"></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>ID</td>
												<td>{{data.id}}</td>
											</tr>
											<tr>
												<td>Name</td>
												<td>{{data.name}}</td>
											</tr>
											<tr>
												<td>SKU</td>
												<td>{{data.sku}}</td>
											</tr>
											<tr>
												<td>HSN Code</td>
												<td>{{data.hsn}}</td>
											</tr>
											<tr>
												<td>MRP</td>
												<td><v-icon>mdi-currency-inr</v-icon> {{data.mrp}}</td>
											</tr>
											<tr>
												<td>Landing Price</td>
												<td><v-icon>mdi-currency-inr</v-icon> {{data.landing_price}}</td>
											</tr>
											<tr>
												<td>GSP Customer</td>
												<td><v-icon>mdi-currency-inr</v-icon> {{data.gsp_customer}}</td>
											</tr>
											<tr>
												<td>GSP Dealer</td>
												<td><v-icon>mdi-currency-inr</v-icon> {{data.gsp_dealer}}</td>
											</tr>
											<tr>
												<td>GST</td>
												<td>{{data.gst}} %</td>
											</tr>
											<template v-if="data.alias.length > 0">
												<tr v-for="(item,index) in data.alias" :key="index">
													<td>Alias {{index+1}}</td>
													<td>{{data.alias[index].alias}}</td>
												</tr>
											</template>
											</tr>
										</tbody>
		    						</template>
		    					</v-simple-table>
		    				</v-col>
		    				<v-col cols="12">
			        			<v-simple-table>
		    						<template v-slot:default>
		    							<thead>
											<tr>
												<th class="text-left">Pricelist</th>
												<th class="text-left">Margin(%)</th>
												<th class="text-left">Price</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="item in data.pricelists">
												<td>{{item.name}}</td>
												<td>{{item.pivot.margin}} %</td>
												<td><v-icon>mdi-currency-inr</v-icon>{{computePrice(item.pivot.margin)}}</td>
											</tr>
										</tbody>
		    						</template>
		    					</v-simple-table>
		    				</v-col>
		    			</v-row>
    				</v-col>
    				<v-col cols="12" md="6">
    					<v-row>
    						<v-col cols="12">
	        					<v-carousel v-if="data.medias.length > 0">
									<v-carousel-item
									v-for="(item,i) in data.medias"
									:key="i"
									:src="baseUrl+'/'+item.url"
									reverse-transition="fade-transition"
									transition="fade-transition"
									></v-carousel-item>
								</v-carousel>
							</v-col>
    						
		    				<v-col cols="12">
			        			<v-simple-table>
		    						<template v-slot:default>
		    							<thead>
											<tr>
												<th class="text-left">Categories</th>
												<th class="text-left"></th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="item in data.categories">
												<td>{{item.taxonomy.name}}</td>
												<td>{{item.name}}</td>
											</tr>
										</tbody>
		    						</template>
		    					</v-simple-table>
		    				</v-col>
		    				<v-col cols="12">
			        			<v-simple-table>
		    						<template v-slot:default>
		    							<thead>
											<tr>
												<th class="text-left">Warehouses</th>
												<th class="text-left">Stock</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="item in data.warehouses">
												<td>{{item.name}}</td>
												<td>{{item.pivot.stock}}</td>
											</tr>
											<tr>
												<td>Total</td>
												<td>{{data.total_stock}}</td>
											</tr>
										</tbody>
		    						</template>
		    					</v-simple-table>
		    				</v-col>
		    			</v-row>
    				</v-col>
    			</v-row>
	        </v-card-text>
	    </v-card>
	    <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
			{{sbText}}
			<v-btn dark text @click.stop="snackbar = false"> Close</v-btn>
		</v-snackbar>
	</v-dialog>
</template>
<script>
	export default{
		watch:{
			dialog(){
				axios.get('products/'+this.pId).then((res)=>{
					this.data = res.data
				})
			}
		},
		props:['pId','dialog'],
		data(){
			return{
				sbText:'',
				sbTimeout:3000,
				sbColor:'',
				data:{
					medias:[],
					alias:[],
				},
				snackbar:false,
			}
		},
		computed:{
			baseUrl(){
				return window.base_url.content
			},
		},
		mounted(){
			
		},
		methods:{
			closeDialog(){
				this.$emit('close-dialog');
			},
			computePrice(el){
				return ((parseFloat(this.data.landing_price) * (1+(parseFloat(this.data.gst)/100)))*(1+(parseFloat(el)/100))).toFixed(2)
			}
		}
	}
</script>