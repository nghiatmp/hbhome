import Vue from 'vue';
import VueRouter from 'vue-router';
import Login from './views/auth/login';
import MyProjects from './views/myproject/index';
import Teams from './views/team/index';
import Projects from './views/project/index';
import Users from './views/user/index';
import Phases from './views/phase/index';
import OverviewMM from './views/overviewmm/index';
import OverviewAllocate from './views/overviewallocate/index';
import ActivityLogs from './views/activityLog/index';
import Holiday from './views/holiday/index';


const routes = [
    {
        path: '/login',
        component: Login,
    },
    {
        path: '/myprojects',
        component: MyProjects,
    },
    {
        path: '/teams',
        component: Teams,
    },
    {
        path: '/projects',
        component: Projects,
    },
    {
        path: '/users',
        component: Users,
    },
    {
        path: '/phases',
        component: Phases,
    },
    {
        path: '/overviewmm',
        component: OverviewMM,
    },
    {
        path: '/overviewAllocate',
        component: OverviewAllocate,
    },
    {
        path: '/activitylogs',
        component: ActivityLogs,
    },
    {
        path: '/holiday',
        component: Holiday,
    },

];
Vue.use(VueRouter);
export default new VueRouter({
    base: '/',
    mode: 'history',
    routes,
    linkActiveClass: 'active',
});

