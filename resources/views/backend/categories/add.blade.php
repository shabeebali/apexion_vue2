@extends('layouts.app')
@section('content')
<v-row>
	<v-col class="mx-4">
		<v-card>
	        <v-card-title>Create Category</v-card-title>
	        <v-card-text>
				<v-row class="mx-4">
					<v-col cols="12" md="4"></v-col>
	        		<v-col cols="12" md="4">
	        			<v-form v-model="form1Val" ref="form1" v-on:submit.prevent="">
							<v-text-field 
								v-model="fd.name.value" 
								label="Name" 
								autofocus
								:error-messages="fd.name.error"
								@keydown="fd.name.error = ''"
								:rules=[rules.required]
							></v-text-field>
							<v-select label="Taxonomy" 
								v-model="fd.taxonomy.value" 
								:items="taxonomy.options"
								item-text="name"
								item-value="id"
								:rules=[rules.required]
								v-on:change="sizeUpd"></v-select>
							<v-text-field
								v-model="fd.code.value"
								:maxlength="fd.code.size"
								label="Code"
								:disabled="fd.code.disable"
								:error-messages="fd.code.error"
								@keydown="fd.code.error = ''">
							</v-text-field>
						</v-form>
					</v-col>
					<v-col cols="12" md="4"></v-col>
				</v-row>
			</v-card-text>
			<v-card-actions>
				<v-row class="mx-4">
					<v-col cols="12" md="4"></v-col>
					<v-col cols="12" md="4">
						<v-btn color="primary" :disabled="form1Val == false" :loading="btnloading" @click="save()">Save</v-btn>
						<v-btn text href="{{$prev_url}}">Cancel</v-btn>
					</v-col>
					<v-col cols="12" md="4"></v-col>
				</v-row>
			</v-card-actions>
	    </v-card>
	    <v-snackbar bottom right v-model="snackbar" :timeout="sbTimeout" :color="sbColor">@{{sbText}} <v-btn text @click="snackbar = false">CLOSE</v-btn></v-snackbar>
	</v-col>
</v-row>
@endsection
@section('script')
<script>
    new Vue({
        vuetify: new Vuetify(),
        data(){
            return{
                sidebar_left:false,
                sidebar_left_items:[],
                snackbar:false,
				sbColor:'',
				sbText:'',
				sbTimeout:3000,
				btnloading:false,
				form1Val:false,
				fd:{
					name:{
						value:'',
						error:'',
					},
					taxonomy:{
	                    value:''
	                },
	                code:{
	                    value:'',
	                    error:'',
	                    size:1,
	                    disable:false,
	                },
				},
				rules:{
					required: value=> !!value||'Required.',
				},
				taxonomy:{
					options:[],
				}
            }
        },
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
            })
            axios.get('taxonomies').then((res)=>{
				this.taxonomy.options = res.data.data
			})
        },
        computed:{
            baseUrl(){
                return window.base_url.content
            },
        },
        methods:{
			sizeUpd(){
				var arr = this.taxonomy.options
				this.fd.code.value = ''
				arr.forEach((item)=>{
					if(item.id == this.fd.taxonomy.value){
						this.fd.code.size = item.code_length
						if(item.in_pc != 1){
							this.fd.code.disable = true
						}
						else if(item.autogen == 1){
							this.fd.code.value = item.next_code
							this.fd.code.disable = true
						}
						else{
							this.fd.code.disable = false
						}
					}
				})
			},
			emitSb(text,color){
				window.localStorage.setItem('message',text)
				window.localStorage.setItem('message_status',color)
			},
			triggerSb(text,color){
				this.sbText = text
				this.sbColor = color
				this.snackbar = true
			},
			save(){
				this.btnloading = true
				var fD = new FormData()
				fD.append('name',this.fd.name.value)
				fD.append('taxonomy_id', this.fd.taxonomy.value)
				fD.append('code', this.fd.code.value)
				var route = 'categories'
				axios.post(route,fD).then((response)=>{
					this.btnloading = false
					//this.updateTaxonomy()
					this.emitSb('Category Created Successfully','success')
					window.location.href = '{{$prev_url}}'
				}).catch((error)=> {
					if(error.response.status == 422){
						this.btnloading = false
						var errors = error.response.data.errors
						this.fd.name.error = errors.name
						this.fd.code.error = errors.code
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