<template>
    <v-card class="mt-5 mb-5">
        <v-row>
            <v-col class="mt-2" cols="6" md="6" sm="6">
                <h3 class="ml-5">Member</h3>
            </v-col>
            <v-col class="mt-2" cols="6">
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
                    <v-col>
                        <v-btn depressed color="primary" @click="diaLogcreateMember=true">
                            Create Member
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-tabs
            v-model="tab"
            background-color="light-blue darken-1"
            class="elevation-2"
            dark
            :centered="centered"
            :grow="grow"
            :vertical="vertical"
            :right="right"
            :prev-icon="prevIcon ? 'mdi-arrow-left-bold-box-outline' : undefined"
            :next-icon="nextIcon ? 'mdi-arrow-right-bold-box-outline' : undefined"
            :icons-and-text="icons"
        >
            <v-tabs-slider />

            <v-tab href="#tab_chart">
                Chart
                <v-icon v-if="icons">mdi-phone</v-icon>
            </v-tab>

            <v-tab
                href="#tab_list"
            >
                List
                <v-icon v-if="icons">mdi-phone</v-icon>
            </v-tab>

            <v-tab-item
                id="tab_chart"
            >
                <highcharts :options="chartOptions"></highcharts>
            </v-tab-item>
            <v-tab-item
                id="tab_list"
            >
                <v-card class="pa-3" style="box-shadow: none">
                    <v-card class="pa-3" style="box-shadow: none">
                        <v-skeleton-loader
                            type="card"
                        />
                        <v-data-table
                            :headers="headers"
                            :items="tableData"
                            class="elevation-4 mb-4"
                            locale="US"
                        >
                        </v-data-table>
                    </v-card>
                </v-card>
            </v-tab-item>
        </v-tabs>
        <v-dialog
            v-model="diaLogcreateMember"
            width="700px"
            height="600px"
        >
            <v-card>
                <v-card-title>
                    Create Member
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.user_id"
                                :items="UserCreate"
                                label="Select User"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                            />
                        </v-col>
                        <v-col class="align-center justify-space-between" cols="6">
                            <v-select
                                v-model="paramCreate.role"
                                :items="RoleCreate"
                                label="Role"
                                item-value="key"
                                item-text="value"
                                :hide-details="true"
                                dense
                                outlined
                                required
                            />
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-if="addToResource">
                            <v-menu
                                v-model="menuFromCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramCreate.from_at"
                                        label="From"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.from_at" locale="UTC"  @input="menuFromCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col cols="6" md="6" sm="6" v-if="addToResource">
                            <v-menu
                                v-model="menuToCreate"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                        v-model="paramCreate.to_at"
                                        label="To"
                                        append-icon="event"
                                        :hide-details="true"
                                        clearable
                                        readonly
                                        outlined
                                        dense
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker v-model="paramCreate.to_at"  @input="menuToCreate=false" />
                            </v-menu>
                        </v-col>
                        <v-col class="align-center" cols="12" v-if="addToResource">
                            <v-text-field
                                v-model="paramCreate.note"
                                label="Allocation"
                                dense
                                outlined
                            />
                        </v-col>
                        <v-switch v-model="addToResource" class="mx-2" label="Add To Resource"></v-switch>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                    >Cancel</v-btn>
                    <v-btn
                        color="primary"
                    >Create Member</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
    import {Chart} from 'highcharts-vue'
    export default {
        data () {
            return {
                tab: null,
                icons: false,
                centered: false,
                grow: true,
                vertical: false,
                prevIcon: false,
                nextIcon: false,
                right: false,
                tableData:[],
                menuFrom:false,
                menuTo:false,
                menuFromCreate:false,
                menuToCreate:false,
                date_from:'',
                date_to:'',
                diaLogcreateMember:false,
                addToResource:false,
                paramCreate: {
                    'user_id':'',
                    'role':'',
                    'Allocation':'',
                    'from_at':'',
                    'to_at':'',
                },
                'UserCreate' :[],
                'RoleCreate' : [],

                headers: [
                    { text: 'No', value: 'project_name'},
                    { text: 'Full Name', value: 'title' },
                    { text: 'Email', value: 'from_at' },
                    { text: 'Role', value: 'to_at'},
                    { text: 'Status', value: 'budget' },
                    { text: '', value: 'used_effort' },
                ],
                chartOptions: {
                    chart: {
                        plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                    },
                    title: {
                        text: 'Browser market shares in January, 2018'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                enabled: true,
                                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    },
                    series: [{
                        name: 'Brands',
                        colorByPoint: true,
                        data: [{
                            name: 'Internet Explorer',
                            y: 11.84
                        }, {
                            name: 'Firefox',
                            y: 10.85
                        }, {
                            name: 'Edge',
                            y: 4.67
                        }, {
                            name: 'Safari',
                            y: 4.18
                        }]
                    }]
                }
            }
        },
        components: {
            highcharts: Chart
        }
    }
</script>
