
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('navbar', require('./components/Navbar.vue'));
Vue.component('points', require('./components/Points.vue'));
Vue.component('campaigns', require('./components/Campaigns.vue'));
Vue.component('sidebar', require('./components/Sidebar.vue'));
Vue.component('end_users', require('./components/EndUsers.vue'));
Vue.component('autocomplete',require('./components/Autocomplete.vue'));
Vue.component('chatbot',require('./components/Chatbot.vue'));
Vue.component('add_point_panel',require('./components/AddPoint.vue'));
Vue.component('loader',require('./components/Loader.vue'));
Vue.component('drops',require('./components/Drops.vue'));
Vue.component('questions',require('./components/Questions.vue'));
Vue.component('answer',require('./components/RepeaterInput.vue'));

const app = new Vue({
    el: '#app'
});
