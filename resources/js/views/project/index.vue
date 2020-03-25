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
            <v-card>
                <v-row>
                    <v-col class="mt-2 ml-6" cols="3" md="3" sm="12">
                        <v-text-field
                            label="Project"
                            :hide-details="true"
                            :validate-on-blur="true"
                            outlined
                            dense
                            @input="inputDebounce"
                        /></v-col>
                    <v-col cols="3" md="3" sm="0"></v-col>
                    <v-col class="mt-4" cols="5">
                        <div class="d-flex justify-end" flat tile>
                            <v-btn
                                depressed
                                color="primary"
                                @click="diaLogcreateProject = true"
                            >
                                Create Project
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
                            <template v-slot:item.title="{ item }">
                                <v-layout>
                                    <router-link :to="`/projects/${item.id}`" v-html="item.title"></router-link>
                                </v-layout>
                            </template>
                            <template v-slot:item.id="{ item }">
                                <v-layout justify-center>
                                    <v-icon @click="GetDataUpdateProject(item)">far fa-edit</v-icon>
                                </v-layout>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-card>

            </v-card>
        </v-card>
        <v-dialog
            v-model="diaLogcreateProject"
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
                            <v-text-field
                                v-model="paramCreate.title"
                                label="Title"
                                dense
                                outlined
                                required
                                :error-messages="TitleCreateErrors"
                                @input="$v.paramCreate.title.$touch()"
                                @blur="$v.paramCreate.title.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramCreate.key"
                                label="Key"
                                dense
                                outlined
                                required
                                :error-messages="KeyCreateErrors"
                                @input="$v.paramCreate.key.$touch()"
                                @blur="$v.paramCreate.key.$touch()"
                            />
                            <div class="v-text-field__details" v-if="errExistKeyCreate">
                                <div class="v-messages theme--light error--text" role="alert">
                                    <div class="v-messages__wrapper">
                                        <div class="v-messages__message">This key has already been set for another project</div>
                                    </div>
                                </div>
                            </div>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.contract"
                                :items="contractCreate"
                                label="Constract"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="ContractCreateErrors"
                                @change="$v.paramCreate.contract.$touch()"
                                @blur="$v.paramCreate.contract.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.rank"
                                :items="rankCreate"
                                label="Rank"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="RankCreateErrors"
                                @change="$v.paramCreate.rank.$touch()"
                                @blur="$v.paramCreate.rank.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.team_id"
                                :items="allTeam"
                                label="Team"
                                item-value="id"
                                item-text="team"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="TeamCreateErrors"
                                @change="$v.paramCreate.team_id.$touch()"
                                @blur="$v.paramCreate.team_id.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-autocomplete
                                v-model="paramCreate.admin_id"
                                :items="dataPM"
                                label="PM"
                                :hide-details="true"
                                dense
                                outlined
                                clearable
                                item-text="email"
                                item-value="id"
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
                    <v-btn
                        @click="ClearValidateCreate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="createProject"
                    >Create Project</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogcUpdateProject"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Update Project
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center" cols="6">
                            <v-text-field
                                v-model="paramUpdate.title"
                                label="Title"
                                dense
                                outlined
                                required
                                :error-messages="TitleUpdateErrors"
                                @input="$v.paramUpdate.title.$touch()"
                                @blur="$v.paramUpdate.title.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramUpdate.key"
                                label="Key"
                                dense
                                outlined
                                required
                                :error-messages="KeyUpdateErrors"
                                @input="$v.paramUpdate.key.$touch()"
                                @blur="$v.paramUpdate.key.$touch()"
                            />
                            <div class="v-text-field__details" v-if="errExistKeyCreate">
                                <div class="v-messages theme--light error--text" role="alert">
                                    <div class="v-messages__wrapper">
                                        <div class="v-messages__message">This key has already been set for another project</div>
                                    </div>
                                </div>
                            </div>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramUpdate.contract"
                                :items="contractCreate"
                                label="Constract"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="ContractUpdateErrors"
                                @change="$v.paramUpdate.contract.$touch()"
                                @blur="$v.paramUpdate.contract.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramUpdate.rank"
                                :items="rankCreate"
                                label="Rank"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="RankUpdateErrors"
                                @change="$v.paramUpdate.rank.$touch()"
                                @blur="$v.paramUpdate.rank.$touch()"
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramUpdate.team_id"
                                :items="allTeam"
                                label="Team"
                                item-value="id"
                                item-text="team"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                :error-messages="TeamUpdateErrors"
                                @change="$v.paramUpdate.team_id.$touch()"
                                @blur="$v.paramUpdate.team_id.$touch()"
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
                    <v-btn
                        @click="ClearValidateUpdate"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="UpdateProject(idUpdate)"
                    >Update Project</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { required, maxLength } from 'vuelidate/lib/validators';
    import { debounce } from 'debounce';
    import { PROJECT_STATUS } from '../../constants/common';
    import { PROJECT_RANK,PROJECT_CONTRACT } from '../../constants/common';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                dataPM: '',
                isLoadingTable: false,
                tableData:[],
                diaLogcreateProject:false,
                errExistKeyCreate:false,
                diaLogcUpdateProject:false,
                errExistKeyUpdate:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
                idUpdate:'',
                paramCreate : {
                    title:'',
                    key:'',
                    contract:'',
                    rank:'',
                    note: null,
                    team_id:'',
                    admin_id:'',
                },
                paramUpdate : {
                    title:'',
                    key:'',
                    contract:'',
                    rank:'',
                    note: '',
                    team_id:'',
                },
                rankCreate : PROJECT_RANK,
                contractCreate: PROJECT_CONTRACT,
                allTeam: [],
                headers: [
                    { text: 'No', value: 'duration'},
                    { text: 'Name', value: 'title' },
                    { text: 'Key', value: 'key' },
                    { text: 'Start', value: 'from_at'},
                    { text: 'End', value: 'to_at' },
                    { text: 'Team', value: 'team.title' },
                    { text: 'Status', value: 'status' },
                    { text: '', value: 'id' },
                ],
            }
        },
        computed:{
            paramsChangeToRender() {
                return {
                    keyword : this.keyword,
                };
            },
            TitleCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.title.$dirty) return errors;
                !this.$v.paramCreate.title.maxLength && errors.push('Title max 127 characters long');
                !this.$v.paramCreate.title.required && errors.push('Title is required.');
                return errors
            },
            KeyCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.key.$dirty) return errors;
                !this.$v.paramCreate.key.required && errors.push('Key is required');
                return errors
            },
            ContractCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.contract.$dirty) return errors;
                !this.$v.paramCreate.contract.required && errors.push('Contract is required');
                return errors
            },
            TeamCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.team_id.$dirty) return errors;
                !this.$v.paramCreate.team_id.required && errors.push('Team is required');
                return errors
            },
            RankCreateErrors () {
                const errors = [];
                if (!this.$v.paramCreate.rank.$dirty) return errors;
                !this.$v.paramCreate.rank.required && errors.push('Rank is required');
                return errors
            },
            TitleUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.title.$dirty) return errors;
                !this.$v.paramUpdate.title.maxLength && errors.push('Title max 127 characters long');
                !this.$v.paramUpdate.title.required && errors.push('Title is required.');
                return errors
            },
            KeyUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.key.$dirty) return errors;
                !this.$v.paramUpdate.key.required && errors.push('Key is required');
                return errors
            },
            ContractUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.contract.$dirty) return errors;
                !this.$v.paramUpdate.contract.required && errors.push('Contract is required');
                return errors
            },
            TeamUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.team_id.$dirty) return errors;
                !this.$v.paramUpdate.team_id.required && errors.push('Team is required');
                return errors
            },
            RankUpdateErrors () {
                const errors = [];
                if (!this.$v.paramUpdate.rank.$dirty) return errors;
                !this.$v.paramUpdate.rank.required && errors.push('Rank is required');
                return errors
            },
        },
        validations : {
            paramCreate : {
                title : { required, maxLength: maxLength(127) },
                key : { required},
                contract : {required},
                rank: {required},
                team_id : { required },
            },
            paramUpdate : {
                title : { required, maxLength: maxLength(127) },
                key : { required},
                contract : {required},
                rank: {required},
                team_id : { required },
            },
        },
        watch : {
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
            this.getPM();
        },
        methods : {
            renderData() {
                this.isLoadingTable = true;
                const params = {};
                params.keyword = this.keyword;
                params.sort_type='asc';
                this.axios
                    .get('/api/projects/search', {params})
                    .then(res=>{
                        const resData = res.data.data;
                        const DataTable = Object.keys(resData).map((key) => {
                            const dataTableItem = { ... resData[key], duration: parseInt(key)+1 };
                            return dataTableItem;
                        });
                        Object.values(DataTable).forEach(key => {
                            Object.values(PROJECT_STATUS).forEach(item => {
                                if (key.status == item.key) {
                                    key.status = item.value;
                                }
                            });
                        });
                        this.tableData = DataTable;
                        this.isLoadingTable = false;
                    })
                    .catch((err) => {
                        this.tableData = [];
                    });
            },
            inputDebounce: debounce(function(value) {
                this.keyword = value;
            }, 250),
            createProject() {
                this.$v.paramCreate.$touch();
                if (!this.$v.paramCreate.$invalid) {
                    const paramsCreate = Object.keys(this.paramCreate).reduce((prev, key) => {
                        if (this.paramCreate[key] !== '') {
                            prev[key] = this.paramCreate[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                        .post('/api/projects', paramsCreate)
                        .then(res => {
                            this.renderData();
                            this.diaLogcreateProject = false;
                            this.errExistEmailCreate = false;
                            this.ClearValidateCreate();
                            this.snackbar = true;
                            this.snackbarText = 'Add Project Success';
                            this.colors = 'success';
                        })
                        .catch((err) => {
                            if (err.response.status === 422) {
                                this.errExistKeyCreate = true;
                            } else {
                                this.ClearValidateCreate;
                                this.diaLogcreateUser = false;
                                this.snackbar = true;
                                this.snackbarText = 'Add Project False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            GetDataUpdateProject(project) {
                this.idUpdate = project.id;
                this.paramUpdate.title = project.title;
                this.paramUpdate.key = project.key;
                this.paramUpdate.note = project.note;
                this.paramUpdate.contract = PROJECT_CONTRACT.filter(contract =>parseInt(contract.key) === project.contract)[0].key;
                this.paramUpdate.rank = PROJECT_RANK.filter(rank =>parseInt(rank.key) === project.rank)[0].key;
                this.paramUpdate.team_id = this.allTeam.filter(team =>parseInt(team.id) === project.team_id)[0].id;
                this.diaLogcUpdateProject = true;
            },
            UpdateProject(id) {
                this.$v.paramUpdate.$touch();
                if (!this.$v.paramUpdate.$invalid) {
                    const paramsUpdate= Object.keys(this.paramUpdate).reduce((prev, key) => {
                        if(this.paramUpdate[key] !== null) {
                            prev[key] = this.paramUpdate[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                        .put(`/api/projects/${id}`, paramsUpdate)
                        .then(res => {
                            this.renderData();
                            this.diaLogcUpdateProject = false;
                            this.errExistKeyUpdate=false;
                            this.ClearValidateUpdate;
                            this.snackbar =  true;
                            this.snackbarText = 'Update User Success';
                            this.colors = 'success';
                        })
                        .catch((err)=> {
                            if (err.response.status === 422) {
                                this.errExistKeyCreate=true;
                            } else {
                                this.diaLogcUpdateProject = false;
                                this.ClearValidateUpdate;
                                this.snackbar =  true;
                                this.snackbarText = 'Update User False';
                                this.colors = 'error';
                            }
                        });
                }
            },
            getAllTeam() {
                this.axios
                    .get('/api/teams')
                    .then(res=> {
                        const dataTeam = res.data.data;
                        Object.values(dataTeam).forEach(key => {
                            if (key.children.length > 0) {
                                Object.values(key.children).forEach(item => {
                                    item.team = key.title+'-'+item.title;
                                    this.allTeam.push(item);
                                });
                            }
                        });
                    })
                    .catch(()=>{
                        this.allTeam = [];
                    });
            },
            getPM(){
                this.axios
                    .get('/api/users/suggestuser')
                    .then(res=> {
                        this.dataPM = res.data;
                    })
                    .catch(()=>{
                        this.dataPM = [];
                    });
            },
            ClearDateInsert() {
                this.paramCreate.title='';
                this.paramCreate.key='';
                this.paramCreate.team_id='';
                this.paramCreate.rank='';
                this.paramCreate.contract='';
                this.paramCreate.admin_id='';
                this.paramCreate.note='';
            },
            ClearValidateCreate() {
                this.diaLogcreateProject=false;
                this.$v.paramCreate.$reset();
                this.paramCreate.title='';
                this.paramCreate.key='';
                this.paramCreate.team_id='';
                this.paramCreate.rank='';
                this.paramCreate.contract='';
                this.paramCreate.admin_id='';
                this.paramCreate.note='';
                this.errExistKeyCreate = false;
            },
            ClearValidateUpdate() {
                this.diaLogcUpdateProject=false;
                this.$v.paramUpdate.$reset();
                this.paramUpdate.title='';
                this.paramUpdate.key='';
                this.paramUpdate.team_id='';
                this.paramUpdate.rank='';
                this.paramUpdate.contract='';
                this.paramUpdate.admin_id='';
                this.paramUpdate.note='';
                this.errExistKeyUpdate = false;
            },
            redirectProjectDetail(id) {
                this.$router.push({ path: `/projects/${id}` });
            }
        }
    };
</script>
