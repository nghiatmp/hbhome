<template>
    <Layout>
        <v-card>
            <v-row>
                <v-col class="mt-2 ml-6" cols="3" md="3" sm="12">
                    <v-text-field
                        label="Search MyProject"
                        :hide-details="true"
                        :validate-on-blur="true"
                        outlined
                        dense
                        @input="inputDebounce"
                    /></v-col>
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
                    </v-data-table>
                </v-card>
            </v-card>

        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { PROJECT_STATUS } from '../../constants/common';
    import { debounce } from 'debounce';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                isLoadingTable: false,
                tableData:[],
                headers: [
                    { text: 'No', value: 'duration'},
                    { text: 'Name', value: 'title' },
                    { text: 'Key', value: 'key' },
                    { text: 'Start', value: 'from_at'},
                    { text: 'End', value: 'to_at' },
                    { text: 'Team', value: 'team.title' },
                    { text: 'Status', value: 'status' },
                ],
            }
        },
        computed:{
            paramsChangeToRender() {
                return {
                    keyword : this.keyword,
                };
            },
        },
        watch: {
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
        methods:{
            renderData() {
                this.isLoadingTable = true;
                const params = {};
                params.keyword = this.keyword;
                params.sort_type='asc';
                this.axios
                    .get('/api/projects/search/me', {params})
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
        }
    };
</script>
