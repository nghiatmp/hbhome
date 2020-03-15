<template>
    <Layout>
        <v-card>
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
            width="600px"
        >
            <v-card>
                <v-card-title>
                    Create User
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-row align="center" class="mr-0">
                                <v-text-field placeholder="Name"/>
                            </v-row>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                            <v-row align="center" class="mr-0">
                                <v-text-field placeholder="Email"/>
                            </v-row>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="12">
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        text
                        color="primary"
                        @click="diaLogcreateUser = false"
                    >Cancel</v-btn>
                    <v-btn
                        text
                        @click="diaLogcreateUser = false"
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
                headers: [
                    { text: 'No', value: 'duration'},
                    { text: 'Full Name', value: 'full_name' },
                    { text: 'Email', value: 'email' },
                    { text: 'Team', value:'available_team'},
                    { text: 'Role', value: 'role' },
                    { text: '', value: 'id' },
                ],
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
            inputDebounce: debounce(function(value) {
                this.keyword = value;
            }, 250),
        }
    };
</script>
