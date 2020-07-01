<template>
    <Layout>
        <v-card>
            <UpdateComponent/>
        </v-card>
        <v-card>
            <EE/>
        </v-card>
        <v-card>
            <Resourves v-bind:members="members" v-bind:currentUserNow="currentUserNow"/>
        </v-card>
        <v-card>
            <Member v-bind:members="members" v-bind:currentUserNow="currentUserNow"/>
        </v-card>
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
