import Vue from 'vue'
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import i18n from '@/libs/i18n'
import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'

// 3rd party plugins
import '@axios'
import '@/libs/acl'
import '@/libs/portal-vue'
import '@/libs/clipboard'
import '@/libs/toastification'
import '@/libs/sweet-alerts'
import '@/libs/vue-select'
import '@/libs/tour'

// Axios Mock Adapter
import '@/@fake-db/db'
import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css'
Vue.use(Vuesax, {
    rtl: true
})

// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)
// Mixin
import mixin from './mixin'
Vue.mixin({
    mixins: [mixin],
})
// Feather font icon - For form-wizard
// * Shall remove it if not using font-icons of feather-icons - For form-wizard
require('@core/assets/fonts/feather/iconfont.css') // For form-wizard

// import core styles
require('@core/scss/core.scss')

// import assets styles
require('@/assets/scss/style.scss')
Vue.config.productionTip = false
import callApi, { setupInterceptors } from './axios'
// import GAuth from 'vue-google-oauth2'
// const gauthOption = {
//   clientId: '1024188057686-vrjca8a5g6u626ppi2smc0neee5k7963.apps.googleusercontent.com',
//   scope: 'profile email',
//   prompt: 'select_account'
// }
// Vue.use(GAuth, gauthOption)
// Vue.prototype.$http = callApi
Vue.config.disableNoTranslationWarning = true;
let app = new Vue({
  router,
  store,
  created() {
    setupInterceptors(store, router)
    const accessToken = this.$store.state.user.accessToken || localStorage.getItem('_ac')
    if (accessToken) {
      callApi.get('manage/users/authenticate').then((response) => {
        const user = response.user
        const tools = response.tools
        store.dispatch('user/setUser', user).then(r => {})
        store.dispatch('user/setTool', tools).then(r => {})
      }).catch((error) => {
          // localStorage.clear()
      })
    }
  },
  i18n,
  render: h => h(App),
}).$mount('#app')

export default app
