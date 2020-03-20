<template>
    <v-card>
        <v-row>
            <v-col class="mt-2" cols="7"></v-col>
            <v-col class="mt-2" cols="5">
                <v-row class="d-flex justify-end" flat tile>
                    <v-col>
                        <v-menu
                            v-model="menuFrom"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    v-model="date_from"
                                    append-icon="event"
                                    label="From"
                                    :hide-details="true"
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="date_from" locale="UTC"  @input="menuFrom=false" />
                        </v-menu>
                    </v-col>
                    <v-col>
                        <v-menu
                            v-model="menuTo"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                    v-model="date_to"
                                    append-icon="event"
                                    label="To"
                                    :hide-details="true"
                                    readonly
                                    outlined
                                    dense
                                    v-on="on"
                                />
                            </template>
                            <v-date-picker v-model="date_to" locale="UTC"  @input="menuFrom=false" />
                        </v-menu>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <highcharts :options="chartOptions" />
    </v-card>
</template>

<script>
    export default {
        data(){
            return {
                menuFrom: false,
                menuTo: false,
                date_to:'',
                date_from:'',
                chartOptions: {
                    title: {
                        text: 'ManMonth',
                    },
                    xAxis: {
                        categories: [],
                    },
                    yAxis: {
                        allowDecimals: false,
                    },
                    series: [
                        {
                            name: 'ManMonth',
                            data: [],
                        }
                    ],
                    credits: {
                        enabled: false,
                    },
                },
            }
        }
    }
</script>
