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
                        <v-list-item @click="clickMenu('/teams')" v-if="permissionAdminLeader">
                            <v-list-item-action>
                                <v-icon>home</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Team</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>

                    <template>
                        <v-list-item @click="clickMenu('/projects')" v-if="permissionAdmin">
                            <v-list-item-action>
                                <v-icon>assignment</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Projects</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/users')" v-if="permissionAdmin">
                            <v-list-item-action>
                                <v-icon>people</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">User</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/phases')" v-if="permissionAdmin">
                            <v-list-item-action>
                                <v-icon>link</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Phase</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item @click="clickMenu('/overviewmm')" v-if="permissionAdmin">
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview MM</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="clickMenu('/overviewAllocate')" v-if="permissionAdmin">
                            <v-list-item-action>
                                <v-icon>widgets</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title class="font-weight-bold">Overview Allcate</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="clickMenu('/activitylogs')" v-if="permissionAdminLeader">

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
                    <span class="hidden-sm-and-down white--text text--lighten-1">
                        {{ user.email }}
                    </span>
                </v-toolbar-title>
        </v-app-bar>
        <v-content>
            <v-container>
                <slot />
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    import { USER_ROLE_STRING } from '../../constants/common';
    export default {
        data: () => ({
            dialog: false,
            drawer: null,
            user : JSON.parse(localStorage.getItem('UserInfor')),
        }),
        computed: {
            permissionAdminLeader() {
                const currentUser = JSON.parse(localStorage.getItem('UserInfor'));
                return currentUser.role === USER_ROLE_STRING.Admin || currentUser.role === USER_ROLE_STRING.Leader;
            },
            permissionAdmin() {
                const currentUser = JSON.parse(localStorage.getItem('UserInfor'));
                return currentUser.role === USER_ROLE_STRING.Admin;
            },
            userInfo: function() {
                return this.$store.getters['hbhome/currentUser'];
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
                        window.localStorage.removeItem('UserInfor');
                        this.$router.push({ path: '/login' });
                    },
                    redirect: '/login',
                });
            },
            clickMenu(routeName) {
                if (this.$router.currentRoute.path !== routeName) {
                    this.$router.push({ path: routeName });
                }
            }
        },
    }
</script>
