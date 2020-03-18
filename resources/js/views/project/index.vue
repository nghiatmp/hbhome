<template>
    <Layout>
        <v-card>
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
                        <div class="d-flex justify-end mr-4" flat tile>
                            <v-btn
                                depressed
                                color="primary"
                                @click="createProject"
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
                            <!--                            <template v-slot:item.available_team="{ item }">-->
                            <!--                                <div v-if="item.available_team.length > 0">-->
                            <!--                                    <p v-html="item.available_team[0].title"></p>-->
                            <!--                                </div>-->
                            <!--                            </template>-->
                                <template v-slot:item.id="{ item }">
                                    <v-layout justify-center>
                                        <i class="far fa-edit" @click="GetDataUpdateUser(item)"></i>
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
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-text-field
                                v-model="paramCreate.key"
                                label="Key"
                                dense
                                outlined
                                required
                                @input="inputPm"
                            />
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
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-autocomplete
                                v-model="paramCreate.rank"
                                :items="rankCreate"
                                label="PM"
                                :hide-details="true"
                                dense
                                outlined
                                required
                                @input="inputPm"
                            />
                        </v-col>
                        <v-col class="align-center" cols="12">
                            <v-text-field
                                v-model="paramCreate.note"
                                label="Note"
                                dense
                                outlined
                                required
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        @click=""
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                        @click=""
                    >Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { debounce } from 'debounce';
    import { PROJECT_STATUS } from '../../constants/common';
    import { PROJECT_RANK,PROJECT_CONTRACT } from '../../constants/common';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                keywordPM:'',
                isLoadingTable: false,
                tableData:[],
                diaLogcreateProject:false,
                paramCreate : {
                    title:'',
                    key:'',
                    contract:'',
                    rank:'',
                    note:'',
                    team_id:'',
                    admin_id:'',
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
            ChangePM() {
                return {
                    keywordPM : this.keywordPM,
                }
            }
        },
        watch : {
            paramsChangeToRender: {
                handler() {
                    this.renderData();
                },
                deep: true,
            },
            ChangePM: {
                handler() {
                    this.getPM();
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
                this.diaLogcreateProject = true;
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
                const params = {};
                params.keyword = this.keywordPM;
                console.log(params);
                this.axios
                    .get('/api/users/suggest', {params})
                    .then(res=> {
                        const dataPm = res.data.data;
                            console.log(dataPm);
                    })
                    .catch(()=>{
                        this.allTeam = [];
                    });
            },
            inputPm: debounce(function(value) {
                this.keywordPM = value;
            }, 250),
        }
    };
</script>
