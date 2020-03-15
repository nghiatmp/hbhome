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
                        <v-list-item @click="clickMenu('/myprojects')">
                            <v-list-item-action>
                                <v-icon>home</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">My Project</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                    <template>
                        <v-list-item @click="clickMenu('/teams')">
                            <v-list-item-action>
                                <v-icon>home</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Team</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <template>
                        <v-list-item @click="clickMenu('/projects')">
                            <v-list-item-action>
                                <v-icon>assignment</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Projects</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/users')">
                            <v-list-item-action>
                                <v-icon>people</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">User</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/phases')">
                            <v-list-item-action>
                                <v-icon>link</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Phase</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/overviewmm')">
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview MM</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="clickMenu('/overviewAllocate')">
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview Allcate</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="clickMenu('/activitylogs')">
                            <v-list-item-action>
                                <v-icon>mdi-call-split</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Activity Log</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="clickMenu('/holiday')">
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
                                <v-list-item-title class="font-weight-bold" @click="logout">LogOut</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>

        </v-navigation-drawer>

        <v-app-bar
            :clipped-left="$vuetify.breakpoint.lgAndUp"
            app
            color="light-blue darken-1"
            dark
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-toolbar-title style="width: 300px" class="ml-0 pl-4">
                <span class="hidden-sm-and-down white--text text--lighten-1">HBHome</span>
            </v-toolbar-title>
            <v-spacer />
            <v-icon>people</v-icon>
            <v-toolbar-title class="ml-1">
                <span class="hidden-sm-and-down white--text text--lighten-1">{{ userInfo.email }}</span>
            </v-toolbar-title>
        </v-app-bar>
        <v-content>
            <v-container>
                <slot />
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
        data: () => ({
            dialog: false,
            drawer: null,
            user : null,
        }),
        created() {
            this.getUser();
        },
        computed: {
            userInfo: function() {
                return this.$auth.user();
            },
        },
        methods: {
            getSelectMenu(){
                switch (this.$router.currentRoute.path) {
                    case '/myprojects':
                        return 1;
                    case '/teams':
                        return 2;
                    case '/projects':
                        return 3;
                    case '/users':
                        return 4;
                    case '/phases':
                        return 5;
                    case '/overviewmm':
                        return 6;
                    case '/overviewAllocate':
                        return 7;
                    case '/activitylogs':
                        return 8;
                    case '/holiday':
                        return 9;
                    default:
                        return 1;
                }
            },
            logout() {
                this.$auth.logout({
                    error: function() {
                        window.localStorage.removeItem('User_token');
                        this.$router.push({ path: '/login' });
                    },
                    redirect: '/login',
                });
            },
            clickMenu(routeName) {
                if (this.$router.currentRoute.path !== routeName) {
                    this.$router.push({ path: routeName });
                }
            },
            getUser() {
                this.axios
                .get('api/auth/me')
                .then(res => {
                })
            }
        },
    }
</script>
