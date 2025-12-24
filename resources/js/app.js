import './bootstrap';
import '../css/app.css';
import './Pages/Styles/Custom.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import {ZiggyVue} from '../../vendor/tightenco/ziggy'
import Layout from './Layout/Layout.vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { definePreset } from '@primeuix/themes';
import ToastService from 'primevue/toastservice';

import Toast from 'primevue/toast'
import ConfirmationService from 'primevue/confirmationservice';

import ConfirmPopup from 'primevue/confirmpopup';




const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{blue.50}',
            100: '{blue.100}',
            200: '{blue.200}',
            300: '{blue.300}',
            400: '{blue.400}',
            500: '{blue.500}',
            600: '{blue.600}',
            700: '{blue.700}',
            800: '{blue.800}',
            900: '{blue.900}',
            950: '{blue.950}'
        },
   
   
    }
});

createInertiaApp({
    title: (title) => `FOIX PCDP ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout === undefined ? Layout : page.default.layout 
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(ToastService)
      .use(ConfirmationService)
      .component("Toast", Toast)
      .component('ConfirmPopup', ConfirmPopup)
      .component("Head", Head)
      .component("Link", Link)
      .use(PrimeVue, {
        theme: {
          preset: MyPreset,
          options: {
            darkModeSelector: 'none',
            
            
          }
        }
      })
      
      .mount(el);
     import('aos').then(AOS => {
        AOS.init();
    });
  },
    progress:{
    // delay: 250,
    color: 'white',
    includeCSS: true,
    showSpinner: false,

  }
})