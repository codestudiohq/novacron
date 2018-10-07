Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'novacron',
            path: '/novacron',
            component: require('./components/Tool'),
        },
    ]);
    Vue.component('index-frequency', require('./components/frequency/IndexField'));
    Vue.component('detail-frequency', require('./components/frequency/DetailField'));
    Vue.component('form-frequency', require('./components/frequency/FormField'));

  Vue.component('index-hidden', require('./components/hidden/IndexField'));
  Vue.component('detail-hidden', require('./components/hidden/DetailField'));
  Vue.component('form-hidden', require('./components/hidden/FormField'));
});
