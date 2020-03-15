<template>
    <Layout>
        <v-card>
            <v-card>
                <v-row>
                    <v-col class="mt-2 ml-2" cols="3" md="3" sm="12">
                        <v-text-field
                            label="Phase"
                            :hide-details="true"
                            :validate-on-blur="true"
                            outlined
                            dense
                            @input="inputDebounce"
                        />
                    </v-col cols="3" md="3" sm="12">
                    <v-col c></v-col>
                    <v-col class="mt-2" cols="4">
                        <div class="d-flex justify-end mr-4" flat tile>
                            <v-btn
                                depressed
                                color="primary">
                                Create
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>

            </v-card>
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
