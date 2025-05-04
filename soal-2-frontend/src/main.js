import './assets/main.css'
import axios from 'axios'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
axios.defaults.baseURL = 'http://localhost:8000/api'
const app = createApp(App)
app.config.globalProperties.$axios = axios
app.use(createPinia())
app.use(router)

app.mount('#app')
