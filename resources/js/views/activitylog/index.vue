<template>
    <Layout>
        <v-card>
            <v-card-title class="title">
                Activity Log
            </v-card-title>
            <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
                <v-row>
                    <v-col cols="12" md="3" sm="12">
                        <v-select
                            v-model="params.project_id"
                            :items="projects"
                            label="Project"
                            item-value="id"
                            item-text="title"
                            :hide-details="true"
                            outlined
                            dense
                        />
                    </v-col>
                    <v-col cols="12" md="3" sm="12">
                        <v-select
                            v-model="params.type"
                            :items="type"
                            label="Type"
                            item-value="key"
                            item-text="value"
                            :hide-details="true"
                            outlined
                            dense
                        />
                    </v-col>
                    <v-col cols="12" md="3" sm="12">
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
                                    v-model="params.date_from"
                                    label="From"
                                    prepend-icon="event"
                                    :hide-details="true"
                                    clearable
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="params.date_from" locale="UTC" :max="nowDate" @input="menuFrom=false" />
                        </v-menu>
                    </v-col>
                    <v-col cols="12" md="3" sm="12">
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
                                    v-model="params.date_to"
                                    label="To"
                                    prepend-icon="event"
                                    :hide-details="true"
                                    clearable
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="params.date_to" locale="UTC" :min="params.date_from" :max="nowDate" @input="menuTo=false" />
                        </v-menu>
                    </v-col>
                </v-row>
            </v-card>
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
                    />
                </v-card>
            </v-card>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import { TYPE_ACTIVITY_LOG,TYPE_ACTIVITY_LOG_DEFAULT } from '../../constants/common';
    export default {
        name: 'Index',
        components: { Layout },
        data() {
            return {
                pagination: {
                    page: 1,
                    totalPage: 1,
                    total: 0,
                    from: 0,
                    to: 0,
                },
                params: {
                    project_id: '',
                    type: TYPE_ACTIVITY_LOG_DEFAULT,
                    date_from: '',
                    date_to: '',
                },
                nowDate: new Date().toISOString().slice(0, 10),
                menuFrom: false,
                menuTo: false,
                headers: [
                    {text: 'No', value: 'no'},
                    {text: 'Type', value: 'type'},
                    {text: 'Content', value: 'content'},
                    {text: 'User', value: 'full_name', align: 'center'},
                    {text: 'Email', value: 'email', align: 'center'},
                    {text: 'Date', value: 'created_at'},
                ],
                projects: [],
                type : TYPE_ACTIVITY_LOG,
                isLoadingTable: false,
                tableData : [],
            }
        },
        computed: {
            paramsChangeToRender() {
                return {
                    project_id : this.params.project_id,
                    type: this.params.type,
                    date_from: this.params.date_from,
                    date_to: this.params.date_to,
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
            this.getProject();
        },
        methods: {
            renderData() {
                this.isLoadingTable = true;
                if (this.params.project_id) {
                    const params= Object.keys(this.params).reduce((prev, key) => {
                        if(this.params[key] !== null) {
                            prev[key] = this.params[key];
                        }
                        return prev;
                    }, {});
                    this.axios
                    .get('api/activity-logs', {params})
                    .then(res=>{
                        const resData = res.data.data;
                        const DataTable = Object.keys(resData).map((key) => {
                            const dataTableItem = { ... resData[key], duration: key };
                            return dataTableItem;
                        });
                        let no = 1;
                        Object.keys(DataTable).forEach(key => {
                            DataTable[key]['no'] = no;
                            no++;
                        });
                        this.tableData = DataTable;
                        this.isLoadingTable = false;
                    })
                    .catch(()=> {
                        this.tableData = [];
                        this.isLoadingTable = false;
                    })

                }
            },
            getProject() {
                this.axios
                .get('api/projects/all')
                .then(res => {
                    this.projects = res.data.data;
                    if (this.projects.length > 0 ) {
                        this.params.project_id = this.projects[0].id;
                    }
                })
                .catch(()=>  {
                    this.projects = [];
                })
            },
        }
    };
</script>
