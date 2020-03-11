/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue';
import VueAxios from 'vue-axios';
import App from './App.vue';
// import VueAuth from '@websanova/vue-auth';
import router from './routes';
import store from './store';
// import auth from './auth';

require('./bootstrap');

window.Vue = require('vue');

//Set vue router
Vue.router = router;
Vue.use(VueAxios, axios);
// Vue.use(VueAuth, auth);


new Vue({
    el: '#app',
    router,
    store,
    components: {
        app: App,
    },
    render: h => h(App)
});
