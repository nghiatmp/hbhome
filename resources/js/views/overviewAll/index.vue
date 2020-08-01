<template>
    <Layout>
        <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
            <v-card style="width: 900px; margin: auto">
                <v-row class="mx-5">
                    <v-col cols="6" md="12" sm="12" xl="6">
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
                                    v-model="params.from"
                                    label="From"
                                    prepend-icon="event"
                                    :hide-details="true"
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="params.from" locale="UTC" :max="params.to" type="month" @input="menuFrom=false" />
                        </v-menu>
                    </v-col>
                    <v-col cols="6" md="12" sm="12" xl="6">
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
                                    v-model="params.to"
                                    label="To"
                                    prepend-icon="event"
                                    :hide-details="true"
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="params.to" locale="UTC" :min="params.from" type="month" @input="menuTo=false" />
                        </v-menu>
                    </v-col>
                </v-row>
                <v-card-title>
                    MM Information
                </v-card-title>
                <v-skeleton-loader
                    type="card"
                    v-if="isLoadingData"
                />
                <v-container v-if="!isLoadingData">
                    <v-data-table
                        :headers="headers"
                        :items="dataTable"
                        class="elevation-4 mb-4"
                        locale="US"
                    />
                </v-container>
            </v-card>
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
                isLoadingData: false,
                params: {
                    'from': moment(new Date('2020-02').toISOString().slice(0, 10)).format('YYYY-MM'),
                    'to': moment(new Date('2020-09').toISOString().slice(0, 10)).format('YYYY-MM'),
                },
                menuFrom: false,
                menuTo: false,
                headers:[],
                dataTable: [],
            }
        },
        computed:{
            changeParam(){
                return {
                    from : this.params.from,
                    to : this.params.to
                }
            }
        },
        watch:{
            changeParam :{
                handler() {
                    this.renderData();
                    this.phaseBudgetToRender();
                },
                deep: true,
            }
        },
        created(){
            this.renderData();
            this.phaseBudgetToRender();
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
                    .get('/api/totalresources', {params})
                    .then(res=>{
                        this.dataTable = res.data;
                        console.log(this.tableData);
                        this.isLoadingData = false;
                    })
                    .catch(()=> {
                        this.dataTable=[];
                        this.isLoadingData = false;
                    })
            },
            phaseBudgetToRender(){
                if(this.params.from && this.params.to) {
                    var startDate = moment(this.params.from).startOf('month');
                    var endDate = moment(this.params.to);
                    var dates = [];
                    dates.push(moment(this.params.from).format('MM-YYYY'));
                    endDate.subtract(1, "month");

                    var month = moment(startDate);
                    while( month <= endDate ) {
                        month.add(1, "month");
                        dates.push(month.format('MM-YYYY'));
                    }
                    let dataReturn = [{
                        'text': 'projects',
                        'value': 'projects'
                    }];
                    dates.forEach(value => {
                        dataReturn.push({
                            'text': value,
                            value,
                        })
                    });
                    this.headers = dataReturn;
                }
            },
        }
    };
</script>
