<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            app
        >
            <v-list>
                <v-list-item-group color="primary">
                    <template>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>home</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">My Project</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                    <template>
                        <v-list-item >
                            <v-list-item-action>
                                <v-icon>home</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Team</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <template>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>assignment</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Projects</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>people</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">User</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>link</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Phase</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview MM</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview Allcate</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>mdi-call-split</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Activity Log</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-action>
                                <v-icon>event</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Holiday</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <v-divider />

                    <template>
                        <v-list-item link>
                            <v-list-item-action>
                                <v-icon>input</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">LogOut</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>

        </v-navigation-drawer>

        <v-app-bar
            :clipped-left="$vuetify.breakpoint.lgAndUp"
            app
            color="blue darken-3"
            dark
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-toolbar-title style="width: 300px" class="ml-0 pl-4">
                <span class="hidden-sm-and-down">HBHome</span>
            </v-toolbar-title>
            <v-spacer />
                <v-avatar size="32px" item>
                    <v-img src="https://cdn.vuetifyjs.com/images/logos/logo.svg" alt="Vuetify"/>
                </v-avatar>
            <v-toolbar-title class="ml-1">
                <span class="hidden-sm-and-down">NghiaLM</span>
            </v-toolbar-title>
        </v-app-bar>
        <v-content>
            <v-container
                class="fill-height"
                fluid
            >
                <v-row
                    align="center"
                    justify="center"
                >
                    <v-tooltip right>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                :href="source"
                                icon
                                large
                                target="_blank"
                                v-on="on"
                            >
                                <v-icon large>mdi-code-tags</v-icon>
                            </v-btn>
                        </template>
                        <span>Source</span>
                    </v-tooltip>
                    <v-tooltip right>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                icon
                                large
                                href="https://codepen.io/johnjleider/pen/MNYLdL"
                                target="_blank"
                                v-on="on"
                            >
                                <v-icon large>mdi-codepen</v-icon>
                            </v-btn>
                        </template>
                        <span>Codepen</span>
                    </v-tooltip>
                </v-row>
            </v-container>
        </v-content>
        <v-btn
            bottom
            color="pink"
            dark
            fab
            fixed
            right
            @click="dialog = !dialog"
        >
            <v-icon>mdi-plus</v-icon>
        </v-btn>
        <v-dialog
            v-model="dialog"
            width="800px"
        >
            <v-card>
                <v-card-title class="grey darken-2">
                    Create contact
                </v-card-title>
                <v-container>
                    <v-row class="mx-2">
                        <v-col
                            class="align-center justify-space-between"
                            cols="12"
                        >
                            <v-row
                                align="center"
                                class="mr-0"
                            >
                                <v-avatar
                                    size="40px"
                                    class="mx-3"
                                >
                                    <img
                                        src="//ssl.gstatic.com/s2/oz/images/sge/grey_silhouette.png"
                                        alt=""
                                    >
                                </v-avatar>
                                <v-text-field
                                    placeholder="Name"
                                />
                            </v-row>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                prepend-icon="mdi-account-card-details-outline"
                                placeholder="Company"
                            />
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                placeholder="Job title"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                prepend-icon="mdi-mail"
                                placeholder="Email"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                type="tel"
                                prepend-icon="mdi-phone"
                                placeholder="(000) 000 - 0000"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                prepend-icon="mdi-text"
                                placeholder="Notes"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <v-card-actions>
                    <v-btn
                        text
                        color="primary"
                    >More</v-btn>
                    <v-spacer />
                    <v-btn
                        text
                        color="primary"
                        @click="dialog = false"
                    >Cancel</v-btn>
                    <v-btn
                        text
                        @click="dialog = false"
                    >Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app>
</template>

<script>
    export default {
        props: {
            source: String,
        },
        data: () => ({
            dialog: false,
            drawer: null,
            items: [
                { icon: 'mdi-contacts', text: 'Contacts' },
                { icon: 'mdi-history', text: 'Frequently contacted' },
                { icon: 'mdi-content-copy', text: 'Duplicates' },
                {
                    icon: 'mdi-chevron-up',
                    'icon-alt': 'mdi-chevron-down',
                    text: 'Labels',
                    model: true,
                    children: [
                        { icon: 'mdi-plus', text: 'Create label' },
                    ],
                },
                {
                    icon: 'mdi-chevron-up',
                    'icon-alt': 'mdi-chevron-down',
                    text: 'More',
                    model: false,
                    children: [
                        { text: 'Import' },
                        { text: 'Export' },
                        { text: 'Print' },
                        { text: 'Undo changes' },
                        { text: 'Other contacts' },
                    ],
                },
                { icon: 'mdi-settings', text: 'Settings' },
                { icon: 'mdi-message', text: 'Send feedback' },
                { icon: 'mdi-help-circle', text: 'Help' },
                { icon: 'mdi-cellphone-link', text: 'App downloads' },
                { icon: 'mdi-keyboard', text: 'Go to the old version' },
            ],
        }),
    }
</script>
