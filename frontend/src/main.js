import { createApp } from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import axios from 'axios'
import { Quasar } from 'quasar'
import Toaster from "@meforma/vue-toaster";
import quasarUserOptions from './quasar-user-options'
import 'viewerjs/dist/viewer.css'
import VueViewer from 'v-viewer'
axios.defaults.baseURL = 'http://localhost:8000/api/'
require('@/store/subcriber')

store.dispatch('auth/attempt', localStorage.getItem('token')).then(() => {
    createApp(App).use(Quasar, quasarUserOptions).use(store).use(Toaster).use(VueViewer).use(router).mount('#app')
})

