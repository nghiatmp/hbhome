<template>
    <Layout>
        <v-card>
            <Resourves v-bind:members="members" v-bind:currentUserNow="currentUserNow"/>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    import Resourves from '../../components/projectDetail/resources';
    export default {
        name: 'Index',
        components: {
            Layout,
            Resourves,
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
