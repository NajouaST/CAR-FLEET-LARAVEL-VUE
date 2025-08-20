import { createApp, markRaw } from "vue";
import App from './App.vue';
import router from './router';

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

import '@/assets/styles.scss';
import { createPinia } from "pinia";

const pinia = createPinia()
const app = createApp(App);

app.use(router);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.app-dark'
        }
    }
});
app.use(ToastService);
app.use(ConfirmationService);
pinia.use(({ store })=>{
    store.router = markRaw(router)
})
pinia.use(piniaPluginPersistedstate);

app.use(pinia)
app.mount('#app');
