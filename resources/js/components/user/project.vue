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
        <v-card class="pa-3" style="box-shadow: none">
            <v-card class="pa-3" style="box-shadow: none">
                <v-skeleton-loader
                    type="card"
                    v-if="isLoadingTable"
                />
                <v-data-table
                    v-if="!isLoadingTable"
                    :headers="headers"
                    :items="tableData"
                    class="elevation-4 mb-4"
                    locale="US"
                >
                </v-data-table>
            </v-card>
        </v-card>
    </v-card>
</template>

<script>
    export default {
        data(){
            return {
                isLoadingTable:false,
                menuFrom: false,
                menuTo: false,
                date_to:'',
                date_from:'',
                tableData:[],
                headers: [
                    { text: 'Project', value: 'project_name'},
                    { text: 'Role', value: 'title' },
                    { text: 'From', value: 'from_at' },
                    { text: 'End', value: 'to_at'},
                    { text: 'Allocation', value: 'budget' },
                ],
            }
        }
    }
</script>
