@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-card-title>Product Data</v-card-title>
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
                                                <td>@{{data.id}}</td>
                                            </tr>
                                            <tr>
                                                <td>Name</td>
                                                <td>@{{data.name}}</td>
                                            </tr>
                                            <tr>
                                                <td>SKU</td>
                                                <td>@{{data.sku}}</td>
                                            </tr>
                                            <tr>
                                                <td>HSN Code</td>
                                                <td>@{{data.hsn}}</td>
                                            </tr>
                                            <tr>
                                                <td>MRP</td>
                                                <td><v-icon>mdi-currency-inr</v-icon> @{{data.mrp}}</td>
                                            </tr>
                                            <tr>
                                                <td>Landing Price</td>
                                                <td><v-icon>mdi-currency-inr</v-icon> @{{data.landing_price}}</td>
                                            </tr>
                                            <tr>
                                                <td>GSP Customer</td>
                                                <td><v-icon>mdi-currency-inr</v-icon> @{{data.gsp_customer}}</td>
                                            </tr>
                                            <tr>
                                                <td>GSP Dealer</td>
                                                <td><v-icon>mdi-currency-inr</v-icon> @{{data.gsp_dealer}}</td>
                                            </tr>
                                            <tr>
                                                <td>GST</td>
                                                <td>@{{data.gst}} %</td>
                                            </tr>
                                            <template v-if="data.alias.length > 0">
                                                <tr v-for="(item,index) in data.alias" :key="index">
                                                    <td>Alias @{{index+1}}</td>
                                                    <td>@{{data.alias[index].alias}}</td>
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
                                                <td>@{{item.name}}</td>
                                                <td>@{{item.pivot.margin}} %</td>
                                                <td><v-icon>mdi-currency-inr</v-icon>@{{computePrice(item.pivot.margin)}}</td>
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
                                                <td>@{{item.taxonomy.name}}</td>
                                                <td>@{{item.name}}</td>
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
                                                <th class="text-left">Warehouse</th>
                                                <th class="text-left">Batch</th>
                                                <th class="text-left">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in data.stocks">
                                                <td>@{{item.warehouse.name}}</td>
                                                <td>@{{item.batch}}</td>
                                                <td>@{{item.qty}}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Total</td>
                                                <td>@{{totalStock}}</td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-col>
                            <v-col cols="12">
                                <v-card flat>
                                    <v-card-title>Comments</v-card-title>
                                    <v-card-text>
                                        <p v-if="data.comments.length == 0">No Comments</p>
                                        <div v-else>
                                            <v-list>
                                                <v-list-item v-for="(item,index) in data.comments" :key="index" three-line>
                                                    <v-list-item-content>
                                                        <v-list-item-title>@{{item.body}}</v-list-item-title>
                                                        <v-list-item-subtitle>@{{item.user.name}}</v-list-item-subtitle>
                                                        <v-list-item-subtitle>@{{new Date(item.created_at).toDateString()}}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </div>
                                        <v-form v-on:submit.prevent="">
                                            <v-textarea v-model="comment" label="New Comment" rows="2"></v-textarea>
                                        </v-form>
                                        <v-btn text @click="addComment">Add Comment</v-btn>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-snackbar v-model="snackbar" right botttom :color="sbColor" :timeout="sbTimeout" >
            @{{sbText}}
            <v-btn dark text @click.stop="snackbar = false"> Close</v-btn>
        </v-snackbar>
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
                comment:'',
                sbText:'',
                sbTimeout:3000,
                sbColor:'',
                data:{
                    medias:[],
                    alias:[],
                    comments:[],
                },
                snackbar:false,
                loading:false,
                totalStock:0,
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('products/{{$id}}').then((res)=>{
                    this.data = res.data
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                })
            ]).then(()=>{
                var total = 0
                this.data.stocks.forEach((item)=>{
                    total = total + parseInt(item.qty)
                })
                this.totalStock = total
                this.waitDialog = false
            })
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
            addComment(){
                this.loading = true
                var fD = new FormData()
                fD.append('body',this.comment)
                axios.post('products/add_comment/{{$id}}',fD).then((res)=>{
                    this.loading=false
                    this.data.comments.push(res.data)
                    this.comment = ''
                })
            },
            closeDialog(){
                this.$emit('close-dialog');
            },
            computePrice(el){
                return ((parseFloat(this.data.landing_price) * (1+(parseFloat(this.data.gst)/100)))*(1+(parseFloat(el)/100))).toFixed(2)
            }
        }
    }).$mount('#app')
</script>
@endsection
