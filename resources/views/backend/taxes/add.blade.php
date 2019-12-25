@extends('layouts.app')

@section('content')
<v-row>
    <v-col class="mx-4">
        <v-card>
            <v-card-text>
                <v-form v-on:submit.prevent="">
                    <v-row>
                        <v-col cols="12">
                            <v-text-field label="Name" v-model="fd.name"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-select :items="typeItems" label="Type" v-model="fd.type"></v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field label="Value" v-model="fd.value"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-switch v-model="fd.applyToAll" label="Apply To All" ></v-switch>
                        </v-col>
                    </v-row>
                    <v-row v-if="!fd.applyToAll">
                        <v-col cols="12" md="4">
                            <v-select :items="typeItems" label="Type" v-model="fd.type"></v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-switch v-model="fd.applyToAll" label="Apply To All" true-value=1 false-value=0></v-switch>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
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
                fd:{
                    applyToAll:true,
                    name:'',
                    type:'percentage',
                    value:'',
                },
                typeItems:[
                    {value:'percentage',text:'Percentage'},
                    {value:'value',text:'Value'}
                ]
            }
        },
        mounted(){
            axios.get('menu').then((res)=>{
                this.sidebar_left_items = res.data
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
        }
    }).$mount('#app')
</script>
@endsection
