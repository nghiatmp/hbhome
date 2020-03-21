<template>
    <v-card>
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
                </v-data-table>
            </v-card>
        </v-card>
    </v-card>
</template>

<script>
    import { PROJECT_ROLE} from '../../constants/common';
    export default {
        data(){
            return {
                isLoadingTable:false,
                menuFrom: false,
                menuTo: false,
                param: {
                    from :'',
                    to:'',
                },
                nowDate: new Date().toISOString().slice(0, 10),
                maxDate: new Date().toISOString().slice(0, 10),
                tableData:[],
                headers: [
                    { text: 'Project', value: 'project.title'},
                    { text: 'Role', value: 'role' },
                    { text: 'From', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Allocation', value: 'allocation' },
                ],
            }
        },computed:{
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
            this.createTimeDefault();
            this.renderData();
        },
        methods:{
            createTimeDefault(){
                this.param.from =  new Date(2019,11,2).toISOString().slice(0, 10)
                this.param.to =  new Date(2020,5,31).toISOString().slice(0, 10)
            },
            renderData()
            {
                this.isLoadingTable = true;
                const UserID = this.UserID;
                const params= Object.keys(this.param).reduce((prev, key) => {
                    if(this.param[key] !== null) {
                        prev[key] = this.param[key];
                    }
                    return prev;
                }, {});
                this.axios
                    .get(`/api/users/${UserID}/resources`, {params})
                    .then(res=>{
                        this.tableData = res.data.data;
                        Object.values(this.tableData).forEach(key=>{
                            key.allocation= key.allocation+'%';
                            key.role = PROJECT_ROLE.filter(role =>parseInt(role.key) === key.role)[0].value;
                        });

                        this.isLoadingTable = false;
                    })
                    .catch(err=>{
                        this.isLoadingTable = false;
                    });
            }
        }
    }
</script>
