<template>
    <Layout>
        <v-card>
            <v-snackbar
                v-model="snackbar"
                :color="colors"
                :multi-line="mode === 'multi-line'"
                :right="true"
                :timeout="2500"
                :top="true"
                :vertical="mode === 'vertical'"
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
                                <i class="far fa-edit"></i>
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
                                    placeholder="Name"
                                    dense
                                    outlined
                                />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                                <v-text-field
                                    v-model="paramCreate.email"
                                    placeholder="Email"
                                    dense
                                    outlined
                                />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-select
                                v-model="paramCreate.team_id"
                                :items="allTeam"
                                label="Team"
                                item-value="id"
                                item-text="title"
                                :hide-details="true"
                                dense
                                outlined
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
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click="diaLogcreateUser = false"
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click="CreateUser"
                    >Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { SYSTEM_ROLE } from '../../constants/common';
    import { debounce } from 'debounce';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                tableData :[],
                isLoadingTable:false,
                diaLogcreateUser:false,
                snackbar: false,
                snackbarText:'',
                colors:'',
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
                roleCreate : SYSTEM_ROLE,
                allTeam: [],
            }
        },
        computed: {
            paramsChangeToRender() {
                return {
                    keyword : this.keyword,
                };
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
                        'id':'0',
                        'title':'None'
                    }];
                    this.allTeam = TeamDefault.concat(res.data.data)
                })
                .catch(()=>{
                    this.allTeam = [];
                });
            },
            CreateUser() {
                const paramsCreate= Object.keys(this.paramCreate).reduce((prev, key) => {
                    if(this.paramCreate[key] !== null) {
                        prev[key] = this.paramCreate[key];
                    }
                    return prev;
                }, {});
                this.axios
                .post('/api/users', paramsCreate)
                .then(res => {
                    this.renderData();
                    this.diaLogcreateUser = false;
                    this.ClearDateInsert();
                    this.snackbar =  true;
                    this.snackbarText = 'Add User Success';
                    this.colors = 'success';
                })
                .catch(()=> {
                    this.diaLogcreateUser = false;
                    this.ClearDateInsert();
                    this.snackbar =  true;
                    this.snackbarText = 'Add User False';
                    this.colors = 'error';
                });
            },
            ClearDateInsert() {
                this.paramCreate.full_name='';
                this.paramCreate.email='';
                this.paramCreate.team_id='';
                this.paramCreate.role='';
            },
            inputDebounce: debounce(function(value) {
                this.keyword = value;
            }, 250),
        }
    };
</script>
