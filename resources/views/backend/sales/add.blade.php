@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-toolbar id="top" flat color="transparent">
                <v-toolbar-title>Create Order</v-toolbar-title>
                <div class="flex-grow-1"></div>
                <v-toolbar-items>
                    <v-btn text href="{{$prev_url}}">Back</v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text>
                <v-form ref="form" v-model="formVal" v-on:submit.prevent="">
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-autocomplete
                                clearable
                                label="Customer Account"
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
                            <v-select label="Saleperson" v-model="fd.saleperson_id" :items="salepersons" item-text="name" item-value="id" :rules="[rules.required]" @change="updateBill();">
                            </v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-select v-model="fd.pricelist_id" :items="pricelists" item-text="name" item-value="id" label="Pricelist" @change="changePl()">
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-list dense v-if="selectedAdd.line_1">
                                <v-list-item dense>
                                    <v-list-item-content>
                                        @{{selectedAdd.line_1}}, @{{selectedAdd.line_2}}
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item dense>
                                    <v-list-item-content>
                                        PIN: @{{selectedAdd.pin}}, @{{selectedAdd.city.name}}
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item dense>
                                    <v-list-item-content>
                                        @{{selectedAdd.state.name}}, @{{selectedAdd.country.name}}
                                    </v-list-item-content>
                                </v-list-item>
                                <v-list-item dense v-for="(phone,index) in selectedAdd.phones" :key="index">
                                    <v-list-item-content>
                                        Phone: @{{selectedAdd.phones[index].phone}}
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
                                    <v-switch v-model="gstSwitch" true-value="1" false-value="0" label="GST Included" @change="updateBill()"/>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-form>
                <v-row class="mx-0">
                    <v-col class="pa-3">
                        <v-card elevation="10">
                            <v-card-text>
                                <v-simple-table>
                                    <template v-slot:default>
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
                                                <td>@{{item.line}}</td>
                                                <td>@{{item.product}}</td>
                                                <td class="text-right">@{{item.qty}}</td>
                                                <td class="text-right">@{{item.rate}}</td>
                                                <td class="text-right">@{{item.gst}}%</td>
                                                <td class="text-right">@{{item.price}}</td>
                                                <td><v-btn icon fab small @click.stop="removeItem(item.pos)"><v-icon>mdi-minus-circle</v-icon></v-btn></td>
                                            </tr>
                                            <tr>
                                                <td>@{{count}}</td>
                                                <td>
                                                    <v-autocomplete
                                                      v-model="productSelect"
                                                      :loading="productLoading"
                                                      :items="productItems"
                                                      :search-input.sync="searchProduct"
                                                      class="mx-0 py-0 mt-0"
                                                      flat
                                                      clearable
                                                      hide-no-data
                                                      item-text="name"
                                                      item-value="id"
                                                      :filter="prodFilter"
                                                      @change="setRate()"
                                                    >
                                                        <template v-slot:item="{ item }">
                                                            <v-list-item-content>
                                                                <v-list-item-title v-text="item.name"></v-list-item-title>
                                                                <v-list-item-subtitle v-text="item.sku"></v-list-item-subtitle>
                                                            </v-list-item-content>
                                                        </template>
                                                    </v-autocomplete>
                                                </td>
                                                <td><v-text-field class="input-right" style="" v-model="qty" @click.native="$event.target.select()"></v-text-field></td>
                                                <td><v-text-field class="input-right" style="" :loading="rateLoading" v-model="rate" @click.native="$event.target.select()"></v-text-field></td>
                                                <td><v-text-field class="input-right" style="" readonly :value="gst+'%'"></v-text-field @click.native="$event.target.select()"></td>
                                                <td><v-text-field class="input-right" style="" readonly :rules="[rules.decimal]" :value="(parseInt(qty)*parseFloat(rate)).toFixed(2)" @click.native="$event.target.select()"></v-text-field></td>

                                                <td><v-btn icon rounded small :disabled="productSelect == null || isNaN((parseInt(qty)*parseFloat(rate)).toFixed(2))" @click.stop="addLine"><v-icon>mdi-plus-circle</v-icon></v-btn></td>
                                            </tr>
                                            
                                            <tr v-if="gstSwitch == '0'">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="input-right">GST</td>
                                                <td><v-text-field readonly class="input-right" style="" v-model="tax" prepend-icon="mdi-plus"></v-text-field></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="input-right">Discount</td>
                                                <td><v-text-field class="input-right" style="" v-model="discount" prepend-icon="mdi-minus"></v-text-field></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="input-right">Freight</td>
                                                <td><v-text-field class="input-right" style="" v-model="freight" prepend-icon="mdi-plus"></v-text-field></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="input-right">Total</td>
                                                <td><v-text-field readonly class="input-right" style="" v-model="total"></v-text-field></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                                <v-row justify="space-around">
                                    <v-btn tile color="primary" @click="save()">Save</v-btn>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
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
        <v-dialog v-model="waitDialog" persistent width="300">
        	<v-card color="primary" dark>
        		<v-card-text>
        			Please stand by
        			<v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
        		</v-card-text>
        	</v-card>
        </v-dialog>
    </v-col>
</v-row>
@endsection

@section('script')
<script>
    var vue = new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                waitDialog:false,
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
                formVal:'',
                addSearch:'',
                fd:{
                    pricelist_id:null,
                    saleperson_id:null,
                    address_id:'',
                    createdby_id: null
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
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('pricelists').then((res)=>{this.pricelists = res.data.data}),
                axios.get('users?role=Sale').then((res)=>{this.salepersons = res.data}),
                axios.get('users?role=Warehouse').then((res)=>{this.warehouseStaffs = res.data}),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                })
            ]).then(()=>{
                this.fd.createdby_id = parseInt('{{$user->id}}')
                this.waitDialog = false
            })
            this.deboucedProdSearch = _.debounce((val)=>{
                this.productLoading = true
                axios.get('products?search='+val).then((response)=>{
                    this.productItems = response.data.data
                    this.productLoading = false
                })
            },500)
        },
        watch:{
            addSearch(val){
                if(val != '' && val != null){
                    axios.get('customers/add_search?search='+val+'&rpp=5').then((res)=>{
                        this.addresses = res.data
                    })
                }
            },
            searchProduct: {
                handler (val) {
                    if( val != undefined || val != null){
                        if(val.length != 1) this.deboucedProdSearch(val);
                    }
                },
                deep: true
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
        },

        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
            changePl(){
                this.productSelect = null
                this.qty = 1
                this.rate = 0
                this.gst = 0
                this.updateBill()
            },
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
                window.location.href = '{{$prev_url}}'
            },
            updateTotal(){
                var temp = 0;
                var temp2 = 0
                //console.log(this.items)
                Object.keys(this.items).forEach((key)=>{    
                    if(this.gstSwitch === '0'){
                        var taxIndividual = (parseFloat(this.items[key].rate)*(parseFloat(this.items[key].gst)/100)).toFixed(2)
                        //console.log('taxIndividual:'+ taxIndividual)
                        var taxItem = taxIndividual*parseInt(this.items[key].qty)
                        //console.log('taxItem:'+ taxItem)
                        //this.items[key].rate = (parseFloat(this.items[key].rate)-parseFloat(taxIndividual)).toFixed(2)
                        this.items[key].price = parseFloat(this.items[key].rate)*parseInt(this.items[key].qty) //update each item price
                        temp = parseFloat(temp)+parseFloat(this.items[key].rate) // 
                        //console.log('temp:'+ temp)
                        temp2 = parseFloat(temp2) + parseFloat(taxItem)
                        //console.log('temp2:'+ temp2)
                    }
                    else{
                        temp = parseFloat(temp)+parseFloat(this.items[key].price)
                    }
                })
                //console.log(this.items)
                this.tax = temp2.toFixed(2)
                if(this.gstSwitch === '0'){
                    this.total = (parseFloat(temp)+parseFloat(temp2)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
                }
                else{
                    this.total = (parseFloat(temp)-parseFloat(this.discount)+parseFloat(this.freight)).toFixed(2)
                }
            },
            updateBill(){
                if(this.items.length > 0){
                    this.waitDialog = true
                    var ids = []
                    this.items.forEach((item)=>{
                        ids.push(item.id)
                    })
                    axios.get('products/getrate?&id='+ids.toString()+'&pl='+this.fd.pricelist_id).then((res)=>{
                        res.data.forEach((item)=>{
                            Object.keys(this.items).forEach((key)=>{
                                if(this.items[key].id == item.id){
                                    var gst = item.gst
                                    var landing_price = item.landing_price
                                    var margin = item.margin
                                    this.items[key].gst = gst
                                    if(this.gstSwitch === '0' && parseFloat(landing_price) != 0 && parseFloat(margin) != 0 ){
                                        this.items[key].rate = (parseFloat(landing_price)*((parseFloat(margin)/100)+1)).toFixed(2)
                                    }
                                    else {
                                        if(parseFloat(landing_price) != 0 && parseFloat(margin) != 0){
                                            this.items[key].rate = (((parseFloat(landing_price)*((parseFloat(margin)/100)+1)).toFixed(2)
        )*((parseFloat(gst)/100)+1)).toFixed(2)
                                        }
                                    }
                                    this.items[key].price = (parseFloat(this.items[key].rate)*parseInt(this.items[key].qty)).toFixed(2)
                                }   
                            })
                        })
                    }).finally(()=>{
                        this.waitDialog = false
                        this.updateTotal()
                    })
                }   
            },
            setRate(){
                if(this.fd.pricelist_id != null && this.fd.pricelist_id != undefined && this.productSelect != undefined && this.productSelect != null){
                    var grouped = _.groupBy(this.productItems,'id')
                    var index = Object.keys(grouped).indexOf(this.productSelect.toString())
                    //console.log('grouped : '+grouped)
                    //console.log('index : '+index)
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
                    'price': (parseInt(this.qty)*parseFloat(this.rate)).toFixed(2),
                    'pos': parseInt(this.count)-1
                })
                this.count=parseInt(this.count)+1
                this.qty = 1
                this.rate = 0
                this.gst = 0
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
            emitSb(text,color){
                window.localStorage.setItem('message',text)
                window.localStorage.setItem('message_status',color)
            },
        }
    }).$mount('#app')
</script>
@endsection
