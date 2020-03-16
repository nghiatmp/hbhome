<template>
    <v-col class="mt-4" cols="5">
        <div class="d-flex justify-end mr-4" flat tile>
            <v-btn
                depressed
                color="primary"
                @click="diaLogcreateUser = true"
            >
                Create
            </v-btn>
        </div>
    </v-col>
    <v-dialog
        v-model="diaLogcreateUser"
        width="600px"
    >
        <v-card>
            <v-card-title>
                Create User
            </v-card-title>
            <v-container>
                <v-row class="mx-2">
                    <v-col class="align-center justify-space-between" cols="12">
                        <v-row align="center" class="mr-0">
                            <v-text-field placeholder="Name"/>
                        </v-row>
                    </v-col>
                    <v-col class="align-center justify-space-between" cols="12">
                        <v-row align="center" class="mr-0">
                            <v-text-field placeholder="Email"/>
                        </v-row>
                    </v-col>
                    <v-col class="align-center justify-space-between" cols="12">
                    </v-col>
                </v-row>
            </v-container>
            <v-card-actions>
                <v-btn
                    text
                    color="primary"
                    @click="diaLogcreateUser = false"
                >Cancel</v-btn>
                <v-btn
                    text
                    @click="diaLogcreateUser = false"
                >Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
