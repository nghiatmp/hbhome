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
                    <h3 class="ml-5">Member</h3>
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
                                <v-date-picker v-model="param.to" locale="UTC" :min="minfrom" :max ="maxto"  @input="menuTo=false" />
                            </v-menu>
                        </v-col>
                        <v-col>
                            <v-btn depressed color="primary" @click="diaLogcreateMember=true">
                                Create Member
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-skeleton-loader
                type="card"
                v-if="isLoadingDataMember"
            />
            <v-tabs
                v-if="!isLoadingDataMember"
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
                                    </v-layout>
                                </template>
                            </v-data-table>
                        </v-card>
                    </v-card>
                </v-tab-item>
            </v-tabs>
            <v-dialog
                v-model="diaLogcreateMember"
                width="700px"
                height="600px"
            >
                <v-card>
                    <v-card-title>
                        Create Member
                    </v-card-title>
                    <v-container>
                        <v-row class="mx-2">
                            <v-col class="align-center justify-space-between" cols="6">
                                <v-autocomplete
                                    v-model="paramCreate.user_id"
                                    :items="UserCreate"
                                    label="Select User"
                                    item-text="email"
                                    item-value="id"
                                    :hide-details="true"
                                    dense
                                    outlined
                                    clearable
                                    required
                                    :error-messages="UserCreateErrors"
                                    @change="$v.paramCreate.user_id.$touch()"
                                    @blur="$v.paramCreate.user_id.$touch()"
                                />
                                <div class="v-text-field__details mt-2" v-if="errExistUserInProject">
                                    <div class="v-messages theme--light error--text" role="alert">
                                        <div class="v-messages__wrapper">
                                            <div class="v-messages__message">This User has already been set for project</div>
                                        </div>
                                    </div>
                                </div>
                            </v-col>
                            <v-col class="align-center justify-space-between" cols="6">
                                <v-select
                                    v-model="paramCreate.role"
                                    :items="RoleCreate"
                                    label="Role"
                                    item-value="key"
                                    item-text="value"
                                    :hide-details="true"
                                    dense
                                    outlined
                                    required
                                    :error-messages="RoleCreateErrors"
                                    @change="$v.paramCreate.role.$touch()"
                                    @blur="$v.paramCreate.role.$touch()"
                                />
                            </v-col>
                            <v-col cols="6" md="6" sm="6" v-if="addToResource">
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
                                            :hide-details="true"
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
                            <v-col cols="6" md="6" sm="6" v-if="addToResource">
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
                                            :hide-details="true"
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
                            <v-col class="align-center" cols="12" v-if="addToResource">
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
                            <v-switch v-model="addToResource" class="mx-2" label="Add To Resource" @change="change"></v-switch>
                        </v-row>
                    </v-container>
                    <v-card-actions>
                        <v-btn
                            @click="ClearValidateCreate"
                        >Cancel</v-btn>
                        <v-btn
                            color="primary"
                            @click = "createMember"
                        >Create Member</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog
                v-model="diaLogUpdateMember"
                width="700px"
                height="600px"
            >
                <v-card>
                    <v-card-title v-html="nameUserUpdate">

                    </v-card-title>
                    <v-container>
                        <v-row class="mx-2">
                            <v-col class="align-center justify-space-between" cols="6">
                                <v-select
                                    v-model="paramUpdate.role"
                                    :items="Role"
                                    label="Role"
                                    item-value="key"
                                    item-text="value"
                                    :hide-details="true"
                                    dense
                                    outlined
                                    required
                                    :error-messages="RoleUpdateErrors"
                                    @change="$v.paramUpdate.role.$touch()"
                                    @blur="$v.paramUpdate.role.$touch()"
                                />
                            </v-col>
                            <v-col class="align-center justify-space-between" cols="6">
                                <v-select
                                    v-model="paramUpdate.is_member"
                                    :items="Status"
                                    label="Status"
                                    item-value="key"
                                    item-text="value"
                                    :hide-details="true"
                                    dense
                                    outlined
                                    required
                                    :error-messages="StatusUpdateErrors"
                                    @change="$v.paramUpdate.is_member.$touch()"
                                    @blur="$v.paramUpdate.is_member.$touch()"
                                />
                            </v-col>
                        </v-row>
                    </v-container>
                    <v-card-actions>
                        <v-btn @click="ClearValidateUpdate"
                        >Cancel</v-btn>
                        <v-btn
                            color="primary"
                            @click="UpdateMember"
                        >Update Member</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-card>
    </v-card>
</template>

<script>
    import {Chart} from 'highcharts-vue'
    import {PROJECT_ROLE, MEMBER_STATUS} from "../../constants/common";
    import {between, integer, required} from "vuelidate/lib/validators";
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
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
                isLoading:false,
                Role: PROJECT_ROLE,
                Status: MEMBER_STATUS,
                IdUpdate:'',
                nameUserUpdate:'',
                dataChartEmpty:false,
                errExistUserInProject:'',
                param:{
                    from:'',
                    to:'',
                },
                paramUpdate:{
                    role:'',
                    is_member:'',
                },
                minfrom:'',
                maxto:'',
                diaLogcreateMember:false,
                diaLogUpdateMember:false,
                isLoadingDataMember:false,
                addToResource:false,
                paramCreate: {
                    'user_id':'',
                    'role':'',
                    'allocation':'',
                    'from_at':'',
                    'to_at':'',
                },
                'UserCreate' :[],
                'RoleCreate' : PROJECT_ROLE,

                headers: [
                    { text: 'Full Name', value: 'user.full_name' },
                    { text: 'Email', value: 'user.email' },
                    { text: 'Role', value: 'role'},
                    { text: 'Status', value: 'is_member' },
                    { text: '', value: 'id' },
                ],
                chartOptions: {
                    chart: {
                        plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat:  '<b>{point.name}</b>: {point.effort}'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.effort}'
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Effort',
                        colorByPoint: true,
                        data: []
                    }]
                }
            }
        },
        components: {
            highcharts: Chart
        },
        validations : {
            paramUpdate : {
                role : { required},
                is_member : { required},
            },
            paramCreate : {
                user_id: { required,integer},
                role : { required},
                allocation : {required, integer, between: between(0, 100)},
                from_at: {required},
                to_at : { required },
            },
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
            RoleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.role.$dirty) return errors;
                !this.$v.paramUpdate.role.required && errors.push('Role is required.');
                return errors
            },
            StatusUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.is_member.$dirty) return errors;
                !this.$v.paramUpdate.is_member.required && errors.push('Status is required.');
                return errors
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
            this.renderData();
            this.getUserToCreate();
        },
        mounted() {
            this.$root.$on('event-change-resource', () => {
                this.renderData();
            });
            this.$root.$on('event-change-create-phase', () => {
                this.getDefaultDate();
                this.renderData();
            });
            this.$root.$on('event-change-update-phase', () => {
                this.getDefaultDate();
                this.renderData();
            });
        },
        methods:{
            renderData() {
                this.isLoadingDataMember = true;
                const ProjectID = this.ProjectID;
                const params= Object.keys(this.param).reduce((prev, key) => {
                    if(this.param[key] !== null) {
                        prev[key] = this.param[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get(`/api/projects/${ProjectID}/effort-members`, {params})
                    .then(res=>{
                        const dataMember =res.data.users;
                        if (dataMember.length == 0) {
                            this.dataChartEmpty = true;
                        } else {
                            var TotalEf = 0;
                            Object.values(dataMember).forEach(key =>{
                                TotalEf += key.effort;
                            });
                            const dataReturn=[];
                            Object.values(dataMember).forEach(key =>{
                                dataReturn.push({
                                    'name': key.name,
                                    'y':(key.effort)/TotalEf*100,
                                    'effort':key.effort
                                });
                            });
                            this.chartOptions.series[0].data = dataReturn;
                        }
                    })
                    .catch(err=>{
                        this.isLoadingDataMember = false;
                    });
                this.axios
                    .get(`/api/projects/${ProjectID}/members`, {params})
                    .then(res=>{
                        const dataMem =res.data.data;
                        Object.values(dataMem).forEach(key=>{
                            key.role = PROJECT_ROLE.filter(role =>parseInt(role.key) === key.role)[0].value;
                            key.is_member = MEMBER_STATUS.filter(status =>parseInt(status.key) === key.is_member)[0].value;
                        });
                        this.tableData = dataMem;
                        this.isLoadingDataMember = false;
                    })
                    .catch(err=>{
                        this.isLoadingDataMember = false;
                    });
            },
            getDefaultDate(){
                const ProjectID = this.ProjectID;
                this.isLoading=true;
                this.axios
                    .get(`/api/projects/${ProjectID}/effort-members`)
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
            getDataUpdate(item){
                this.nameUserUpdate = 'Update Member '+item.user.full_name;
                this.IdUpdate = item.id;
                this.paramUpdate.role = PROJECT_ROLE.filter(role =>role.value === item.role)[0].key;
                this.paramUpdate.is_member = MEMBER_STATUS.filter(status =>status.value === item.is_member)[0].key;
                this.diaLogUpdateMember = true;
            },
            createMember() {
                if (this.addToResource == false) {
                    this.$v.paramCreate.user_id.$touch();
                    this.$v.paramCreate.role.$touch();
                    if (!this.$v.paramCreate.user_id.$invalid && !this.$v.paramCreate.role.$invalid) {
                        const paramsCreate = Object.keys(this.paramCreate).reduce((prev, key) => {
                            if (this.paramCreate[key] !== '') {
                                prev[key] = this.paramCreate[key];
                            }
                            return prev;
                        }, {});
                        paramsCreate['project_id'] = this.ProjectID;
                        const ProID = this.ProjectID;
                        this.createMemberFunction(ProID, paramsCreate);
                    }
                } else {
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
                        this.createMemberFunction(ProID, paramsCreate);
                    }
                }
            },
            UpdateMember() {
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const paramUpdate = Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if (this.paramUpdate[key] !== '') {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    const IdUpdate = this.IdUpdate;
                    this.axios
                        .put(`/api/project-members/${IdUpdate}`, paramUpdate)
                        .then(res => {
                            this.renderData();
                            this.diaLogUpdateMember = false;
                            this.ClearValidateUpdate();
                            this.snackbar = true;
                            this.snackbarText = 'Update Member Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            this.ClearValidateCreate;
                            this.diaLogUpdateMember = false;
                            this.snackbar = true;
                            this.snackbarText = 'Update Member False';
                            this.colors = 'error';
                        });
                }
            },
            ClearValidateUpdate() {
                this.diaLogUpdateMember=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.role='';
                this.paramUpdate.status='';
            },
            ClearValidateCreate() {
                this.diaLogcreateMember=false;
                this.addToResource=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.user_id= null;
                this.paramCreate.role='';
                this.paramCreate.from_at='';
                this.paramCreate.to_at='';
                this.paramCreate.allocation='';
            },
            getUserToCreate(){
                this.axios
                    .get('/api/users/suggestuser')
                    .then(res=> {
                        this.UserCreate = res.data;
                    })
                    .catch(()=>{
                        this.UserCreate = [];
                    });
            },
            change(){
                if(!this.addToResource) {
                    this.$v.paramCreate.from_at.$reset();
                    this.$v.paramCreate.to_at.$reset();
                    this.$v.paramCreate.allocation.$reset();
                    this.paramCreate.from_at='';
                    this.paramCreate.to_at='';
                    this.paramCreate.allocation='';
                }
            },
            createMemberFunction(ProID,paramsCreate){
                this.axios
                    .post(`/api/projects/${ProID}/members`, paramsCreate)
                    .then(res => {
                        this.renderData();
                        this.$root.$emit('event-change-member');
                        this.diaLogcreateMember = false;
                        this.errExistUserInProject = false;
                        this.ClearValidateCreate();
                        this.snackbar = true;
                        this.snackbarText = 'Add Member To Project Success';
                        this.colors = 'success';
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            this.errExistUserInProject = true;
                        } else {
                            this.ClearValidateCreate;
                            this.diaLogcreateResource = false;
                            this.snackbar = true;
                            this.snackbarText = 'Add Member To Project False';
                            this.colors = 'error';
                        }
                    });
            }
        }
    }
</script>
