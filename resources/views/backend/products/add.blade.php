@extends('layouts.app')
@section('page-title')
    Create Product
@endsection
@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card >
            <v-card-text>
                <v-form ref="form" v-model="detailsFormVal" v-on:submit.prevent="">
                    <v-text-field
                        autofocus
                        label="Name"
                        v-model="fd.name.value"
                        :rules="[rules.required]"
                        :error-messages="fd.name.error"
                        @keydown="fd.name.error = ''">
                    </v-text-field>
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="HSN Code"
                                v-model="fd.hsn.value">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="MRP"
                                v-model.number="fd.mrp.value"
                                :rules="[rules.price,rules.required]"
                                :error-messages="fd.mrp.error"
                                @keydown="fd.mrp.error = ''"
                                prepend-inner-icon="mdi-currency-inr">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="GSP Customer"
                                v-model.number="fd.gsp_customer.value"
                                :rules="[rules.price,rules.required]"
                                :error-messages="fd.gsp_customer.error"
                                @keydown="fd.gsp_customer.error = ''"
                                hint="General Selling Price Customer"
                                persistent-hint
                                prepend-inner-icon="mdi-currency-inr">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="GSP Dealer"
                                v-model.number="fd.gsp_dealer.value"
                                :rules="[rules.price,rules.required]"
                                :error-messages="fd.gsp_dealer.error"
                                @keydown="fd.gsp_dealer.error = ''"
                                hint="General Selling Price Dealer"
                                persistent-hint
                                prepend-inner-icon="mdi-currency-inr">
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="Weight"
                                v-model.number="fd.weight.value"
                                :rules="[rules.weight,rules.required]"
                                :error-messages="fd.weight.error"
                                @keydown="fd.weight.error = ''"
                                hint="weight in grams"
                                persistent-hint
                                suffix="grams">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-text-field 
                                label="Landing Price"
                                v-model.number="fd.landing_price.value"
                                :rules="[rules.price,rules.required]"
                                :error-messages="fd.landing_price.error"
                                @keydown="fd.landing_price.error = ''"
                                prepend-inner-icon="mdi-currency-inr">
                            </v-text-field>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                label="GST"
                                v-model="fd.gst.value"
                                suffix="%"
                                :items="fd.gst.items"
                                :rules="[rules.required]">
                            </v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-text-field v-for="(item,index) in aliases" :key="index" :label="aliases[index].label" v-model="aliases[index].value" :error-messages="aliases[index].error"
                            @keydown="aliases[index].error = ''" append-outer-icon="mdi-minus-circle" v-on:click:append-outer="deleteAlias(index)">
                            </v-text-field>
                            <v-btn text @click.stop="addAlias">Add Alias</v-btn>
                        </v-col>
                        <v-col cols="12" md="8">
                            <v-row>
                                <v-col cols="12" md="6" v-if="user.meta.approve_product == true">
                                    <v-switch v-model="fd.approved.value" label="Approved?" true-value=1 false-value=0>
                                    </v-switch>
                                </v-col>
                                <v-col cols="12" md="6" v-if="user.meta.tally_product == true">
                                    <v-switch v-model="fd.tally.value" label="Tally updated?" true-value=1 false-value=0>
                                    </v-switch>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                    <h4>Category</h4>
                    <v-row>
                        @foreach($taxonomies as $taxonomy)
                            <v-col cols="12" md="3">
                                <v-autocomplete 
                                label="{{$taxonomy->name}}"
                                :items="taxonomies[{{$loop->index}}].categories"
                                item-text="name"
                                item-value="id"
                                v-model="fd.taxonomy_{{$taxonomy->slug}}"
                                :rules="[rules.required]"></v-autocomplete>
                            </v-col>
                        @endforeach
                    </v-row>
                        <v-col cols="12" md="6">
                            <h4>Pricelist</h4>
                            <template v-for="(item,index) in pricelists">
                                <v-row>
                                    <v-col cols="6">
                                        <v-text-field
                                            v-model.number="pricelists[index].value"
                                            :label="pricelists[index].name"
                                            :rules="[rules.price,rules.required]"
                                            hint="Margin"
                                            persistent-hint
                                            suffix="%">
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-text-field
                                            :value="calculatePrice(pricelists[index].value)"
                                            readonly
                                            hint="Estimated Selling Price"
                                            persistent-hint>
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                            </template>
                        </v-col>
                    </v-row>
                    <h4>Stock</h4>
                    <v-switch v-model="expirable" label="Expirable Product ?"></v-switch>
                    <v-file-input 
                        prepend-icon="mdi-camera" 
                        v-model="imgFile"
                        accept="image/png, image/jpeg, image/bmp"
                        placeholder="Select an image to upload"
                        hint="Supported formats: png, jpg, bmp"
                        persistent-hint
                        append-outer-icon="mdi-cloud-upload"
                        @click:append-outer="uploadImg"></v-file-input>
                    <v-row>
                        <v-col cols="6" md="2" v-for="(url,index) in medias" :key="index">
                            <v-img max-height="250" max-width="250" :src="baseUrl+'/'+url" @click.stop="imgModal(baseUrl+'/'+url)" style="cursor:pointer"></v-img>
                            <v-btn text color="info" @click="deleteMedia(url)">Delete</v-btn>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-textarea v-model="comment" row="3" label="Comment"></v-textarea>
                        </v-col>
                    </v-row>
                    <v-row justify="space-around">
                        <v-col>
                            <v-btn tile color="primary" @click="save()" :loading="btnloading">Save</v-btn>
                            <v-btn tile color="secondary" @click="closeConfirm = true">Cancel</v-btn>
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
        <v-dialog v-model ="imgDialog" width="700">
            <v-card>
                <v-img :src="imgUrl" :lazy-src="imgUrl"></v-img>
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
                user:{
                    meta:{
                        tally_product:false,
                        approve_product:false,
                    }
                },
                imgFile:null,
                closeConfirm:false,
                waitDialog:false,
                imgDialog:false,
                btnloading:false,
                detailsFormVal:null,
                imgUrl:null,
                sbColor:'',
                sbText:'',
                sbTimeout:3000,
                snackbar:false,
                fd:{
                    name:{
                        value:'',
                        error:'',
                    },
                    hsn:{
                        value:'',
                        error:'',
                    },
                    mrp:{
                        value:'',
                        error:'',
                    },
                    landing_price:{
                        value:'',
                        error:'',
                    },
                    gsp_customer:{
                        value:'',
                        error:'',
                    },  
                    gsp_dealer:{
                        value:'',
                        error:'',
                    },
                    weight:{
                        value:'',
                        error:'',
                    },
                    tally:{
                        value:0
                    },
                    approved:{
                        value:0
                    },
                    gst:{
                        value:'',
                        error:'',
                        items:[
                            {text:'5%', value:'5'},
                            {text:'12%', value:'12'},
                            {text:'18%', value:'18'},
                        ],
                    },
                    @foreach ($taxonomies as $taxonomy)
                        @foreach($taxonomy->categories as $category)
                            @if($category->slug == 'none')
                                taxonomy_{{$taxonomy->slug}}: {{$category->id}},
                                @break
                            @endif
                        @endforeach
                    @endforeach
                },
                aliases:[
                    {label:'Alias 1',value:'',error:''}
                ],
                taxonomies:@json($taxonomies),
                pricelists:[],
                expirable: false,
                medias:[],
                comment:'',
                rules:{
                    required: value=> !!value||'Required.',
                    price: value => {
                        const pattern = /^\d{0,8}(\.\d{1,2})?$/
                        return pattern.test(value) || 'Invalid value.'
                        },
                    weight: value => {
                        const pattern = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/
                        return pattern.test(value) || 'Invalid value.'
                        },
                    whole: value => {
                        const pattern = /^\d+$/
                        return pattern.test(value) || 'Invalid value.'
                        },
                    img: value => !value || value.size < 2000000 || 'Image size should be less than 2 MB!',
                },
                taxonomy:{
                    options:[],
                }
            }
        },
        mounted(){
            this.waitDialog = true
            axios.all([
                axios.get('pricelists').then((res)=>{
                    var data = res.data.data
                    data.forEach((item,index)=>{
                        data[index].value = '0'
                    })
                    this.pricelists = data
                }),
                axios.get('menu').then((res)=>{
                    this.sidebar_left_items = res.data
                }),
                axios.get('user?with_permissions=1').then((res)=>{
                    res.data.roles.forEach((role)=>{
                        role.permissions.forEach((permission)=>{
                            if(permission.name == 'approve_product'){
                                this.user.meta.approve_product = true
                            }
                            if(permission.name == 'tally_product'){
                                this.user.meta.tally_product = true
                            }
                        })
                    })
                })
            ]).then(()=>{
                this.waitDialog = false
            })
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },       
        },
        methods:{
            closeDialog(){
                window.location.href='{{$prev_url}}'
            },
            emitSb(text,color){
                window.localStorage.setItem('message',text)
                window.localStorage.setItem('message_status',color)
            },
            addStockLine(index){
                this.warehouses[index].items.push({value:'0',batch:'',expiry_date:'',date_menu:false,})
            },
            addAlias(){
                var newLabel = 'Alias '+(this.aliases.length + 1)
                this.aliases.push({label:newLabel,value:'',error:''})
            },
            deleteStockLine(index,index2){
                this.warehouses[index].items.splice(index2,1)
            },
            deleteAlias(index){
                this.aliases.splice(index,1)
                Object.keys(this.aliases).forEach((key,index)=>{
                    this.aliases[key].label = 'Alias '+(index+1)
                })
            },
            deleteMedia(url){
                const index = this.medias.indexOf(url)
                if (index >= 0) this.medias.splice(index, 1)
            },
            calculatePrice(el){
                //const val = ((parseFloat(this.fd.landing_price.value) * (1+(parseFloat(this.fd.gst.value)/100)))*(1+(parseFloat(el)/100))).toFixed(2)
                //return isNaN(val) ? '-': val.toString()
            },
            uploadImg(){
                this.waitDialog = true
                var fD = new FormData()
                fD.append('file',this.imgFile)
                axios.post('/products/upload',fD,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then((response)=>{
                    this.waitDialog = false
                    this.medias.push(response.data)
                    this.imgFile = null
                }).catch((error)=>{
                    this.imgFile = null
                    this.waitDialog = false
                    if(error.response.status == 422){
                        this.triggerSb('Invalid Image','error')
                    }
                })
            },
            imgModal(url){
                this.imgUrl = url
                this.imgDialog = true
            },
            save(){
                this.btnloading = true
                this.$refs.form.validate();
                if(!this.$refs.form.validate() ){
                    this.triggerSb('There are errors in the form submitted. Please check!!','error')
                    this.btnloading = false
                    this.$vuetify.goTo(0)
                }
                else{
                    var fD = new FormData()
                    Object.keys(this.fd).forEach((key)=>{
                        fD.append(key,this.fd[key].value)
                    })
                    this.taxonomies.forEach((item)=>{
                        fD.append('taxonomy_'+item.slug,item.value)
                    })
                    this.pricelists.forEach((item)=>{
                        fD.append('pricelist_'+item.slug,item.value)
                    })
                    fD.append('medias',this.medias)
                    fD.append('aliases',JSON.stringify(this.aliases))
                    fD.append('expirable',this.expirable ? 1 : 0)
                    fD.append('comment',this.comment ? this.comment : '')
                    var route = 'products'
                    axios.post(route,fD).then((response)=>{
                        this.btnloading = false
                        this.emitSb({text:'Category Created Successfully',color:'success'})
                        window.location.href = '{{$prev_url}}'
                    }).catch((error)=> {
                        if(error.response.status == 422){
                            this.btnloading = false
                            var errors = error.response.data.errors
                            Object.keys(errors).forEach((key)=>{
                                this.fd[key].error = errors[key]
                            })
                            this.triggerSb('There are errors in the form submitted. Please check!!','error')
                        }
                        if(error.response.status == 403){
                            this.btnloading = false
                            this.triggerSb('You are not authorised to do this action','error')
                        }
                    })
                }
            },
            triggerSb(text,color){
                this.sbText = text
                this.sbColor = color
                this.snackbar = true
            },
        }
    }).$mount('#app')
</script>
@endsection
