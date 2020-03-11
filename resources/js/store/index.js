import Vue from 'vue';
import Vuex from 'vuex';
import camelCase from 'camelcase';

Vue.use(Vuex);

const requireModule = require.context('./modules', false, /\.js$/);
const modules = {};
requireModule.keys().forEach((modulePath) => {
    const moduleName = camelCase(modulePath.replace(/^\.\/(.*)\.\w+$/, '$1'));
    modules[moduleName] = requireModule(modulePath).default || requireModule(modulePath);
});
const store = new Vuex.Store({
    modules,
});

export default store;
