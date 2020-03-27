<template>
    <v-card class="mt-5 mb-5">
        <v-snackbar
            v-model="snackbar"
            :color="colors"
            :right="true"
            :timeout="2500"
            :top="true"
        >
            {{ snackbarText }}
            <v-btn
                dark
                text
                @click="snackbar = false"
            >
                Close
            </v-btn>
        </v-snackbar>
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
       <v-card v-if="!isLoading">
           <v-row>
               <v-col class="mt-2" cols="6" md="6" sm="6">
                   <span class="font-weight-bold headline ml-5">
                    Resource
                    </span>
               </v-col>
               <v-col class="mt-2" cols="6">
                   <v-row class="d-flex justify-end" flat tile>
                       <v-col>
                           <v-menu
                               v-model="menuFrom"
                               :close-on-content-click="false"
                               :nudge-right="40"
                               transition="scale-transition"
                               offset-y
                               min-width="290px"
                           >
                               <template v-slot:activator="{ on }">
                                   <v-text-field
                                       v-model="param.from"
                                       append-icon="event"
                                       label="From"
                                       :hide-details="true"
                                       readonly
                                       outlined
                                       dense
                                       v-on="on"
                                   />
                               </template>
                               <v-date-picker v-model="param.from" locale="UTC" :min="minfrom" :max="param.to" @input="menuFrom=false" />
                           </v-menu>
                       </v-col>
                       <v-col>
                           <v-menu
                               v-model="menuTo"
                               :close-on-content-click="false"
                               :nudge-right="40"
                               transition="scale-transition"
                               offset-y
                               min-width="290px"
                           >
                               <template v-slot:activator="{ on }">
                                   <v-text-field
                                       v-model="param.to"
                                       append-icon="event"
                                       label="To"
                                       :hide-details="true"
                                       readonly
                                       outlined
                                       dense
                                       v-on="on"
                                   />
                               </template>
                               <v-date-picker v-model="param.to" locale="UTC"  :min="minfrom" :max ="maxto" @input="menuTo=false" />
                           </v-menu>
                       </v-col>
                       <v-col>
                           <v-btn depressed color="primary" @click="diaLogcreateResource=true">
                               Create Resource
                           </v-btn>
                       </v-col>
                   </v-row>
               </v-col>
           </v-row>
           <v-skeleton-loader
               type="card"
               v-if="isLoadingDataResource"
           />
           <v-card style="margin-bottom: 50px">
               <v-tabs v-if="!isLoadingDataResource"
                       v-model="tab"
                       background-color="light-blue darken-1"
                       class="elevation-2"
                       dark
                       :centered="centered"
                       :grow="grow"
                       :vertical="vertical"
                       :right="right"
                       :prev-icon="prevIcon ? 'mdi-arrow-left-bold-box-outline' : undefined"
                       :next-icon="nextIcon ? 'mdi-arrow-right-bold-box-outline' : undefined"
                       :icons-and-text="icons"
               >
                   <v-tabs-slider />

                   <v-tab href="#tab_chart">
                       Chart
                       <v-icon v-if="icons">mdi-phone</v-icon>
                   </v-tab>

                   <v-tab
                       href="#tab_list"
                   >
                       List
                       <v-icon v-if="icons">mdi-phone</v-icon>
                   </v-tab>

                   <v-tab-item
                       id="tab_chart"
                   >
                       <v-card v-if="dataChartEmpty">
                           <v-list-item-title style="padding: 20px 50px">Nothing To Show</v-list-item-title>
                       </v-card>
                       <highcharts v-if="!dataChartEmpty" :options="chartOptions"></highcharts>
                   </v-tab-item>
                   <v-tab-item
                       id="tab_list"
                   >
                       <v-card class="pa-3" style="box-shadow: none">
                           <v-card class="pa-3" style="box-shadow: none">
                               <v-data-table
                                   :headers="headers"
                                   :items="tableData"
                                   class="elevation-4 mb-4"
                                   locale="US"
                               >
                                   <template v-slot:item.id="{ item }">
                                       <v-layout justify-center>
                                           <v-icon @click="getDataUpdate(item)">far fa-edit mr-2</v-icon>
                                           <v-icon  @click="detete(item.id)">far fa-trash-alt</v-icon>
                                       </v-layout>
                                   </template>
                               </v-data-table>
                           </v-card>
                       </v-card>
                   </v-tab-item>
               </v-tabs>
           </v-card>
           <v-dialog
               v-model="diaLogcreateResource"
               width="700px"
               height="600px"
           >
               <v-card>
                   <v-card-title>
                       Create Resource
                   </v-card-title>
                   <v-container>
                       <v-row class="mx-2">
                           <v-col class="align-center justify-space-between" cols="6">
                               <v-select
                                   v-model="paramCreate.user_id"
                                   :items="UserCreate"
                                   label="Select User"
                                   item-value="id"
                                   item-text="full_name"
                                   dense
                                   outlined
                                   required
                                   :error-messages="UserCreateErrors"
                                   @change="$v.paramCreate.user_id.$touch()"
                                   @blur="$v.paramCreate.user_id.$touch()"
                               />
                           </v-col>
                           <v-col class="align-center justify-space-between" cols="6">
                               <v-select
                                   v-model="paramCreate.role"
                                   :items="RoleCreate"
                                   label="Role"
                                   item-value="key"
                                   item-text="value"
                                   dense
                                   outlined
                                   required
                                   :error-messages="RoleCreateErrors"
                                   @change="$v.paramCreate.role.$touch()"
                                   @blur="$v.paramCreate.role.$touch()"
                               />
                           </v-col>
                           <v-col cols="6" md="6" sm="6">
                               <v-menu
                                   v-model="menuFromCreate"
                                   :close-on-content-click="false"
                                   :nudge-right="40"
                                   transition="scale-transition"
                                   offset-y
                                   min-width="290px"
                               >
                                   <template v-slot:activator="{ on }">
                                       <v-text-field
                                           v-model="paramCreate.from_at"
                                           label="From"
                                           append-icon="event"
                                           clearable
                                           readonly
                                           outlined
                                           dense
                                           v-on="on"
                                           :error-messages="FromCreateErrors"
                                           @change="$v.paramCreate.from_at.$touch()"
                                           @blur="$v.paramCreate.from_at.$touch()"
                                       />
                                   </template>
                                   <v-date-picker v-model="paramCreate.from_at" :min="minfrom" :max="maxto"  @input="menuFromCreate=false" />
                               </v-menu>
                           </v-col>
                           <v-col cols="6" md="6" sm="6">
                               <v-menu
                                   v-model="menuToCreate"
                                   :close-on-content-click="false"
                                   :nudge-right="40"
                                   transition="scale-transition"
                                   offset-y
                                   min-width="290px"
                               >
                                   <template v-slot:activator="{ on }">
                                       <v-text-field
                                           v-model="paramCreate.to_at"
                                           label="To"
                                           append-icon="event"
                                           clearable
                                           readonly
                                           outlined
                                           dense
                                           v-on="on"
                                           :error-messages="ToCreateErrors"
                                           @change="$v.paramCreate.to_at.$touch()"
                                           @blur="$v.paramCreate.to_at.$touch()"

                                       />
                                   </template>
                                   <v-date-picker v-model="paramCreate.to_at" :min="minfrom" :max ="maxto" @input="menuToCreate=false" />
                               </v-menu>
                           </v-col>
                           <v-col class="align-center" cols="12">
                               <v-text-field
                                   v-model="paramCreate.allocation"
                                   label="Allocation"
                                   dense
                                   outlined
                                   :error-messages="AllocationCreateErrors"
                                   @change="$v.paramCreate.allocation.$touch()"
                                   @blur="$v.paramCreate.allocation.$touch()"
                               />
                           </v-col>
                       </v-row>
                   </v-container>
                   <div class="v-text-field__details ml-5" v-if="errMessageCreate">
                       <div class="v-messages theme--light error--text" role="alert">
                           <div class="v-messages__wrapper">
                               <p class="v-messages__message" v-html="errMessage"></p>
                           </div>
                       </div>
                   </div>
                   <v-card-actions>
                       <v-btn @click="ClearValidateCreate"
                       >Cancel</v-btn>
                       <v-btn
                           color="primary"
                           @click="createResource"
                       >Create Resource</v-btn>
                   </v-card-actions>
               </v-card>
           </v-dialog>
           <v-dialog
               v-model="diaLogdeleteResource"
               width="700px"
               height="600px"
           >
               <v-card>
                   <v-card-title>
                       Remove Resource
                   </v-card-title>
                   <v-card-subtitle>
                       Do you want to detete ?
                   </v-card-subtitle>
                   <v-card-actions>
                       <v-btn @click="diaLogdeleteResource = false"
                       >Cancel</v-btn>
                       <v-btn
                           color="error"
                           @click="deleteResource"
                       >Delete</v-btn>
                   </v-card-actions>
               </v-card>
           </v-dialog>
           <v-dialog
               v-model="diaLogUpdateResource"
               width="700px"
               height="600px"
           >
               <v-card>
                   <v-card-title>
                       Update Resource
                   </v-card-title>
                   <v-container>
                       <v-row class="mx-2">
                           <v-col class="align-center justify-space-between" cols="6">
                               <v-select
                                   v-model="paramUpdate.user_id"
                                   label="UserName"
                                   :items="UserCreate"
                                   item-value="id"
                                   item-text="full_name"
                                   dense
                                   outlined
                                   readonly
                                   required
                                   :error-messages="UserUpdateErrors"
                                   @change="$v.paramUpdate.user_id.$touch()"
                                   @blur="$v.paramUpdate.user_id.$touch()"
                               />
                           </v-col>
                           <v-col class="align-center justify-space-between" cols="6">
                               <v-select
                                   v-model="paramUpdate.role"
                                   :items="RoleCreate"
                                   label="Role"
                                   item-value="key"
                                   item-text="value"
                                   dense
                                   outlined
                                   required
                                   :error-messages="RoleUpdateErrors"
                                   @change="$v.paramUpdate.role.$touch()"
                                   @blur="$v.paramUpdate.role.$touch()"
                               />
                           </v-col>
                           <v-col cols="6" md="6" sm="6">
                               <v-menu
                                   v-model="menuFromUpdate"
                                   :close-on-content-click="false"
                                   :nudge-right="40"
                                   transition="scale-transition"
                                   offset-y
                                   min-width="290px"
                               >
                                   <template v-slot:activator="{ on }">
                                       <v-text-field
                                           v-model="paramUpdate.from_at"
                                           label="From"
                                           append-icon="event"
                                           clearable
                                           readonly
                                           outlined
                                           dense
                                           v-on="on"
                                           :error-messages="FromUpdateErrors"
                                           @change="$v.paramUpdate.from_at.$touch()"
                                           @blur="$v.paramUpdate.from_at.$touch()"
                                       />
                                   </template>
                                   <v-date-picker v-model="paramUpdate.from_at" :min="minfrom" :max="maxto"  @input="menuFromUpdate=false" />
                               </v-menu>
                           </v-col>
                           <v-col cols="6" md="6" sm="6">
                               <v-menu
                                   v-model="menuToUpdate"
                                   :close-on-content-click="false"
                                   :nudge-right="40"
                                   transition="scale-transition"
                                   offset-y
                                   min-width="290px"
                               >
                                   <template v-slot:activator="{ on }">
                                       <v-text-field
                                           v-model="paramUpdate.to_at"
                                           label="To"
                                           append-icon="event"
                                           clearable
                                           readonly
                                           outlined
                                           dense
                                           v-on="on"
                                           :error-messages="ToUpdateErrors"
                                           @change="$v.paramUpdate.to_at.$touch()"
                                           @blur="$v.paramUpdate.to_at.$touch()"

                                       />
                                   </template>
                                   <v-date-picker v-model="paramUpdate.to_at" :min="minfrom" :max ="maxto" @input="menuToUpdate=false" />
                               </v-menu>
                           </v-col>
                           <v-col class="align-center" cols="12">
                               <v-text-field
                                   v-model="paramUpdate.allocation"
                                   label="Allocation"
                                   dense
                                   outlined
                                   :error-messages="AllocationUpdateErrors"
                                   @change="$v.paramUpdate.allocation.$touch()"
                                   @blur="$v.paramUpdate.allocation.$touch()"
                               />
                           </v-col>
                       </v-row>
                   </v-container>
                   <div class="v-text-field__details ml-5" v-if="errMessageUpdate">
                       <div class="v-messages theme--light error--text" role="alert">
                           <div class="v-messages__wrapper">
                               <p class="v-messages__message" v-html="errMessageUpdateDisplay"></p>
                           </div>
                       </div>
                   </div>
                   <v-card-actions>
                       <v-btn @click="ClearValidateUpdate"
                       >Cancel</v-btn>
                       <v-btn
                           color="primary"
                           @click="UpdateResource"
                       >Update Resource</v-btn>
                   </v-card-actions>
               </v-card>
           </v-dialog>
       </v-card>
    </v-card>
</template>

<script>
    import {Chart} from 'highcharts-vue'
    import { PROJECT_ROLE} from "../../constants/common";
    import { required,integer,between} from "vuelidate/lib/validators";
    export default {
        data () {
            return {
                tab: null,
                icons: false,
                centered: false,
                grow: true,
                vertical: false,
                prevIcon: false,
                nextIcon: false,
                right: false,
                isLoading:false,
                isLoadingDataResource:false,
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                menuFromUpdate:false,
                menuToUpdate:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
                idResource:'',
                idResourceUpdate:'',
                param:{
                    from:'',
                    to:'',
                },
                minfrom:'',
                maxto:'',
                diaLogcreateResource:false,
                diaLogdeleteResource:false,
                diaLogUpdateResource:false,
                paramCreate: {
                    user_id:'',
                    role:'',
                    allocation:'',
                    from_at:'',
                    to_at:'',
                },
                paramUpdate: {
                    user_id:'',
                    role:'',
                    allocation:'',
                    from_at:'',
                    to_at:'',
                },
                dataChartEmpty:false,
                errMessageCreate:'',
                errMessage:'',
                errMessageUpdate:'',
                errMessageUpdateDisplay:'',
                UserCreate :[],
                RoleCreate : PROJECT_ROLE,
                headers: [
                    { text: 'Full Name', value: 'user.full_name'},
                    { text: 'Role', value: 'role' },
                    { text: 'From', value: 'from_at' },
                    { text: 'To', value: 'to_at'},
                    { text: 'AllCation', value: 'allocation' },
                    { text: '', value: 'id' },
                ],
                chartOptions: {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'Resource',
                    },
                    xAxis: {
                        categories: [],
                    },
                    yAxis: {
                        allowDecimals: false,
                    },
                    series: [{
                        name: 'Resource',
                        data: [],
                    }]
                }
            }
        },
        components: {
            highcharts: Chart
        },
        computed:{
            ProjectID(){
                return this.$route.params.proID;
            },
            ParamChange() {
                return {
                    from : this.param.from,
                    to : this.param.to,
                };
            },
            UserCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.user_id.$dirty) return errors;
                !this.$v.paramCreate.user_id.integer && errors.push('User_id inval');
                !this.$v.paramCreate.user_id.required && errors.push('User is required.');
                return errors
            },
            RoleCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.role.$dirty) return errors;
                !this.$v.paramCreate.role.required && errors.push('Role is required.');
                return errors
            },
            AllocationCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.allocation.$dirty) return errors;
                !this.$v.paramCreate.allocation.required && errors.push('Allocation is required.');
                !this.$v.paramCreate.allocation.integer && errors.push('Allocation is integer.');
                !this.$v.paramCreate.allocation.between && errors.push('Allocation is between 0% and 100%.');
                return errors
            },
            FromCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.from_at.$dirty) return errors;
                !this.$v.paramCreate.from_at.required && errors.push('StartDate is required.');
                return errors
            },
            ToCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.to_at.$dirty) return errors;
                !this.$v.paramCreate.to_at.required && errors.push('EndDate is required.');
                return errors
            },

            UserUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.user_id.$dirty) return errors;
                !this.$v.paramUpdate.user_id.integer && errors.push('User_id inval');
                !this.$v.paramUpdate.user_id.required && errors.push('User is required.');
                return errors
            },
            RoleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.role.$dirty) return errors;
                !this.$v.paramUpdate.role.required && errors.push('Role is required.');
                return errors
            },
            AllocationUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.allocation.$dirty) return errors;
                !this.$v.paramUpdate.allocation.required && errors.push('Allocation is required.');
                !this.$v.paramUpdate.allocation.integer && errors.push('Allocation is integer.');
                !this.$v.paramUpdate.allocation.between && errors.push('Allocation is between 0% and 100%.');
                return errors
            },
            FromUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.from_at.$dirty) return errors;
                !this.$v.paramUpdate.from_at.required && errors.push('StartDate is required.');
                return errors
            },
            ToUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.to_at.$dirty) return errors;
                !this.$v.paramUpdate.to_at.required && errors.push('EndDate is required.');
                return errors
            },
        },
        validations : {
            paramCreate : {
                user_id: { required,integer},
                role : { required},
                allocation : {required, integer, between: between(0, 100)},
                from_at: {required},
                to_at : { required },
            },
            paramUpdate : {
                user_id: { required,integer},
                role : { required},
                allocation : {required, integer, between: between(0, 100)},
                from_at: {required},
                to_at : { required },
            },
        },
        watch: {
            ParamChange:{
                handler(){
                    this.renderData();
                },
                deep:true,
            }
        },
        created(){
            this.getDefaultDate();
            this.getUserCreateResource();
            this.renderData();
        },
        mounted() {
            this.$root.$on('event-change-member', () => {
                this.getUserCreateResource();
                this.renderData();
            });
            this.$root.$on('event-change-create-phase', () => {
                this.getUserCreateResource();
                this.getDefaultDate();
                this.renderData();
            });
            this.$root.$on('event-change-update-phase', () => {
                this.getUserCreateResource();
                this.getDefaultDate();
                this.renderData();
            });
        },
        methods:{
            renderData()
            {
                this.isLoadingDataResource = true;
                const ProjectID = this.ProjectID;
                const params= Object.keys(this.param).reduce((prev, key) => {
                    if(this.param[key] !== null) {
                        prev[key] = this.param[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get(`/api/projects/${ProjectID}/effort`, {params})
                    .then(res=>{
                        const dataResource =res.data.efforts;
                        if (dataResource.length == 0) {
                            this.dataChartEmpty = true;
                        } else {
                            const datamonth=[];
                            const dataMM=[];
                            Object.values(dataResource).forEach(key =>{
                                datamonth.push(key.month);
                                dataMM.push(key.mm);
                            });
                            this.chartOptions.xAxis.categories= datamonth;
                            this.chartOptions.series[0].data = dataMM;
                        }
                    })
                    .catch(err=>{
                        this.isLoadingDataResource = false;
                    });
                this.axios
                    .get(`/api/projects/${ProjectID}/resources`, {params})
                    .then(res=>{
                        const dataResource =res.data.data;
                        Object.values(dataResource).forEach(key=>{
                            key.allocation= key.allocation+'%';
                            key.role = PROJECT_ROLE.filter(role =>parseInt(role.key) === key.role)[0].value;
                        });
                        this.tableData = dataResource;
                        this.isLoadingDataResource = false;
                    })
                    .catch(err=>{
                        this.isLoadingDataResource = false;
                    });
            },
            getDefaultDate(){
                const ProjectID = this.ProjectID;
                this.isLoading=true;
                this.axios
                    .get(`/api/projects/${ProjectID}/effort`)
                    .then(res=>{
                        this.minfrom = res.data.from;
                        this.maxto = res.data.to;
                        this.param.from = res.data.from;
                        this.param.to = res.data.to;
                        this.isLoading = false;
                    })
                    .catch(err=>{
                        this.isLoading = false;
                    });
            },
            getUserCreateResource(){
                const ProjectID = this.ProjectID;
                this.axios
                    .get(`/api/projects/${ProjectID}/members`)
                    .then(res=>{
                        const dataMem =res.data.data;
                        const data = [];
                        Object.values(dataMem).forEach(key=>{
                            data.push(key.user);
                        });
                        this.UserCreate = data;
                    })
                    .catch(err=>{
                        this.UserCreate = [];
                    });
            },
            createResource() {
                this.$v.paramCreate.$touch();
                if (!this.$v.paramCreate.$invalid) {
                    const paramsCreate = Object.keys(this.paramCreate).reduce((prev, key) => {
                        if (this.paramCreate[key] !== '') {
                            prev[key] = this.paramCreate[key];
                        }
                        return prev;
                    }, {});
                    paramsCreate['project_id'] = this.ProjectID;
                    const ProID = this.ProjectID;
                    this.axios
                        .post(`/api/projects/${ProID}/resources`, paramsCreate)
                        .then(res => {
                            this.renderData();
                            this.diaLogcreateResource = false;
                            this.errMessageCreate = false;
                            this.ClearValidateCreate();
                            this.$root.$emit('event-change-resource');
                            this.snackbar = true;
                            this.snackbarText = 'Add Resource Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 400) {
                                this.errMessageCreate = true;
                                this.errMessage = err.response.data.message;
                            } else {
                                this.ClearValidateCreate;
                                this.diaLogcreateResource = false;
                                this.snackbar = true;
                                this.snackbarText = 'Add Resource False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            UpdateResource() {
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const paramUpdate = Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if (this.paramUpdate[key] !== '') {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    const IdResource = this.idResourceUpdate;
                    this.axios
                        .put(`/api/resources/${IdResource}`, paramUpdate)
                        .then(res => {
                            this.renderData();
                            this.$root.$emit('event-change-resource');
                            this.diaLogUpdateResource = false;
                            this.errMessageUpdate = false;
                            this.ClearValidateUpdate();
                            this.snackbar = true;
                            this.snackbarText = 'Update Resource Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 400) {
                                this.errMessageUpdate = true;
                                this.errMessageUpdateDisplay = err.response.data.message;
                            } else {
                                this.ClearValidateCreate;
                                this.diaLogcreateResource = false;
                                this.snackbar = true;
                                this.snackbarText = 'Update Resource False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            deleteResource(){
                const reId = this.idResource;
                this.axios
                    .delete(`/api/resources/${reId}`)
                    .then(res=>{
                        this.renderData();
                        this.$root.$emit('event-change-resource');
                        this.diaLogdeleteResource = false;
                        this.snackbar = true;
                        this.snackbarText = 'Remove Resource Success';
                        this.colors = 'success';
                    })
                    .catch(err=>{
                        this.diaLogdeleteResource = false;
                        this.snackbar = true;
                        this.snackbarText = 'Remove Resource Error';
                        this.colors = 'error';
                    });
            },
            detete(id) {
                this.idResource = id;
                this.diaLogdeleteResource=true;
            },
            getDataUpdate(item){
                this.idResourceUpdate = item.id;
                this.paramUpdate.user_id= item.user_id;
                this.paramUpdate.role = PROJECT_ROLE.filter(role =>role.value === item.role)[0].key;
                this.paramUpdate.from_at= item.from_at;
                this.paramUpdate.to_at= item.to_at;
                this.paramUpdate.allocation= item.allocation.replace('%','');
                this.diaLogUpdateResource = true;
            },
            ClearValidateCreate() {
                this.diaLogcreateResource=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.user_id='';
                this.paramCreate.role='';
                this.paramCreate.from_at='';
                this.paramCreate.to_at='';
                this.paramCreate.allocation='';
            },
            ClearValidateUpdate() {
                this.diaLogUpdateResource=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.user_id='';
                this.paramUpdate.role='';
                this.paramUpdate.from_at='';
                this.paramUpdate.to_at='';
                this.paramUpdate.allocation='';
            },
        }
    }
</script>
