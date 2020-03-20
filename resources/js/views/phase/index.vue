<template>
    <Layout>
        <v-card>
            <v-card>
                <v-row>
                    <v-col class="mt-2 ml-6" cols="3" md="3" sm="12">
                        <v-text-field
                            label="Phase"
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
                            <template v-slot:item.project_name="{ item }">
                                <v-layout>
                                    <router-link :to="`/projects/${item.id}`" v-html="item.project_name"></router-link>
                                </v-layout>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-card>

            </v-card>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { debounce } from 'debounce';
    import { PHASE_STATUS } from '../../constants/common';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                keyword: '',
                isLoadingTable: false,
                tableData:[],
                headers: [
                    { text: 'Project', value: 'project_name'},
                    { text: 'Phase', value: 'title' },
                    { text: 'Start', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Buget (MM)', value: 'budget' },
                    { text: 'User (MM)', value: 'used_effort' },
                    { text: 'Plan (MM)', value: 'plan_effort' },
                    { text: 'Status (MM)', value: 'status' },
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
        },
        methods : {
            renderData() {
                this.isLoadingTable = true;
                const params = {};
                params.keyword = this.keyword;
                params.sort_type='asc';
                this.axios
                    .get('/api/phases/search', {params})
                    .then(res=>{
                        const resData = res.data.data;
                        Object.values(resData).forEach(key => {
                            Object.values(PHASE_STATUS).forEach(item => {
                                if (key.status == item.key) {
                                    key.status = item.value;
                                }
                            });
                        });
                        this.tableData = resData;
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
