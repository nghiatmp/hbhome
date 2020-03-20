<template>
    <v-expansion-panels>
        <v-expansion-panel>
            <v-expansion-panel-header>
                    <v-row>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-list-item>
                                <v-list-item-content>Key:</v-list-item-content>
                                <v-list-item-content v-html="project.key"></v-list-item-content>
                            </v-list-item>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-list-item>
                                <v-list-item-content>Contract:</v-list-item-content>
                                <v-list-item-content  v-html="project.contract"></v-list-item-content>
                            </v-list-item>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-list-item>
                                <v-list-item-content>Rank:</v-list-item-content>
                                <v-list-item-content  v-html="project.rank"></v-list-item-content>
                            </v-list-item>
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-list-item>
                                <v-list-item-content>Status:</v-list-item-content>
                                <v-list-item-content  v-html="project.status"></v-list-item-content>
                            </v-list-item>
                        </v-col>
                    </v-row>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
                <v-row>
                    <v-col class="align-center justify-space-between" cols="6">
                        <v-list-item>
                            <v-list-item-content>From:</v-list-item-content>
                            <v-list-item-content  v-html="project.from"></v-list-item-content>
                        </v-list-item>
                    </v-col>
                    <v-col class="align-center justify-space-between" cols="6">
                        <v-list-item>
                            <v-list-item-content>To:</v-list-item-content>
                            <v-list-item-content  v-html="project.to"></v-list-item-content>
                        </v-list-item>
                    </v-col>
                    <v-col class="align-center justify-space-between" cols="6">
                        <v-list-item>
                            <v-list-item-content>Team:</v-list-item-content>
                            <v-list-item-content  v-html="project.team"></v-list-item-content>
                        </v-list-item>
                    </v-col>
                    <v-col class="align-center justify-space-between" cols="6">
                        <v-list-item>
                            <v-list-item-content>Note:</v-list-item-content>
                            <v-list-item-content  v-html="project.note"></v-list-item-content>
                        </v-list-item>
                    </v-col>
                </v-row>
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
                project: {
                    'key' : '',
                    'contract':'',
                    'rank':'',
                    'from':'',
                    'to':'',
                    'status':'',
                    'team':'',
                    'note':'',
                }
            }
        },
        computed:{
            projectID() {
                return this.$route.params.proID;
            },
        },
        created(){
            this.renderData();
        },
        methods:{
            renderData(){
                const param =  this.projectID;
                this.axios
                    .get(`/api/projects/${param}`)
                    .then(res=>{
                        const data = res.data;
                        this.project.key = data.key;
                        this.project.contract = PROJECT_CONTRACT.filter(contract=>contract.key == data.contract)[0].value;
                        this.project.rank = PROJECT_RANK.filter(rank=>rank.key == data.rank)[0].value;
                        this.project.from = data.from_at;
                        this.project.to= data.to_at;
                        this.project.status= PROJECT_STATUS.filter(status=>status.key == data.status)[0].value;;
                        this.project.team= data.team.title;
                        this.project.note= data.note;
                    })
            }
        }
    }
</script>


