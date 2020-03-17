<template>
    <Layout>
        <v-card>
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
            <v-row>
                <v-col class="mt-2 ml-2" cols="3" md="3" sm="6">
                    <v-text-field
                        class="pa-3"
                        label="UserName"
                        :hide-details="true"
                        :validate-on-blur="true"
                        outlined
                        dense
                        @input="inputDebounce"
                    ></v-text-field>
                </v-col>
                <v-col cols="3" md="3" sm="0"></v-col>
                <v-col class="mt-4" cols="5">
                    <div class="d-flex justify-end mr-4" flat tile>
                        <v-btn
                            depressed
                            color="primary"
                            @click="diaLogcreateUser = true"
                            >
                            Create
                        </v-btn>
                    </div>
                </v-col>
            </v-row>
            <v-card class="pa-3" style="box-shadow: none">
                <v-card class="pa-3" style="box-shadow: none">
                    <v-skeleton-loader
                        type="card"
                        v-if="isLoadingTable"
                    />
                    <v-data-table
                        v-if="!isLoadingTable"
                        :headers="headers"
                        :items="tableData"
                        class="elevation-4 mb-4"
                        locale="US"
                    >
                        <template v-slot:item.available_team="{ item }">
                            <div v-if="item.available_team.length > 0">
                                <p v-html="item.available_team[0].title"></p>
                            </div>
                        </template>
                        <template v-slot:item.id="{ item }">
                            <v-layout justify-center>
                                <i class="far fa-edit" @click="GetDataUpdateUser(item)"></i>
                            </v-layout>
                        </template>
                    </v-data-table>
                </v-card>
            </v-card>
        </v-card>
        <v-dialog
            v-model="diaLogcreateUser"
            width="560px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Create User
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center " cols="12">
                                <v-text-field
                                    v-model="paramCreate.full_name"
                                    :error-messages="FullnameCreateErrors"
                                    placeholder="Name"
                                    dense
                                    outlined
                                    required
                                    @input="$v.paramCreate.full_name.$touch()"
                                    @blur="$v.paramCreate.full_name.$touch()"
                                />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                                <v-text-field
                                    v-model="paramCreate.email"
                                    :error-messages="emailCreateErrors"
                                    placeholder="Email"
                                    dense
                                    outlined
                                    required
                                    @input="$v.paramCreate.email.$touch()"
                                    @blur="$v.paramCreate.email.$touch()"
                                />
                            <div class="v-text-field__details" v-if="errExistEmailCreate">
                                <div class="v-messages theme--light error--text" role="alert">
                                    <div class="v-messages__wrapper">
                                        <div class="v-messages__message">This email has already been set for another user</div>
                                    </div>
                                </div>
                            </div>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramCreate.team_id"
                                :error-messages="teamCreateErrors"
                                :items="allTeam"
                                label="Team"
                                item-value="id"
                                item-text="title"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                @change="$v.paramCreate.team_id.$touch()"
                                @blur="$v.paramCreate.team_id.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramCreate.role"
                                :items="roleCreate"
                                label="Role"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="roleCreateErrors"
                                @change="$v.paramCreate.role.$touch()"
                                @blur="$v.paramCreate.role.$touch()"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="ClearValidateCreate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="CreateUser"
                    >Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogUpdateUser"
            width="560px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Update User
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center " cols="12">
                            <v-text-field
                                v-model="paramUpdate.full_name"
                                :error-messages="FullnameUpdateErrors"
                                placeholder="Name"
                                dense
                                outlined
                                required
                                @input="$v.paramUpdate.full_name.$touch()"
                                @blur="$v.paramUpdate.full_name.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-text-field
                                v-model="paramUpdate.email"
                                :error-messages="emailUpdateErrors"
                                placeholder="Email"
                                dense
                                outlined
                                required
                                @input="$v.paramUpdate.email.$touch()"
                                @blur="$v.paramUpdate.email.$touch()"
                            />
                            <div class="v-text-field__details" v-if="errExistEmailUpdate">
                                <div class="v-messages theme--light error--text" role="alert">
                                    <div class="v-messages__wrapper">
                                        <div class="v-messages__message">This email has already been set for another user</div>
                                    </div>
                                </div>
                            </div>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramUpdate.team_id"
                                :error-messages="teamUpdateErrors"
                                :items="allTeam"
                                label="Team"
                                item-value="id"
                                item-text="title"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                @change="$v.paramUpdate.team_id.$touch()"
                                @blur="$v.paramUpdate.team_id.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramUpdate.role"
                                :items="roleCreate"
                                label="Role"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="roleUpdateErrors"
                                @change="$v.paramUpdate.role.$touch()"
                                @blur="$v.paramUpdate.role.$touch()"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="ClearValidateUpdate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="UpdateUser(idUpdate)"
                    >Update</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { SYSTEM_ROLE } from '../../constants/common';
    import { debounce } from 'debounce';
    import { required, maxLength,email } from 'vuelidate/lib/validators';
    import { validationMixin } from 'vuelidate'
    export default {
        mixins: [validationMixin],
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                tableData :[],
                isLoadingTable:false,
                diaLogcreateUser:false,
                diaLogUpdateUser:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
                errExistEmailCreate:false,
                errExistEmailUpdate:false,
                headers: [
                    { text: 'No', value: 'duration'},
                    { text: 'Full Name', value: 'full_name' },
                    { text: 'Email', value: 'email' },
                    { text: 'Team', value:'available_team'},
                    { text: 'Role', value: 'role' },
                    { text: '', value: 'id' },
                ],
                paramCreate : {
                    full_name : '',
                    email : '',
                    team_id : '',
                    role : '',
                },
                paramUpdate : {
                    full_name : '',
                    email : '',
                    team_id : '',
                    role : '',
                },
                idUpdate:'',
                roleCreate : SYSTEM_ROLE,
                allTeam: [],
            }
        },
        validations : {
            paramCreate : {
                full_name : { required, maxLength: maxLength(127) },
                email : { required, email},
                team_id : { required },
                role : { required },
            },
            paramUpdate : {
                full_name : { required, maxLength: maxLength(127) },
                email : { required, email},
                team_id : { required },
                role : { required },
            },
        },
        computed: {
            paramsChangeToRender() {
                return {
                    keyword : this.keyword,
                };
            },
            FullnameCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.full_name.$dirty) return errors;
                !this.$v.paramCreate.full_name.maxLength && errors.push('FullName must be at most 10 characters long');
                !this.$v.paramCreate.full_name.required && errors.push('FullName is required.');
                return errors
            },
            emailCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.email.$dirty) return errors;
                !this.$v.paramCreate.email.email && errors.push('Must be valid e-mail');
                !this.$v.paramCreate.email.required && errors.push('E-mail is required');
                return errors
            },
            teamCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.team_id.$dirty) return errors;
                !this.$v.paramCreate.team_id.required && errors.push('Team is required');
                return errors
            },
            roleCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.role.$dirty) return errors;
                !this.$v.paramCreate.role.required && errors.push('Role is required');
                return errors
            },
            FullnameUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.full_name.$dirty) return errors;
                !this.$v.paramUpdate.full_name.maxLength && errors.push('FullName must be at most 10 characters long');
                !this.$v.paramUpdate.full_name.required && errors.push('FullName is required.');
                return errors
            },
            emailUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.email.$dirty) return errors;
                !this.$v.paramUpdate.email.email && errors.push('Must be valid e-mail');
                !this.$v.paramUpdate.email.required && errors.push('E-mail is required');
                return errors
            },
            teamUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.team_id.$dirty) return errors;
                !this.$v.paramUpdate.team_id.required && errors.push('Team is required');
                return errors
            },
            roleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.role.$dirty) return errors;
                !this.$v.paramUpdate.role.required && errors.push('Role is required');
                return errors
            },
        },
        watch:{
            paramsChangeToRender: {
                handler() {
                    this.renderData();
                },
                deep: true,
            },
        },
        created() {
            this.renderData();
            this.getAllTeam();
        },
        methods : {
            renderData() {
                this.isLoadingTable = true;
                const params = {};
                params.keyword = this.keyword;
                params.sort_type='asc',
                this.axios
                    .get('api/users/search', {params})
                    .then(res=>{
                        const resData = res.data.data;
                        const DataTable = Object.keys(resData).map((key) => {
                            const dataTableItem = { ... resData[key], duration: parseInt(key)+1 };
                            return dataTableItem;
                        });
                        Object.values(DataTable).forEach(key => {
                            Object.values(SYSTEM_ROLE).forEach(item => {
                                if (key.role == item.key) {
                                    key.role = item.value;
                                }
                            });
                        });
                        this.tableData = DataTable;
                        this.isLoadingTable = false;
                    })
                    .catch(()=> {
                        this.tableData = [];
                        this.isLoadingTable = false;
                    })

            },
            getAllTeam() {
                this.axios
                .get('/api/teams')
                .then(res=> {
                    var TeamDefault = [{
                        'id':0,
                        'title':'None'
                    }];
                    this.allTeam = TeamDefault.concat(res.data.data)
                })
                .catch(()=>{
                    this.allTeam = [];
                });
            },
            CreateUser() {
                this.$v.paramCreate.$touch();
                if (!this.$v.paramCreate.$invalid) {
                    const paramsCreate= Object.keys(this.paramCreate).reduce((prev, key) => {
                        if(this.paramCreate[key] !== null) {
                            prev[key] = this.paramCreate[key];
                        }
                        return prev;
                    }, {});
                    if (paramsCreate.team_id == 0 ) {
                        delete paramsCreate.team_id;
                    }
                    this.axios
                        .post('/api/users', paramsCreate)
                        .then(res => {
                            this.renderData();
                            this.diaLogcreateUser = false;
                            this.errExistEmailCreate=false;
                            this.ClearDateInsert();
                            this.$v.paramCreate.$reset();
                            this.snackbar =  true;
                            this.snackbarText = 'Add User Success';
                            this.colors = 'success';
                        })
                        .catch((err)=> {
                            if (err.response.status === 422) {
                                this.errExistEmailCreate=true;
                            } else {
                                this.$v.paramCreate.$reset();
                                this.diaLogcreateUser = false;
                                this.ClearDateInsert();
                                this.snackbar =  true;
                                this.snackbarText = 'Add User False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            GetDataUpdateUser(user) {
                this.paramUpdate.full_name = user.full_name;
                this.paramUpdate.email = user.email;
                this.paramUpdate.role = SYSTEM_ROLE.filter(role =>role.value === user.role)[0].key;
                this.idUpdate = user.id;
                if (user.available_team.length > 0) {
                    this.paramUpdate.team_id = this.allTeam.filter(team =>team.title === user.available_team[0].title)[0].id;
                } else {
                    this.paramUpdate.team_id = this.allTeam[0].id;
                }
                this.diaLogUpdateUser = true;
            },
            UpdateUser(id) {
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const paramsUpdate= Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if(this.paramUpdate[key] !== null ) {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    if (paramsUpdate.team_id == 0 ) {
                        delete paramsUpdate.team_id;
                    }
                    this.axios
                        .put(`/api/users/${id}`, paramsUpdate)
                        .then(res => {
                            this.renderData();
                            this.diaLogUpdateUser = false;
                            this.errExistEmailUpdate=false;
                            this.ClearDateUpdate();
                            this.$v.paramUpdate.$reset();
                            this.snackbar =  true;
                            this.snackbarText = 'Update User Success';
                            this.colors = 'success';
                        })
                        .catch((err)=> {
                            if (err.response.status === 422) {
                                this.errExistEmailUpdate=true;
                            } else {
                                this.$v.paramUpdate.$reset();
                                this.diaLogUpdateUser = false;
                                this.ClearDateUpdate;
                                this.snackbar =  true;
                                this.snackbarText = 'Update User False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            ClearDateInsert() {
                this.paramCreate.full_name='';
                this.paramCreate.email='';
                this.paramCreate.team_id='';
                this.paramCreate.role='';
            },
            ClearValidateCreate() {
                this.diaLogcreateUser=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.full_name='';
                this.paramCreate.email='';
                this.paramCreate.team_id='';
                this.paramCreate.role='';
                this.errExistEmailCreate=false;
            },
            ClearDateUpdate() {
                this.paramUpdate.full_name='';
                this.paramUpdate.email='';
                this.paramUpdate.team_id='';
                this.paramUpdate.role='';
            },
            ClearValidateUpdate() {
                this.diaLogUpdateUser=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.full_name='';
                this.paramUpdate.email='';
                this.paramUpdate.team_id='';
                this.paramUpdate.role='';
                this.errExistEmailUpdate=false;
            },
            inputDebounce: debounce(function(value) {
                this.keyword = value;
            }, 250),
        }
    };
</script>
