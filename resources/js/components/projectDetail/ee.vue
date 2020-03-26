<template>
    <v-skeleton-loader
        type="card"
        v-if="isLoading"
    />
    <v-row class="mt-5 my-2" v-else>
        <v-col cols="4" sm="6" md="6" lg="4" xs="12">
            <v-card color="#7cb342" style="color: white">
                <p  class="ml-5 font-weight-medium display-1 pt-2" v-html="budget"></p>
                <v-list-item-title class="ml-5 font-weight-medium headline">Buget (MM) </v-list-item-title>
            </v-card>
        </v-col>
        <v-col cols="4" sm="6" md="6" lg="4" xs="12">
            <v-card color="#7cb342" style="color: white">
                <p  class="ml-5 font-weight-medium display-1 pt-2" v-html="user"></p>
                <v-list-item-title class="ml-5 font-weight-medium headline">User (MM) </v-list-item-title>
            </v-card>
        </v-col>
        <v-col cols="4" sm="6" md="6" lg="4" xs="12">
            <v-card color="#7cb342" style="color: white">
                <p  class="ml-5 font-weight-medium display-1 pt-2" v-html="ee"></p>
                <v-list-item-title class="ml-5 font-weight-medium headline">EE (%) </v-list-item-title>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
    export default {
        data(){
            return {
                budget:'',
                user:'',
                ee:'',
                isLoading:false,
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
            renderData(){
                this.isLoading = true;
                const ProjectID = this.ProjectID;
                this.axios
                    .get(`/api/projects/${ProjectID}/ee`)
                    .then(res=>{
                        const dataEE =res.data;
                        this.budget = dataEE.budget ? dataEE.budget : 0;
                        this.user = dataEE.effort;
                        this.ee = dataEE.ee;
                        this.isLoading = false;

                    })
                    .catch(err=>{
                        this.isLoading = false;
                    });
            }
        }
    }
</script>
