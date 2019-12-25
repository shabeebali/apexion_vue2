@extends('layouts.app')
@section('content')
<v-row>
	<v-col class="mx-4">
		<v-toolbar color="transparent" flat>
			<v-toolbar-title>Configuration</v-toolbar-title>
			<div class="flex-grow-1"></div>
			<v-toolbar-items>
				<v-btn @click="save" tile color="primary" :loading="btnloading">Save</v-btn>
			</v-toolbar-items>
		</v-toolbar>
		<v-expansion-panels v-model="panels" multiple>
			<v-expansion-panel>
				<v-expansion-panel-header>Sale Order</v-expansion-panel-header>
				<v-expansion-panel-content>
					<v-row>
						<v-col cols="6">
							<p class="text-right mt-5">Default warehouse for sale order</p>
						</v-col>
						<v-col cols="6">
							<v-select :items="warehouses" v-model="so_default_wh" item-text="name" item-value="id"></v-select>
						</v-col>
					</v-row>
                    <v-row>
                        <v-col cols="6">
                            <p class="text-right mt-5">Default pricelist for sale order</p>
                        </v-col>
                        <v-col cols="6">
                            <v-select :items="pricelists" v-model="so_default_pl" item-text="name" item-value="id"></v-select>
                        </v-col>
                    </v-row>
				</v-expansion-panel-content>
			</v-expansion-panel>
		</v-expansion-panels>
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
                sbColor:'',
                sbText:'',
                sbTimeout:10000,
                snackbar:false,
                warehouses:[],
                so_default_wh:null,
                pricelists:[],
                so_default_pl:null,
                btnloading:false,
                panels:[0],
            }
        },
        mounted(){
        	this.waitDialog = true
        	axios.all([
        		axios.get('menu').then((res)=>{
	                this.sidebar_left_items = res.data
	            }),
	            axios.get('warehouses').then((res)=>{
	            	this.warehouses = res.data.data
	            	this.so_default_wh = parseInt(res.data.meta.so_default_wh)
	            }),
                axios.get('pricelists').then((res)=>{
                    this.pricelists = res.data.data
                    this.so_default_pl = parseInt(res.data.meta.so_default_pl)
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
        	triggerSb(text,color){
                this.sbText = text
                this.sbColor = color
                this.snackbar = true
            },
        	save(){
        		this.btnloading = true
        		var fD = new FormData
        		fD.append('so_default_wh',this.so_default_wh)
                fD.append('so_default_pl',this.so_default_pl)
        		axios.post('config',fD).then((res)=>{
        			this.btnloading = false
                    this.triggerSb('Settings saved successfully','success')
        		}).catch((error)=> {
                    if(error.response.status == 422){
                        this.btnloading = false
                        this.triggerSb('There are errors in the form submitted. Please check!!','error')
                    }
                    if(error.response.status == 403){
                        this.btnloading = false
                        this.triggerSb('You are not authorised to do this action','error')
                    }
                })
        	}
        }
    }).$mount('#app')
</script>
@endsection