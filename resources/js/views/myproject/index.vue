<template>
    <Layout>
        <v-card>
            <v-row>
                <v-col class="mt-2 ml-2" cols="3" md="3" sm="12">
                    <v-text-field
                        label="Project"
                        :hide-details="true"
                        :validate-on-blur="true"
                        outlined
                        dense
                        @input="inputDebounce"
                    /></v-col>
            </v-row>
        </v-card>
    </Layout>
</template>

<script>
    import Layout from '../../components/Layout/index';
    export default {
        name: 'Index',
        components: { Layout },
    };
</script>
