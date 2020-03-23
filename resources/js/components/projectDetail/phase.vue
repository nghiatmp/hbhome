<template>
    <v-card class="mt-5">
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
        <v-card v-if="!isLoading">
            <v-row>
                <v-col class="mt-2" cols="10" sm="9">
                    <h3 class="ml-5">Phase</h3>
                </v-col>
                <v-col class="mt-2" cols="2" sm="3">
                    <v-row class="d-flex justify-end" flat tile>
                        <v-col>
                            <v-btn depressed color="primary" @click="diaLogcreateMember=true">
                                Create Phase
                            </v-btn>
                        </v-col>
                    </v-row>
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
                                <v-icon class="ml-2">fas fa-edit</v-icon>
                                <v-icon class="ml-2" @click="diaLogChangeStatus=true">far fa-clone</v-icon>
                            </v-layout>
                        </template>
                    </v-data-table>
                </v-card>
            </v-card>
        </v-card>
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
                            <v-select
                                v-model="paramCreate.user_id"
                                :items="UserCreate"
                                label="Select User"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                            />
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
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.from_at" locale="UTC"  @input="menuFromCreate=false" />
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
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.to_at"  @input="menuToCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col class="align-center" cols="12" v-if="addToResource">
                            <v-text-field
                                v-model="paramCreate.note"
                                label="Allocation"
                                dense
                                outlined
                            />
                        </v-col>
                        <v-switch v-model="addToResource" class="mx-2" label="Add To Resource"></v-switch>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                    >Create Member</v-btn>
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
                    Create Project
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
<!--                            <div class="v-text-field__details" v-if="errExistKeyCreate">-->
<!--                                <div class="v-messages theme&#45;&#45;light error&#45;&#45;text" role="alert">-->
<!--                                    <div class="v-messages__wrapper">-->
<!--                                        <div class="v-messages__message">This key has already been set for another project</div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
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
    import {PHASE_STATUS} from "../../constants/common";
    import {between, integer, required, minValue, numeric} from "vuelidate/lib/validators";
    export default {
        data () {
            return {
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                date_from:'',
                date_to:'',
                isLoading:false,
                diaLogcreateMember:false,
                diaLogInForPhase:false,
                diaLogChangeStatus:false,
                addToResource:false,
                paramCreate: {
                    'user_id':'',
                    'role':'',
                    'Allocation':'',
                    'from_at':'',
                    'to_at':'',
                },
                paramChangeStatus: {
                    'status':'',
                    'css':'',
                    'leakage':'',
                    'ee':'',
                    'timeliness':'',
                },
                StatusPhase:[],
                'UserCreate' :[],
                'RoleCreate' : [],
                desserts: [],
                headers: [
                    { text: 'Phase', value: 'title' },
                    { text: 'Start', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Buget (MM)', value: 'budget' },
                    { text: 'User (MM)', value: 'used_effort' },
                    { text: 'Plan (MM)', value: 'plan_effort' },
                    { text: 'Status (MM)', value: 'status' },
                    { text: '', value: 'id' },
                ],
            }
        },
        computed:{
            ProjectID(){
                return this.$route.params.proID;
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
                !this.$v.paramChangeStatus.css.numeric && errors.push('Css is numeric.');
                !this.$v.paramChangeStatus.css.between && errors.push('Css is between 0% and 100%.');
                return errors
            },
            LeakageChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.leakage.$dirty) return errors;
                !this.$v.paramChangeStatus.leakage.required && errors.push('Leakage is required.');
                !this.$v.paramChangeStatus.leakage.numeric && errors.push('Leakage is numeric.');
                !this.$v.paramChangeStatus.leakage.minValue && errors.push('Please enter a value greater than or equal to 0');
                return errors
            },
            EEChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.ee.$dirty) return errors;
                !this.$v.paramChangeStatus.ee.required && errors.push('EE is required.');
                !this.$v.paramChangeStatus.ee.numeric && errors.push('EE is numeric.');
                !this.$v.paramChangeStatus.ee.minValue && errors.push('Please enter a value greater than or equal to 0');
                return errors
            },
            TimeLinessChangeErrors () {
                const errors = [];
                if (!this.$v.paramChangeStatus.timeliness.$dirty) return errors;
                !this.$v.paramChangeStatus.timeliness.required && errors.push('Css is required.');
                !this.$v.paramChangeStatus.timeliness.numeric && errors.push('Css is numeric.');
                !this.$v.paramChangeStatus.timeliness.between && errors.push('Css is between 0% and 100%.');
                return errors
            },
        },
        validations : {
            paramChangeStatus: {
                status: { required},
                css: {required, numeric, between: between(0, 100)},
                leakage: {required, numeric, minValue : 0},
                ee: {required, numeric, minValue : 0},
                timeliness: {required, numeric, between: between(0, 100)},
            }
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData()
            {
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
            changeStatusPhase(){
                this.$v.diaLogChangeStatus.$touch();
                if (!this.$v.diaLogChangeStatus.$invalid) {
                    console.log('ok');
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
        }
    }
</script>
