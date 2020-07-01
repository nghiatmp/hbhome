<template>
    <v-app>
        <v-content>
            <v-container
                fluid
                fill-height
            >
                <v-layout
                    align-center
                    justify-center
                >
                    <v-flex
                        xs12
                        sm8
                        md4
                    >
                        <v-card class="elevation-12">
                            <v-toolbar
                                color="primary"
                                dark
                                flat
                            >
                                <v-toolbar-title>Login</v-toolbar-title>
                                <v-spacer />
                            </v-toolbar>
                            <v-card-text>
                                <v-form>
                                    <v-text-field
                                        v-model="email"
                                        label="Email"
                                        name="email"
                                        prepend-icon="person"
                                        type="text"
                                        @keyup.enter="login"
                                    />

                                    <v-text-field
                                        v-model="password"
                                        label="Password"
                                        name="password"
                                        prepend-icon="lock"
                                        type="password"
                                        @keyup.enter="login"
                                    />
                                </v-form>
                                <v-alert
                                    v-show="hasError"
                                    dense
                                    outlined
                                    type="error"
                                >Incorrect information</v-alert>
                            </v-card-text>
                            <v-card-actions class="justify-center">
                                <v-btn :loading="loading" color="primary" @click="login">Login</v-btn>

                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>
<script>
    import { call } from 'vuex-pathify';
    export default {
        data() {
            return {
                email: '',
                password: '',
                hasError: false,
                loading: false,

            };
        },
        methods: {
            setUserInfo: call('user/setUserInfo'),
            login() {
                this.loading = true;
                this.$auth.login({
                    data: {
                        email: this.email,
                        password: this.password,
                    },
                    success: function(res) {
                        this.hasError = false;
                        this.loading = false;
                        localStorage.setItem('UserInfor',res.data.user);
                        this.$store.dispatch('hbhome/setCurrentUser', res.data.user);
                        this.$router.push({ path: '/admin/dashboard' });
                    },
                    error: function() {
                        this.loading = false;
                        this.hasError = true;
                    },
                    rememberMe: true,
                    fetchUser: true,
                });
            },
        },
    };
</script>
