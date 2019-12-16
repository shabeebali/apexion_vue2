<template>
	<v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
	        <v-toolbar id="top" dark color="primary">
				<v-toolbar-title>{{formTitle}}</v-toolbar-title>
				<div class="flex-grow-1"></div>
				<v-toolbar-items>
					<v-btn text @click.stop="closeConfirm = true">Cancel</v-btn>
				</v-toolbar-items>
	        </v-toolbar>
	        <v-card-text>
				<v-form ref="form" v-model="formVal">
					<v-row>
						<v-col cols="12" md="4">
							<v-autocomplete
								clearable
								label="Customer Address Tag"
								v-model="fd.address_id"
								:rules="[rules.required]"
								:search-input.sync="addSearch"
								placeholder="Start typing to Search"
								:items="addresses"
								item-text="tag_name"
								item-value="id"
								@change="updAdd">
							</v-autocomplete>
						</v-col>
						<v-col cols="12" md="4">
							<v-select label="Saleperson" v-model="fd.saleperson_id" :items="salepersons" item-text="name" item-value="id" :rules="[rules.required]" @change="setRate(); updateBill();">
							</v-select>
						</v-col>
						<v-col cols="12" md="4">
							<v-select v-model="fd.pricelist_id" :items="pricelists" item-text="name" item-value="id" :rules="[rules.required]" label="Pricelist">
							</v-select>
						</v-col>
					</v-row>
					<v-row>
						<v-col cols="12" md="4">
							<v-list dense v-if="selectedAdd.line_1">
								<v-list-item dense>
									<v-list-item-content>
										{{selectedAdd.line_1}}, {{selectedAdd.line_2}}
									</v-list-item-content>
								</v-list-item>
								<v-list-item dense>
									<v-list-item-content>
										PIN: {{selectedAdd.pin}}, {{selectedAdd.city.name}}
									</v-list-item-content>
								</v-list-item>
								<v-list-item dense>
									<v-list-item-content>
										{{selectedAdd.state.name}}, {{selectedAdd.country.name}}
									</v-list-item-content>
								</v-list-item>
								<v-list-item dense v-for="(phone,index) in selectedAdd.phones" :key="index">
									<v-list-item-content>
										Phone: {{selectedAdd.phones[index].phone}}
									</v-list-item-content>
								</v-list-item>
							</v-list>
						</v-col>
						<v-col cols="12" md="8">
							<v-row>
								<v-col cols="12" md="4">
									<v-select v-model="fd.createdby_id" :items="warehouseStaffs" item-text="name" item-value="id" :rules="[rules.required]" label="Created By"></v-select>
								</v-col>
								<v-col cols="12" md="4">
			                        <v-switch v-model="gstSwitch" true-value="1" false-value="0" label="GST Included"/>
			                    </v-col>
							</v-row>
						</v-col>
					</v-row>
					<v-row class="mx-0">
	                    <v-col class="pa-3">
	                        <v-card elevation="10">
	                            <v-card-text>
	                                <v-simple-table dense >
	                                    <thead>
	                                        <tr>
	                                            <th class="text-left">#</th>
	                                            <th class="text-left">Product</th>
	                                            <th class="text-right" >Quantity</th>
	                                            <th class="text-right" >Rate</th>
	                                            <th class="text-right" >GST</th>
	                                            <th class="text-right" >Price</th>
	                                            <th class="text-center">Action</th>
	                                        </tr>
	                                    </thead>
	                                    <tbody>
	                                        <tr v-for="(item,index) in items" :key="index">
	                                            <td>{{item.line}}</td>
	                                            <td>{{item.product}}</td>
	                                            <td class="text-right">{{item.qty}}</td>
	                                            <td class="text-right">{{item.rate}}</td>
	                                            <td class="text-right">{{item.gst}}%</td>
	                                            <td class="text-right">{{item.price}}</td>
	                                            <td><v-btn icon fab small @click.stop="removeItem(item.pos)"><v-icon>mdi-minus-circle</v-icon></v-btn></td>
	                                        </tr>
	                                        <tr>
	                                            <td>{{count}}</td>
	                                            <td>
	                                                <v-autocomplete
	                                                  v-model="productSelect"
	                                                  :loading="productLoading"
	                                                  :items="productItems"
	                                                  :search-input.sync="searchProduct"
	                                                  cache-items
	                                                  class="mx-0 py-0 mt-0"
	                                                  flat
	                                                  clearable
	                                                  hide-no-data
	                                                  item-text="name"
	                                                  item-value="id"
	                                                  :filter="prodFilter"
	                                                >
	                                                	<template v-slot:item="{ item }">
															<v-list-item-content>
																<v-list-item-title v-text="item.name"></v-list-item-title>
																<v-list-item-subtitle v-text="item.sku"></v-list-item-subtitle>
															</v-list-item-content>
														</template>
	                                            	</v-autocomplete>
	                                            </td>
	                                            <td><v-text-field class="text-left" style="" v-model="qty" @click.native="$event.target.select()"></v-text-field></td>
	                                            <td><v-text-field class="text-left" style="" :loading="rateLoading" v-model="rate" @click.native="$event.target.select()"></v-text-field></td>
	                                            <td><v-text-field class="text-left" style="" readonly :value="gst+'%'"></v-text-field @click.native="$event.target.select()"></td>
	                                            <td><v-text-field class="text-left" style="" readonly :rules="[rules.decimal]" :value="(parseInt(qty)*parseFloat(rate)).toFixed(2)" @click.native="$event.target.select()"></v-text-field></td>

	                                            <td><v-btn icon rounded small :disabled="productSelect == null || isNaN(parseInt(qty)*parseFloat(rate))" @click.stop="addLine"><v-icon>mdi-plus-circle</v-icon></v-btn></td>
	                                        </tr>
	                                        <tr v-if="gstSwitch == '0'">
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td class="text-right">Tax</td>
	                                            <td><v-text-field readonly class="text-end" style="direction:rtl;" v-model="tax" append-icon="mdi-plus"></v-text-field></td>
	                                            <td></td>
	                                        </tr>
	                                        <tr>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td class="text-right">Discount</td>
	                                            <td><v-text-field class="text-end" style="direction:rtl;" v-model="discount" append-icon="mdi-minus"></v-text-field></td>
	                                            <td></td>
	                                        </tr>
	                                        <tr>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td class="text-right">Freight</td>
	                                            <td><v-text-field class="text-end" style="direction:rtl;" v-model="freight" append-icon="mdi-plus"></v-text-field></td>
	                                            <td></td>
	                                        </tr>
	                                        <tr>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td></td>
	                                            <td class="text-right">Total</td>
	                                            <td><v-text-field readonly class="text-end" style="direction:rtl;" v-model="total"></v-text-field></td>
	                                            <td></td>
	                                        </tr>
	                                    </tbody>
	                                </v-simple-table>
	                                <v-row justify="space-around">
	                                	<v-btn tile color="primary" @click="save()">Save</v-btn>
	                                </v-row>
	                            </v-card-text>
	                        </v-card>
	                    </v-col>
	                </v-row>
				</v-form>
			</v-card-text>
        </v-card>
        <v-dialog v-model ="closeConfirm" persistent width="500">
	    	<v-card>
	    		<v-card-text class="pt-4">
	    			<v-alert dark color="error">
	    				All your unsaved changes will be lost. Do you want to continue? 
	    			</v-alert>
	    		</v-card-text>
	    		<v-card-actions>
	    			<v-btn text color="error" @click.stop="closeDialog">Yes</v-btn>
	    			<v-btn text color="success" @click.stop="closeConfirm = false">NO</v-btn>
	    		</v-card-actions>
	    	</v-card>
	    </v-dialog>
	</v-dialog>
</template>
<script>
	export default{
		mounted(){
			this.deboucedProdSearch = _.debounce((val)=>{
				this.productLoading = true
	            axios.get('products?search='+val).then((response)=>{
                    this.productItems = response.data.data
                    this.productLoading = false
	            })
	        },500)
		},
		props:['dialog','saleId','mode'],
		watch:{
			dialog:function(){
				axios.get('pricelists').then((res)=>{this.pricelists = res.data.data})
				axios.get('users?role=Sale').then((res)=>{this.salepersons = res.data})
				axios.get('users?role=Warehouse').then((res)=>{this.warehouseStaffs = res.data})
				if(this.mode == 'create'){
					this.submitTxt = 'Save'
					this.formTitle = 'Create Sale'					
				}
				if(this.mode == 'edit'){
					this.submitTxt = 'Update'
					this.formTitle = 'Edit Sale'
				}
			},
			addSearch(val){
				if(val != '' && val != null){
					axios.get('customers/add_search?search='+val+'&rpp=5').then((res)=>{
						this.addresses = res.data
					})
				}
			},
			formVal:{
				handler(){
					this.$refs.form.validate()
				},
				deep:true
			},
			searchProduct: {
				handler (val) {
					if( val != undefined || val != null){
						if(val.length != 1) this.deboucedProdSearch(val);
					}
				},
				deep: true
	        },
	        productSelect:{
	            handler(){
	                this.setRate() 
	            }
	        },
	        discount:{
	            handler(){
	                this.updateTotal()
	            }
	        },
	        freight:{
	            handler(){
	                this.updateTotal()
	            }
	        },
	        gstSwitch:{
	            handler(){
	                this.setRate() 
	                this.updateBill()
	            }
	        }
		},
		data(){
			return{
				count:1,
				searchProduct:null,
	            discount : 0,
	            total : 0,
	            freight:0,
	            tax:0,
	            items:[],
	            qty:1,
	            gst:0,
	            rate:0,
	            items:[],
	            productItems:[],
            	productSelect:null,
				productLoading:false,
				rateLoading:false,
				selectedAdd:{
					city:{},
					state:{},
					country:{},
					phones:[],
				},
				closeConfirm:false,
				formTitle:'',
				submitTxt:'',
				formVal:'',
				addSearch:'',
				fd:{
					pricelist_id:0,
					saleperson_id:0,
					address_id:'',
					createdby_id:this.$store.state.user.id
				},
				addresses:[
				],
				pricelists:[],
				salepersons:[],
				warehouseStaffs:[],
				rules:{
					required: value=> !!value||'Required.',
					decimal: value => !isNaN(value) || 'Must be an number',
				},
				gstSwitch:'1',
			}
		},
		methods:{
			prodFilter(item, queryText, itemText){
				const textOne = item.name.toLowerCase()
		        const textTwo = item.sku.toLowerCase()
		        const searchText = queryText.toLowerCase()
		        const searchArr = searchText.split(" ")
		        var flag = false
		        searchArr.forEach((term)=>{
		        	flag = textOne.indexOf(term) > -1 ? true : false
		        })
		        return flag || textTwo.indexOf(searchText) > -1
			},
			updAdd(){
				if(this.fd.address_id == '' || this.fd.address_id == undefined){
					this.selectedAdd={city:{},state:{},country:{},phones:[]}
					this.fd.saleperson_id = ''
				}
				else{
					var grouped = _.groupBy(this.addresses,'id')
					var index = Object.keys(grouped).indexOf(this.fd.address_id.toString())
					this.selectedAdd =  this.addresses[index]
					this.fd.saleperson_id = this.addresses[index].salepersons[0].id
				}
			},
			closeDialog(){
				this.$refs.form.reset()
				this.$emit('close-dialog')
			},
			updateTotal(){
	            var temp = 0;
	            var temp2 = 0
	            Object.keys(this.items).forEach((key)=>{
	                temp = parseFloat(temp)+parseFloat(this.items[key].price)
	                temp2 = (parseFloat(this.items[key].price)*(parseFloat(this.items[key].gst)/100)).toFixed(2)
	            })
	            this.tax = temp2
	            if(this.gstSwitch === '0'){
	                this.total = (parseFloat(temp)+parseFloat(temp2)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
	            }
	            else{
	                this.total = (parseFloat(temp)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
	            }
	        },
	        updateBill(){
	            if(this.items.length > 0){
	                Object.keys(this.items).forEach((key)=>{
	                    axios.get('sale/orders/getrate?&id='+this.items[key].id+'&pl='+this.pricelistSelect).then((response)=>{
	                        if(response.status == 200){
	                            var gst = response.data.gst
	                            var landing_price = response.data.landing_price
	                            var value = response.data.value
	                            this.gst = response.data.gst
	                            this.items[key].gst = response.data.gst
	                            if(this.gstSwitch === '0'){
	                                this.items[key].rate = (parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
	                            }
	                            else {
	                                this.items[key].rate = (((parseFloat(landing_price)*((parseFloat(value)/100)+1)).toFixed(2)
	)*((parseFloat(gst)/100)+1)).toFixed(2)
	                            }
	                            this.items[key].price = (parseFloat(this.items[key].rate)*parseInt(this.items[key].qty)).toFixed(2)
	                            this.updateTotal()
	                        }
	                    })

	                })
	            }   
	        },
	        setRate(){
	            if(this.fd.pricelist_id != null && this.fd.pricelist_id != undefined && this.productSelect != undefined && this.productSelect != null){
	                var grouped = _.groupBy(this.productItems,'id')
	                var index = Object.keys(grouped).indexOf(this.productSelect.toString())
	                var gst = this.productItems[index].gst
	                this.gst = gst
	                var landing_price = this.productItems[index].landing_price
	                var margin = 0
	                this.productItems[index].pricelists.forEach((pl)=>{
	                	if(this.fd.pricelist_id == pl.id) margin = pl.pivot.margin
	                })
	                if(this.gstSwitch === '0'){
                        this.rate = (parseFloat(landing_price)*((parseFloat(margin)/100)+1)).toFixed(2)
                    }
                    else {
                        this.rate = (((parseFloat(landing_price)*((parseFloat(margin)/100)+1)).toFixed(2)
)*((parseFloat(gst)/100)+1)).toFixed(2)
                    }
	            }
	            if(this.productSelect == undefined){
	                this.rate = 0
	            }
	        },
	        getRate(){

	        },
	        addLine(){
	            this.items.push({
	                'id':this.productSelect,
	                'line':this.count,
	                'product':this.searchProduct,
	                'qty':this.qty,
	                'rate':this.rate,
	                'gst':this.gst,
	                'price': parseInt(this.qty)*parseFloat(this.rate),
	                'pos': parseInt(this.count)-1
	            })
	            this.count=parseInt(this.count)+1
	            this.qty = 1
	            this.rate = 0
	            this.searchProduct=null
	            this.productSelect=null
	            this.updateTotal()
	        },
	        removeItem(pos){
	            this.items.splice(pos,1)
	            var tempCount = 1
	            Object.keys(this.items).forEach((key)=>{
	                this.items[key].line = tempCount
	                this.items[key].pos = parseInt(tempCount)-1
	                tempCount = tempCount+1
	            })
	            this.count = parseInt(this.items.length)+1
	            this.updateTotal()
	        },
		}
	}
</script>