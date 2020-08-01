<template>
    <v-card class="mt-5">
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
            </v-btn></v-snackbar>
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
        <v-card v-if="!isLoading">
            <v-row>
                <v-col class="mt-2" cols="6">
                    <span class="font-weight-bold headline ml-5">
                        Phases
                    </span>
                </v-col>
                <v-col class="mt-2" cols="6" v-if="permissionAdminPM">
                    <div class="d-flex justify-end" flat tile>
                        <v-btn class="mr-5" depressed color="primary" @click="diaLogcreatePhase=true">
                            Create Phase
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
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
                                <v-icon @click="getInForPhase(item)">info</v-icon>
                                <v-icon class="ml-2" @click="getdataUpdate(item)" v-if="permissionAdminPM">fas fa-edit</v-icon>
                                <v-icon class="ml-2" @click="getdataChangeStatus(item)" v-if="permissionAdminPM">far fa-clone</v-icon>
                            </v-layout>
                        </template>
                    </v-data-table>
                </v-card>
            </v-card>
        </v-card>
        <v-dialog
            v-model="diaLogcreatePhase"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Create Phase
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <div class="v-text-field__details ml-5" v-if="errMessageCreate">
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <p class="v-messages__message" v-html="errMessageCreateDisplay"></p>
                                </div>
                            </div>
                        </div>
                        <v-col cols="12" md="12" sm="12">
                            <v-text-field
                                v-model="paramCreate.title"
                                label="Name"
                                dense
                                outlined
                                :error-messages="TitleCreateErrors"
                                @change="$v.paramCreate.title.$touch()"
                                @blur="$v.paramCreate.title.$touch()"
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
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.from_at" locale="UTC" :max="paramCreate.to_at"  @input="menuFromCreate=false" />
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
                                        :error-messages="ToCreateErrors"
                                        @change="$v.paramCreate.to_at.$touch()"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.to_at" :min="paramCreate.from_at" @input="menuToCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-for="(item, index) in phaseBudget">
                            <v-text-field
                                :key="index"
                                v-model='paramCreate.budget_details[item]'
                                :label='item'
                                dense
                                outlined
                            />
                        </v-col>
                        <v-col class="align-center" cols="12">
                            <v-text-field
                                v-model="paramCreate.note"
                                label="Note"
                                dense
                                outlined
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn @click="ClearValidateCreate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="createPhase"
                    >Create Phase</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogUpdatePhase"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Update Phase
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <div class="v-text-field__details ml-5" v-if="errMessageUpdate">
                            <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__wrapper">
                                    <p class="v-messages__message" v-html="errMessageUpdateDisplay"></p>
                                </div>
                            </div>
                        </div>
                        <v-col cols="12" md="12" sm="12">
                            <v-text-field
                                v-model="paramUpdate.title"
                                label="Name"
                                dense
                                outlined
                                :error-messages="TitleUpdateErrors"
                                @change="$v.paramUpdate.title.$touch()"
                                @blur="$v.paramUpdate.title.$touch()"
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
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.from_at" locale="UTC" :max="paramUpdate.to_at"  @input="menuFromUpdate=false" />
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
                                        :error-messages="ToUpdateErrors"
                                        @change="$v.paramUpdate.to_at.$touch()"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramUpdate.to_at" :min="paramUpdate.from_at" @input="menuToUpdate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-for="(item, index) in phaseBudgetUpdate">
                            <v-text-field
                                :key="index"
                                v-model='paramUpdate.budget_details[item]'
                                :label='item'
                                dense
                                outlined
                            />
                        </v-col>
                        <v-col class="align-center" cols="12">
                            <v-text-field
                                v-model="paramUpdate.note"
                                label="Note"
                                dense
                                outlined
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn @click="ClearValidateUpdate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="updatePhase"
                    >Update Phase</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogInForPhase"
            width="500px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Phase Information
                </v-card-title>
                <v-container>
                    <v-simple-table>
                        <template v-slot:default>
                            <tbody>
                            <tr v-for="item in desserts" :key="item.name">
                                <td>{{ item.name }}</td>
                                <td>{{ item.value }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-container>
                <v-card-actions>
                    <v-btn @click="diaLogInForPhase=false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogChangeStatus"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Update Phase
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center" cols="6">
                            <v-select
                                v-model="paramChangeStatus.status"
                                :items="StatusPhase"
                                label="Status"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="StatusChangeErrors"
                                @change="$v.paramChangeStatus.status.$touch()"
                                @blur="$v.paramChangeStatus.status.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramChangeStatus.css"
                                label="Css Point"
                                dense
                                outlined
                                required
                                :error-messages="CssChangeErrors"
                                @change="$v.paramChangeStatus.css.$touch()"
                                @blur="$v.paramChangeStatus.css.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramChangeStatus.leakage"
                                label="Leakage"
                                dense
                                outlined
                                required
                                :error-messages="LeakageChangeErrors"
                                @change="$v.paramChangeStatus.leakage.$touch()"
                                @blur="$v.paramChangeStatus.leakage.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramChangeStatus.ee"
                                label="EE"
                                dense
                                outlined
                                required
                                :error-messages="EEChangeErrors"
                                @change="$v.paramChangeStatus.ee.$touch()"
                                @blur="$v.paramChangeStatus.ee.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-text-field
                                v-model="paramChangeStatus.timeliness"
                                label="Timeliness"
                                dense
                                outlined
                                required
                                :error-messages="TimeLinessChangeErrors"
                                @change="$v.paramChangeStatus.timeliness.$touch()"
                                @blur="$v.paramChangeStatus.timeliness.$touch()"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="ClearValidateChangeStatus"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="changeStatusPhase"
                    >Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
    import {PHASE_STATUS, PROJECT_ROLE_STRING, USER_ROLE_STRING} from "../../constants/common";
    import {between, required, minValue, helpers, maxLength } from "vuelidate/lib/validators";
    const regexKey = helpers.regex('regexNumber', /^[+-]?([0-9]*[.])?[0-9]+$/);
    import moment from 'moment-timezone';
    export default {
        props:['members','currentUserNow'],
        data () {
            return {
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                menuFromUpdate:false,
                menuToUpdate:false,
                isLoading:false,
                diaLogcreatePhase:false,
                diaLogUpdatePhase:false,
                diaLogInForPhase:false,
                diaLogChangeStatus:false,
                errMessageCreate:false,
                errMessageCreateDisplay:'',
                errMessageUpdate:false,
                errMessageUpdateDisplay:'',
                snackbar: false,
                snackbarText:'',
                colors:'',
                paramCreate: {
                    'title':'',
                    'from_at':'',
                    'to_at':'',
                    'budget': '',
                    'budget_details':{},
                    'note':'',
                },
                paramUpdate: {
                    'title':'',
                    'from_at':'',
                    'to_at':'',
                    'budget': '',
                    'budget_details':{},
                    'note':'',
                },
                paramChangeStatus: {
                    'status':'',
                    'css':'',
                    'leakage':'',
                    'ee':'',
                    'timeliness':'',
                },
                phaseBudget: [],
                phaseBudgetUpdate: [],
                IdPhase:'',
                IdPhaseUpdate:'',
                StatusPhase: PHASE_STATUS,
                'UserCreate' :[],
                'RoleCreate' : [],
                desserts: [],
                headers: [
                    { text: 'Phase', value: 'title' },
                    { text: 'Start', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Buget (MM)', value: 'budget' },
                    // { text: 'User (MM)', value: 'used_effort' },
                    // { text: 'Plan (MM)', value: 'plan_effort' },
                    { text: 'Status (MM)', value: 'status' },
                    { text: '', value: 'id' },
                ],
            }
        },
        computed:{
            permissionAdminPM() {
                const members = this.members;
                const member = members.filter(member => member.user_id === this.currentUserNow.id);
                return this.currentUserNow.role === USER_ROLE_STRING.Admin || member[0].role === PROJECT_ROLE_STRING.ADMIN;
            },
            ProjectID(){
                return this.$route.params.proID;
            },
            ChangeParamDate() {
                return {
                    from_at: this.paramCreate.from_at,
                    to_at: this.paramCreate.to_at
                }
            },
            ChangeParamUpdateDate() {
                return {
                    from_at: this.paramUpdate.from_at,
                    to_at: this.paramUpdate.to_at
                }
            },
            StatusChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.status.$dirty) return errors;
                !this.$v.paramChangeStatus.status.required && errors.push('Status is required.');
                return errors
            },
            CssChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.css.$dirty) return errors;
                !this.$v.paramChangeStatus.css.required && errors.push('Css is required.');
                !this.$v.paramChangeStatus.css.regexKey && errors.push('Css is number.');
                !this.$v.paramChangeStatus.css.between && errors.push('Css is between 0% and 100%.');
                return errors
            },
            LeakageChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.leakage.$dirty) return errors;
                !this.$v.paramChangeStatus.leakage.required && errors.push('Leakage is required.');
                !this.$v.paramChangeStatus.leakage.regexKey && errors.push('Leakage is number.');
                !this.$v.paramChangeStatus.leakage.minValue && errors.push('Please enter a value greater than or equal to 0');
                return errors
            },
            EEChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.ee.$dirty) return errors;
                !this.$v.paramChangeStatus.ee.required && errors.push('EE is required.');
                !this.$v.paramChangeStatus.ee.regexKey && errors.push('EE is number.');
                !this.$v.paramChangeStatus.ee.minValue && errors.push('Please enter a value greater than or equal to 0');
                return errors
            },
            TimeLinessChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.timeliness.$dirty) return errors;
                !this.$v.paramChangeStatus.timeliness.required && errors.push('Css is required.');
                !this.$v.paramChangeStatus.timeliness.regexKey && errors.push('Css is number.');
                !this.$v.paramChangeStatus.timeliness.between && errors.push('Css is between 0% and 100%.');
                return errors
            },

            TitleCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.title.$dirty) return errors;
                !this.$v.paramCreate.title.required && errors.push('Title is required.');
                !this.$v.paramCreate.title.maxLength && errors.push('Title is 127.');

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

            TitleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.title.$dirty) return errors;
                !this.$v.paramUpdate.title.required && errors.push('Title is required.');
                !this.$v.paramUpdate.title.maxLength && errors.push('Title is 127.');

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
        watch:{
            ChangeParamDate: {
                handler() {
                    this.phaseBudgetToRender();
                },
                deep: true,
            },
            ChangeParamUpdateDate: {
                handler() {
                    this.phaseBudgetToRenderUpdate();
                },
                deep: true,
            }
        },
        validations : {
            paramChangeStatus: {
                status: { required},
                css: {required, regexKey, between: between(0, 100)},
                leakage: {required, regexKey, minValue: minValue(0)},
                ee: {required, regexKey, minValue: minValue(0)},
                timeliness: {required, regexKey, between: between(0, 100)},
            },
            paramCreate: {
                title: { required,maxLength: maxLength(127)},
                from_at: {required},
                to_at: {required},
            },

            paramUpdate: {
                title: { required,maxLength: maxLength(127)},
                from_at: {required},
                to_at: {required},
            },
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData() {
                this.isLoading = true;
                const ProjectID = this.ProjectID;
                this.axios
                    .get(`/api/projects/${ProjectID}/phases`)
                    .then(res=>{
                        const dataTable =res.data.data;
                        Object.values(dataTable).forEach(key=>{
                            key.status = PHASE_STATUS.filter(status =>parseInt(status.key) === key.status)[0].value;
                        });
                        this.tableData = dataTable;
                        this.isLoading = false;
                    })
                    .catch(err=>{
                        this.isLoading = false;
                    });
            },
            getInForPhase(item) {
                this.diaLogInForPhase=true;
                this.desserts= [
                    {
                        name: 'Name',
                        value:item.title
                    }, {
                        name: 'Status',
                        value:item.status
                    }, {
                        name: 'From',
                        value:item.from_at
                    }, {
                        name: 'To',
                        value:item.to_at
                    }, {
                        name: 'Effort (MM)',
                        value:item.used_effort
                    },{
                        name: 'Budget',
                        value:item.budget
                    },{
                        name: 'CSS Point',
                        value:item.css
                    },{
                        name: 'Timeliness (%)',
                        value: item.timeliness
                    }, {
                        name: 'EE (%)',
                        value:item.ee
                    }
                ];
            },
            getdataChangeStatus(item) {
                this.IdPhase = item.id;
                this.paramChangeStatus.status = PHASE_STATUS.filter(status => status.value === item.status)[0].key;
                this.paramChangeStatus.css = item.css;
                this.paramChangeStatus.leakage = item.leakage;
                this.paramChangeStatus.ee = item.ee;
                this.paramChangeStatus.timeliness = item.timeliness;
                this.diaLogChangeStatus = true;
            },
            createPhase(){
                this.$v.paramCreate.$touch();
                if (!this.$v.paramCreate.$invalid) {
                    const params = Object.keys(this.paramCreate).reduce((prev, key) => {
                        if (this.paramCreate[key] !== null) {
                            prev[key] = this.paramCreate[key];
                        }
                        return prev;
                    }, {});
                    params.budget = 0;
                    Object.values(params.budget_details).forEach(key=>{
                        params.budget +=parseFloat(key);
                    });
                    const ProID = this.ProjectID;
                    this.axios
                        .post(`/api/projects/${ProID}/phases`, params)
                        .then(res => {
                            this.renderData();
                            this.diaLogcreatePhase = false;
                            this.errMessageCreate = false;
                            this.$root.$emit('event-change-create-phase');
                            this.ClearValidateCreate();
                            this.snackbar = true;
                            this.snackbarText = 'Add Phase Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 422) {
                                this.errMessageCreateDisplay = "Param Invalid";
                                this.errMessageCreate = true;
                            }else if(err.response.status === 400) {
                                this.errMessageCreateDisplay = "Duration of phase is duplicate";
                                this.errMessageCreate = true;
                            } else {
                                this.ClearValidateCreate();
                                this.diaLogcreatePhase = false;
                                this.snackbar = true;
                                this.snackbarText = 'Add phase False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            getdataUpdate(item) {
               this.IdPhaseUpdate = item.id;
               this.paramUpdate.title = item.title;
               this.paramUpdate.from_at = item.from_at;
               this.paramUpdate.to_at = item.to_at;
               this.paramUpdate.budget_details = item.budget_details;
               this.phaseBudgetUpdate = Object.keys(item.budget_details);
               this.diaLogUpdatePhase = true;
            },
            updatePhase(){
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const params = Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if (this.paramUpdate[key] !== null) {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    params.budget = 0;
                    Object.values(params.budget_details).forEach(key=>{
                        params.budget +=parseFloat(key);
                    });
                    const PhaseId = this.IdPhaseUpdate;
                    console.log(params);
                    this.axios
                        .put(`/api/phases/${PhaseId}`, params)
                        .then(res => {
                            this.renderData();
                            this.$root.$emit('event-change-update-phase');
                            this.diaLogUpdatePhase = false;
                            this.errMessageUpdate = false;
                            this.ClearValidateUpdate();
                            this.snackbar = true;
                            this.snackbarText = 'Update Phase Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 422) {
                                this.errMessageUpdateDisplay = "Param Invalid";
                                this.errMessageUpdate = true;
                            }else if(err.response.status === 400) {
                                this.errMessageUpdateDisplay = "Duration of phase is duplicate";
                                this.errMessageUpdate = true;
                            } else {
                                this.ClearValidateUpdate();
                                this.diaLogUpdatePhase = false;
                                this.snackbar = true;
                                this.snackbarText = 'Update phase False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            changeStatusPhase(){
                const Id =  this.IdPhase;
                this.$v.paramChangeStatus.$touch();
                if (!this.$v.paramChangeStatus.$invalid) {
                    const params= Object.keys(this.paramChangeStatus).reduce((prev, key) => {
                        if(this.paramChangeStatus[key] !== null) {
                            prev[key] = this.paramChangeStatus[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                        .put(`/api/phases/${Id}/change-status`, params)
                        .then(res=>{
                            this.renderData();
                            this.diaLogChangeStatus = false;
                            this.ClearValidateChangeStatus();
                            this.snackbar = true;
                            this.snackbarText = 'Update Phase Success';
                            this.colors = 'success';
                        })
                        .catch(err=>{
                            this.ClearValidateChangeStatus();
                            this.diaLogChangeStatus = false;
                            this.snackbar = true;
                            this.snackbarText = 'Update Phase False';
                            this.colors = 'error';
                        });
                }
            },
            phaseBudgetToRender(){
                if(this.paramCreate.from_at && this.paramCreate.to_at) {
                    var startDate = moment(this.paramCreate.from_at).startOf('month');
                    var endDate = moment(this.paramCreate.to_at);

                    var dates = [];
                    dates.push(moment(this.paramCreate.from_at).format('MM-YYYY'));
                    endDate.subtract(1, "month");

                    var month = moment(startDate);
                    while( month <= endDate ) {
                        month.add(1, "month");
                        dates.push(month.format('MM-YYYY'));
                    }
                    this.phaseBudget = dates;
                    dates.forEach(key => {
                        this.paramCreate.budget_details[key] = '';
                    })
                }
            },
            phaseBudgetToRenderUpdate(){
                if(this.paramUpdate.from_at && this.paramUpdate.to_at) {
                    var startDate = moment(this.paramUpdate.from_at).startOf('month');
                    var endDate = moment(this.paramUpdate.to_at);

                    var dates = [];
                    dates.push(moment(this.paramUpdate.from_at).format('MM-YYYY'));
                    endDate.subtract(1, "month");

                    var month = moment(startDate);
                    while( month <= endDate ) {
                        month.add(1, "month");
                        dates.push(month.format('MM-YYYY'));
                    }
                    this.phaseBudgetUpdate = dates;
                    this.paramUpdate.budget_details = {};
                    dates.forEach(key => {
                        this.paramUpdate.budget_details[key] = '';
                    })
                }
            },
            ClearValidateChangeStatus() {
                this.diaLogChangeStatus=false;
                this.$v.paramChangeStatus.$reset();
                this.paramChangeStatus.status='';
                this.paramChangeStatus.css='';
                this.paramChangeStatus.leakage='';
                this.paramChangeStatus.ee='';
                this.paramChangeStatus.timeliness='';
            },
            ClearValidateCreate(){
                this.diaLogcreatePhase=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.title = '';
                this.paramCreate.from_at = '';
                this.paramCreate.to_at = '';
                this.errMessageCreate = false;
                this.paramCreate.budget_details = {};
                this.phaseBudget = [];
            },
            ClearValidateUpdate(){
                this.diaLogUpdatePhase=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.title = '';
                this.paramUpdate.from_at = '';
                this.paramUpdate.to_at = '';
                this.errMessageUpdate = false;
                this.paramUpdate.budget_details = {};
                this.phaseBudgetUpdate = [];
            }
        }
    }
</script>
