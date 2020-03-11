import Vue from 'vue';
import VueRouter from 'vue-router';

const routes = [

];
Vue.use(VueRouter);
export default new VueRouter({
    base: '/',
    mode: 'history',
    routes,
    linkActiveClass: 'active',
});

