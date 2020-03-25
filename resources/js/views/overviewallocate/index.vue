<template>
    <Layout>
        <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
            <v-row>
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
                                v-model="params.month"
                                label="Choose Month"
                                prepend-icon="event"
                                :hide-details="true"
                                readonly
                                outlined
                                dense
                                v-on="on"
                            />
                        </template>
                        <v-date-picker v-model="params.month" locale="UTC" type="month" @input="menuFrom=false" />
                    </v-menu>
                </v-col>
            </v-row>
            <v-skeleton-loader
                type="card"
                v-if="isLoadingData"
            />
            <v-tabs v-if="!isLoadingData"
                    v-model="tab"
                    background-color="light-blue darken-1"
                    class="elevation-2"
                    dark
                    :centered="centered"
                    :grow="grow"
                    :vertical="vertical"
                    :right="right"
                    :prev-icon="prevIcon ? 'mdi-arrow-left-bold-box-outline' : undefined"
                    :next-icon="nextIcon ? 'mdi-arrow-right-bold-box-outline' : undefined"
                    :icons-and-text="icons"
            >
                <v-tabs-slider />

                <v-tab href="#tab_chart">
                    Overview Allocate
                    <v-icon v-if="icons">mdi-phone</v-icon>
                </v-tab>

                <v-tab
                    href="#tab_list"
                >
                    List User Free
                    <v-icon v-if="icons">mdi-phone</v-icon>
                </v-tab>

                <v-tab-item
                    id="tab_chart"
                >
                    <v-row>
                        <v-col cols="6" md="12" sm="12"  lg="6">
                            <v-card-title class="text-center" style="width: 600px; margin:auto">
                                EE Information Overview
                            </v-card-title>
                            <v-container>
                                <v-simple-table style="margin:auto">
                                    <template v-slot:default>
                                        <thead>
                                        <tr>
                                            <th class="text-left">Name Team</th>
                                            <th class="text-left">EE</th>
                                            <th class="text-left">Budget</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="item in tableData" :key="item.name">
                                            <td>{{ item.name }}</td>
                                            <td>{{ item.ee }}</td>
                                            <td>{{ item.budget }}</td>
                                        </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-container>
                        </v-col>
                        <v-col cols="6" md="12" sm="12" lg="6">
                            <v-card-title class="text-center" style="width: 600px; margin:auto">
                                EE Information Project
                            </v-card-title>
                            <v-container>
                                <v-simple-table style="margin:auto">
                                    <template v-slot:default>
                                        <thead>
                                        <tr>
                                            <th class="text-left">Name Project</th>
                                            <th class="text-left">Name Team</th>
                                            <th class="text-left">EE</th>
                                            <th class="text-left">Budget</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="item in dataProject" :key="item.name">
                                            <td>{{ item.name }}</td>
                                            <td>{{ item.team }}</td>
                                            <td>{{ item.ee }}</td>
                                            <td>{{ item.budget }}</td>
                                        </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-container>
                        </v-col>
                    </v-row>
                </v-tab-item>
                <v-tab-item
                    id="tab_list"
                >
                    <v-container>
                        <v-simple-table style="margin:auto">
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th class="text-left">Id</th>
                                    <th class="text-left">Name User</th>
                                    <th class="text-left">Email</th>
                                    <th class="text-left">EE</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in dataFreeUser" :key="item.name">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.title }}</td>
                                    <td>{{ item.email }}</td>
                                    <td>{{ item.ee }}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-container>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import moment from 'moment-timezone';
    export default {
        name: 'Index',
        components: { Layout },
        data(){
            return {
                tab: null,
                icons: false,
                centered: false,
                grow: true,
                vertical: false,
                prevIcon: false,
                nextIcon: false,
                right: false,
                isLoadingData:false,
                params : {
                    'month': moment(new Date().toISOString().slice(0, 10)).format('YYYY-MM'),
                },
                tableData:[],
                dataFreeUser:[],
                dataProject:[],
                menuFrom: false,
                menuTo: false,
            }
        },
        computed:{
            changeParam(){
                return {
                    month : this.params.month,
                }
            }
        },
        watch:{
            changeParam :{
                handler() {
                    this.renderData();
                },
                deep: true,
            }
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData(){
                this.isLoadingData = true;
                const params= Object.keys(this.params).reduce((prev, key) => {
                    if(this.params[key] !== null) {
                        prev[key] = this.params[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get('api/overview/ee', {params})
                    .then(res=>{
                        const dataEE = res.data;
                        const dataReturn = [
                            {
                                name: dataEE.title,
                                ee: dataEE.ee,
                                budget: dataEE.budget,
                            },
                        ];
                        const dataProject=[];
                        if (dataEE.children.length > 0 ) {
                            Object.values(dataEE.children).forEach(item => {
                                if (item.title == 'Free') {
                                    this.dataFreeUser =item.children;
                                }else {
                                    var child = {
                                        name: item.title,
                                        ee: item.ee,
                                        budget: item.budget,
                                    };
                                    dataReturn.push(child);
                                    if(item.children.length > 0) {
                                        Object.values(item.children).forEach(key=> {
                                            var childrend = {
                                                name : item.title+'-'+key.title,
                                                ee: key.ee,
                                                budget: key.budget,
                                            };
                                            dataReturn.push(childrend);
                                            if(key.projects){
                                                Object.values(key.projects).forEach(val =>{
                                                    var project = {
                                                        name : val.title+'-'+val.key,
                                                        team : item.title+'-'+key.title,
                                                        ee: val.ee,
                                                        budget: val.budget,
                                                    };
                                                    dataProject.push(project);
                                                });

                                            }
                                        })
                                    }
                                }
                            });
                        }
                        this.dataProject = dataProject;
                        this.tableData = dataReturn;
                        this.isLoadingData = false;
                    })
                    .catch(()=> {
                        this.tableData=[];
                        this.isLoadingData = false;
                    })
            }
        }
    };
</script>
