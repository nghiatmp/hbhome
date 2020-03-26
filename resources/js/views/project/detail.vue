<template>
    <Layout>
        <v-card>
            <UpdateComponent/>
        </v-card>
        <v-card>
            <EE/>
        </v-card>
        <v-card>
            <Resourves/>
        </v-card>
        <v-card>
            <Member/>
        </v-card>
        <v-card>
            <Phase/>
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
            }
        }
    };
</script>
