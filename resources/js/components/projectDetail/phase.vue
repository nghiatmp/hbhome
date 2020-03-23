<template>
    <v-card class="mt-5">
        <v-skeleton-loader
            type="card"
            v-if="isLoading"
        />
        <v-card v-if="!isLoading">
            <v-row>
                <v-col class="mt-2" cols="10" sm="9">
                    <h3 class="ml-5">Phase</h3>
                </v-col>
                <v-col class="mt-2" cols="2" sm="3">
                    <v-row class="d-flex justify-end" flat tile>
                        <v-col>
                            <v-btn depressed color="primary" @click="diaLogcreateMember=true">
                                Create Phase
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-card class="pa-3" style="box-shadow: none">
                <v-card class="pa-3" style="box-shadow: none">
                    <v-data-table
                        :headers="headers"
                        :items="tableData"
                        class="elevation-4 mb-4"
                        locale="US"
                    >
                        <template v-slot:item.id="{ item }">
                            <v-layout justify-center>
                                <v-icon @click="getInForPhase(item)">info</v-icon>
                                <v-icon class="ml-2">fas fa-edit</v-icon>
                                <v-icon class="ml-2">far fa-clone</v-icon>
                            </v-layout>
                        </template>
                    </v-data-table>
                </v-card>
            </v-card>
        </v-card>
        <v-dialog
            v-model="diaLogcreateMember"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Create Member
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.user_id"
                                :items="UserCreate"
                                label="Select User"
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
                                v-model="paramCreate.role"
                                :items="RoleCreate"
                                label="Role"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                            />
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-if="addToResource">
                            <v-menu
                                v-model="menuFromCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramCreate.from_at"
                                        label="From"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.from_at" locale="UTC"  @input="menuFromCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-if="addToResource">
                            <v-menu
                                v-model="menuToCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramCreate.to_at"
                                        label="To"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.to_at"  @input="menuToCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col class="align-center" cols="12" v-if="addToResource">
                            <v-text-field
                                v-model="paramCreate.note"
                                label="Allocation"
                                dense
                                outlined
                            />
                        </v-col>
                        <v-switch v-model="addToResource" class="mx-2" label="Add To Resource"></v-switch>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                    >Create Member</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="diaLogInForPhase"
            width="500px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Phase Information
                </v-card-title>
                <v-container>
                    <v-simple-table>
                        <template v-slot:default>
                            <tbody>
                            <tr v-for="item in desserts" :key="item.name">
                                <td>{{ item.name }}</td>
                                <td>{{ item.value }}</td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-container>
                <v-card-actions>
                    <v-btn @click="diaLogInForPhase=false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
    import {PHASE_STATUS} from "../../constants/common";
    export default {
        data () {
            return {
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                date_from:'',
                date_to:'',
                isLoading:false,
                diaLogcreateMember:false,
                diaLogInForPhase:false,
                addToResource:false,
                paramCreate: {
                    'user_id':'',
                    'role':'',
                    'Allocation':'',
                    'from_at':'',
                    'to_at':'',
                },
                'UserCreate' :[],
                'RoleCreate' : [],
                desserts: [
                    {
                        name: 'Frozen Yogurt',
                        value: 159,
                    },
                ],
                headers: [
                    { text: 'Phase', value: 'title' },
                    { text: 'Start', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Buget (MM)', value: 'budget' },
                    { text: 'User (MM)', value: 'used_effort' },
                    { text: 'Plan (MM)', value: 'plan_effort' },
                    { text: 'Status (MM)', value: 'status' },
                    { text: '', value: 'id' },
                ],
            }
        },
        computed:{
            ProjectID(){
                return this.$route.params.proID;
            },
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData()
            {
                this.isLoading = true;
                const ProjectID = this.ProjectID;
                this.axios
                    .get(`/api/projects/${ProjectID}/phases`)
                    .then(res=>{
                        const dataTable =res.data.data;
                        Object.values(dataTable).forEach(key=>{
                            key.status = PHASE_STATUS.filter(status =>parseInt(status.key) === key.status)[0].value;
                        });
                        this.tableData = dataTable;
                        this.isLoading = false;
                    })
                    .catch(err=>{
                        this.isLoading = false;
                    });
            },
            getInForPhase(item) {
                this.diaLogInForPhase=true;
                const dataReturn= [];
            }
        }
    }
</script>
