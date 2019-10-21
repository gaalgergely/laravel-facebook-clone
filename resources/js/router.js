import Vue from 'vue';
import VueRouter from 'vue-router';
import Start from './views/Start';
import NewsFeed from "./views/NewsFeed";

Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',

    routes: [
        {
            path: '/', name: 'home', component: NewsFeed,
        }
    ]
});
