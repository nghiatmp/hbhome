import bearer from '@websanova/vue-auth/drivers/auth/bearer';
import axios from '@websanova/vue-auth/drivers/http/axios.1.x';
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x';

const config = {
  auth: bearer,
  http: axios,
  router: router,
  tokenDefaultName: 'User_token',
  tokenStore: ['localStorage'],
  rolesVar: 'role',
  registerData: { url: 'auth/register', method: 'POST', redirect: '/login' },
  loginData: { url: 'api/auth/login', method: 'POST', redirect: '/myprojects', fetchUser: true },
  logoutData: { url: 'api/auth/logout', method: 'POST', redirect: '/login', makeRequest: true },
  fetchData: { url: 'api/auth/user', method: 'GET', enabled: true },
  parseUserData: function(response) {
    return response.data;
  },
  refreshData: { url: 'auth/refresh', method: 'GET', enabled: true, interval: 45 },
};
export default config;
