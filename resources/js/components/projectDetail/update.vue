<template>
    <v-skeleton-loader
        type="card"
        v-if="isLoading"
    />
    <v-expansion-panels  v-else>
        <v-expansion-panel>
            <v-expansion-panel-header>
                <span class="font-weight-bold headline">
                    Info Project
                </span>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
                <v-simple-table>
                    <template v-slot:default>
                        <tbody>
                        <tr v-for="item in desserts" :key="item.title">
                            <td>{{ item.title }}</td>
                            <td>{{ item.value }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
    import { PROJECT_RANK,PROJECT_CONTRACT,PROJECT_STATUS } from '../../constants/common';
    export default {
        name : 'Update',
        data() {
            return {
                isLoading:false,
                desserts: [],
            }
        },
        computed:{
            projectID() {
                return this.$route.params.proID;
            },
        },
        mounted(){
            this.$root.$on('event-change-create-phase', () => {
                this.getUserCreateResource();
                this.getDefaultDate();
                this.renderData();
            });
            this.$root.$on('event-change-update-phase', () => {
                this.getUserCreateResource();
                this.getDefaultDate();
                this.renderData();
            });
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData(){
                this.isLoading = true;
                const param =  this.projectID;
                this.axios
                    .get(`/api/projects/${param}`)
                    .then(res=>{
                        const data = res.data;
                        const dataReturn = [
                            {
                                title: 'Key',
                                value: data.key
                            },
                            {
                                title: 'Contract',
                                value: PROJECT_CONTRACT.filter(contract=>contract.key == data.contract)[0].value
                            },
                            {
                                title: 'Rank',
                                value: PROJECT_RANK.filter(rank=>rank.key == data.rank)[0].value
                            },
                            {
                                title: 'From',
                                value: data.from_at
                            },
                            {
                                title: 'To',
                                value: data.to_at
                            },
                            {
                                title: 'Status',
                                value: PROJECT_STATUS.filter(status=>status.key == data.status)[0].value
                            },
                            {
                                title: 'Team',
                                value: data.team.title
                            },
                            {
                                title: 'Note',
                                value: data.note
                            },
                        ];

                        this.desserts = dataReturn;

                        this.isLoading = false;
                    })
                    .catch((err) => {
                        this.desserts = [];
                        this.isLoading = false;
                    });
            }
        }
    }
</script>


