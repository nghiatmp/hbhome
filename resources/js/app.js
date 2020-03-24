import Vue from 'vue';
import VueAxios from 'vue-axios';
import App from './App.vue';
import VueAuth from '@websanova/vue-auth';
import Vuelidate from 'vuelidate';
import router from './routes';
import store from './store';
import axios from 'axios';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify';
import vuetify from './plugins/vuetify';
import auth from './auth';
import HighchartsVue from 'highcharts-vue'

require('./bootstrap');

window.Vue = require('vue');

//Set vue router
Vue.router = router;
Vue.use(VueAxios, axios);

Vue.use(Vuetify);
Vue.use(VueRouter);
Vue.use(VueAuth, auth);
Vue.use(Vuelidate);
Vue.use(HighchartsVue);
Vue.use(require('vue-moment'));


new Vue({
    el: '#app',
    router,
    store,
    vuetify,
    components: {
        app: App,
    },
    render: h => h(App)
});
