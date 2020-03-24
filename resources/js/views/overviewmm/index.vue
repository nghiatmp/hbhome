<template>
    <Layout>
        <v-card class="pa-3 ma-3" style="box-shadow: 0 0px 0px 0px rgba(0,0,0,.2), 0 0px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
            <v-card style="width: 800px; margin: auto">
                <v-row class="mx-5">
                    <v-col cols="6" md="12" sm="12">
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
                    <v-col cols="6" md="12" sm="12">
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
                <v-container>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th class="text-left">Name Team</th>
                                <th class="text-left">MM</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in tableData" :key="item.name">
                                <td>{{ item.name }}</td>
                                <td>{{ item.value }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
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
                params : {
                    'from': moment(new Date().toISOString().slice(0, 10)).format('YYYY-MM'),
                    'to': moment(new Date().toISOString().slice(0, 10)).format('YYYY-MM'),
                },
                tableData:[],
                menuFrom: false,
                menuTo: false,
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
                },
                deep: true,
            }
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData(){
                const params= Object.keys(this.params).reduce((prev, key) => {
                    if(this.params[key] !== null) {
                        prev[key] = this.params[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get('api/overview/mm', {params})
                    .then(res=>{
                        const dataMM = res.data;
                        const dataReturn = [
                            {
                                name: dataMM.title,
                                value: dataMM.mm,
                            },
                        ];

                        if (dataMM.children.length > 0 ) {
                            Object.values(dataMM.children).forEach(item => {
                                var child = {
                                    name : item.title,
                                    value: item.mm,
                                };
                                dataReturn.push(child);
                                if(item.children.length > 0) {
                                    Object.values(item.children).forEach(key=> {
                                        var childrend = {
                                            name : item.title+'-'+key.title,
                                            value: key.mm,
                                        };
                                        dataReturn.push(childrend);
                                    })
                                }
                            });
                        }
                        this.tableData = dataReturn;
                    })
                    .catch(()=> {
                        this.tableData=[];
                    })
            }
        }
    };
</script>
