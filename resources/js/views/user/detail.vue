<template>
    <Layout>
        <v-card class="mb-10">
            <BusyRate></BusyRate>
        </v-card>
        <v-card class="mt-10 mb-10">
            <ManMonth></ManMonth>
        </v-card>
        <v-card class="mt-10 mb-10">
            <Project></Project>
        </v-card>
    </Layout>
</template>
<script>
    import Layout from '../../components/Layout/index';
    import BusyRate from '../../components/user/busyrate';
    import ManMonth from '../../components/user/manmonth';
    import Project from '../../components/user/project';
    import {Chart} from 'highcharts-vue'
    export default {
        name: 'Index',
        components: {
            Layout,
            BusyRate,
            Chart,
            ManMonth,
            Project
        },
        data() {
            return {
            }
        }
    };
</script>

