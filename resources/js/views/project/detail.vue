<template>
    <Layout>
        <v-card>
            <UpdateComponent/>
        </v-card>
        <v-card>
            <EE/>
        </v-card>
        <v-row class="mt-5 my-2">
            <v-col cols="6" sm="6" md="6" lg="6" xs="6">
                <router-link :to="`/projects/member/${projectId}`" style="text-decoration: none">
                    <v-card color="#7cb342" style="color: white">
                        <p  class="ml-5 font-weight-medium display-1 pt-2"></p>
                        <v-list-item-title class="ml-5 font-weight-medium headline">Member Project </v-list-item-title>
                        <p  class="ml-5 font-weight-medium display-1 pt-2"></p>
                    </v-card>
                </router-link>
            </v-col>
            <v-col cols="6" sm="6" md="6" lg="6" xs="6">
                <router-link :to="`/projects/resource/${projectId}`" style="text-decoration: none">
                    <v-card color="#7cb342" style="color: white">
                        <p  class="ml-5 font-weight-medium display-1 pt-2"></p>
                        <v-list-item-title class="ml-5 font-weight-medium headline">Resource Project </v-list-item-title>
                        <p  class="ml-5 font-weight-medium display-1 pt-2"></p>
                    </v-card>
                </router-link>
            </v-col>

        </v-row>
        <v-card>
            <Phase v-bind:members="members" v-bind:currentUserNow="currentUserNow"/>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import UpdateComponent from '../../components/projectDetail/update';
    import Resourves from '../../components/projectDetail/resources';
    import Member from '../../components/projectDetail/member';
    import Phase from '../../components/projectDetail/phase';
    import EE from '../../components/projectDetail/ee';
    import {MEMBER_STATUS, PROJECT_ROLE, PROJECT_ROLE_STRING, USER_ROLE_STRING} from "../../constants/common";
    export default {
        name: 'Index',
        components: {
            Layout,
            UpdateComponent,
            Resourves,
            Member,
            EE,
            Phase
        },
        data() {
            return {
                members : [],
                currentUserNow : null,
                projectId: null,
            }
        },
        computed : {
            ProjectID(){
                return this.$route.params.proID;
            },
        },
        created() {
            this.renderData();
            this.getUser();
        },
        methods : {
            renderData() {
                const ProjectID = this.ProjectID;
                this.projectId=this.ProjectID;
                this.axios
                    .get(`/api/projects/${ProjectID}/members`)
                    .then(res=>{
                        this.members =res.data.data;
                    })
                    .catch(err=>{
                    });
            },
            getUser() {
                this.axios
                    .get('/api/auth/me')
                    .then(res => {
                        this.currentUserNow=res.data;
                    })
            }
        }
    };
</script>
