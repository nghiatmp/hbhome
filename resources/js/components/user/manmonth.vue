<template>
    <v-card>
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
        <v-card v-if="!isLoading">
            <v-row>
                <v-col class="mt-2" cols="7"></v-col>
                <v-col class="mt-2" cols="5">
                    <v-row class="d-flex justify-end" flat tile>
                        <v-col>
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
                                        v-model="param.from"
                                        append-icon="event"
                                        label="From"
                                        :hide-details="true"
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="param.from" locale="UTC" :max="param.to" @input="menuFrom=false" />
                            </v-menu>
                        </v-col>
                        <v-col>
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
                                        v-model="param.to"
                                        append-icon="event"
                                        label="To"
                                        :hide-details="true"
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="param.to" locale="UTC" :min="param.from" @input="menuTo=false" />
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <highcharts :options="chartOptions" />
        </v-card>
    </v-card>
</template>

<script>
    export default {
        data(){
            return {
                isLoading:false,
                menuFrom: false,
                menuTo: false,
                nowDate: new Date().toISOString().slice(0, 10),
                maxDate: new Date().toISOString().slice(0, 10),
                param : {
                    from:'',
                    to:'',
                },
                chartOptions: {
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: 'ManMonth',
                    },
                    xAxis: {
                        categories: [],
                    },
                    yAxis: {
                        allowDecimals: false,
                    },
                    series: [
                        {
                            name: 'ManMonth',
                            data: [],
                        }
                    ],
                    credits: {
                        enabled: false,
                    },
                },
            }
        },
        computed:{
            UserID(){
                return this.$route.params.userID;
            },
            ParamChange() {
                return {
                    from : this.param.from,
                    to : this.param.to,
                };
            }
        },
            watch: {
                ParamChange:{
                    handler(){
                        this.renderData();
                    },
                    deep:true,
                }
            },
        created(){
            this.renderData();
        },
        methods:{
            renderData()
            {
                this.isLoading = true;
                const UserID = this.UserID;
                const params= Object.keys(this.param).reduce((prev, key) => {
                    if(this.param[key] !== null) {
                        prev[key] = this.param[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get(`/api/users/${UserID}/effort`, {params})
                    .then(res=>{
                        const dataManMonth=res.data.efforts;
                        const datamonth=[];
                        const dataMM=[];
                        Object.values(dataManMonth).forEach(key =>{
                            datamonth.push(key.month);
                            dataMM.push(key.mm);
                        });
                        this.chartOptions.xAxis.categories= datamonth;
                        this.chartOptions.series[0].data = dataMM;
                        this.param.from = res.data.from;
                        this.param.to = res.data.to;
                        this.isLoading = false;
                    })
                    .catch(err=>{
                        this.isLoading = false;
                    });
            }
        }
    }
</script>
