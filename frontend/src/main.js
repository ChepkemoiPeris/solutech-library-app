import './assets/main.css'
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/dist/css/bootstrap.css'
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

import { createApp } from 'vue'
import App from './App.vue'
import router from './router' 
const app = createApp(App)
app.component('v-select', vSelect);
app.use(router)

app.mount('#app')
