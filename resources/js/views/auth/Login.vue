<template>
    <div class="login-box" style="margin: auto">
        <div class="login-logo mt-3">
            <a href="#"><b>ANNOTATE-TOOL</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card mt-3" >
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in</p>

                <form>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email" required v-model="email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"/>
                            </div>
                        </div>
                    </div>
                    <div style="color: red"> {{ error_email }}</div>
                    <br>
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="Password" required v-model="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div style="color: red"> {{ error_password }}</div>
                    <br>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-4">
                            <button @click.prevent="login()" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Login',
        data() {
            return {
                email: null,
                password: null,
                error_login: null,
                error_email: null,
                error_password: null,
            };
        },
        methods: {
            login() {
                this.$auth.login({
                    params : {
                        email: this.email,
                        password: this.password,
                    },
                    success(res) {
                        this.$router.push({ path: '/dashboard' });
                    },
                    error(res) {
                        this.error_login = res.response.data.error;
                        console.log(res.response.data.error);
                    },
                    rememberMe: true,
                });
            },
        },
    };
</script>
