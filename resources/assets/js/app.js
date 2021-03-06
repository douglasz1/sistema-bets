import { formatMoney } from './filters/formatMoney'
import { formatQuotation } from './filters/formatQuotation'
import { Date } from './functions/DateToISO'
import VueSweetAlert from 'vue-sweetalert'

/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */
Vue.use(VueSweetAlert)

Vue.filter('formatMoney', formatMoney)
Vue.filter('formatQuotation', formatQuotation)

Vue.component('load-quotations', require('./components/Simulator/LoadQuotations.vue'))
Vue.component('simulator', require('./components/Simulator/Index.vue'))
Vue.component('live-simulator', require('./components/Simulator/Live/Index'))
Vue.component('load-live-quotations', require('./components/Simulator/Live/LoadQuotations'))
Vue.component('bolao-segunda-via', require('./components/Bolao/SegundaVia'))
Vue.component('bet-reprints', require('./components/Bets/Reprint.vue'))
Vue.component('bet-validate', require('./components/Bets/Validade.vue'))
Vue.component('bet-search', require('./components/Bets/Search.vue'))
Vue.component('leagues-to-print', require('./components/leagues/LeaguesToPrint.vue'))

/**
 * Admin components
 */
Vue.component('admin-technical-list', require('./components/Admin/Technical/List.vue'))
Vue.component('admin-supervisors-list', require('./components/Admin/Supervisors/List.vue'))
Vue.component('admin-managers-list', require('./components/Admin/Managers/List.vue'))
Vue.component('admin-sellers-list', require('./components/Admin/Sellers/List.vue'))
Vue.component('admin-cashier-monitoring', require('./components/Admin/Cashier/Monitoring.vue'))
Vue.component('admin-acompanhamento-bolao', require('./components/Admin/Reports/AcompanhamentoBolao'))
Vue.component('admin-financial-general', require('./components/Admin/Financial/General.vue'))
Vue.component('admin-reports-analitico', require('./components/Admin/Reports/Analitico'))
Vue.component('admin-leagues-list', require('./components/Admin/Leagues/List.vue'))
Vue.component('admin-matches-list', require('./components/Admin/Matches/List.vue'))
Vue.component('admin-quotations-list', require('./components/Admin/Quotations/List.vue'))
Vue.component('admin-companies-supervisors', require('./components/Admin/Companies/Supervisors.vue'))
Vue.component('admin-send-balance', require('./components/Admin/Balance/ListSellers'))
Vue.component('admin-send-expenses', require('./components/Admin/Expenses/ListSellers'))
Vue.component('admin-goals-index', require('./components/Admin/Goals/Index'))
Vue.component('admin-send-payments', require('./components/Admin/Payments/Index'))
Vue.component('admin-cancelar-palpites', require('./components/Admin/Palpites/Cancelar'))
Vue.component('admin-auditoria', require('./components/Admin/Auditoria/Index'))
Vue.component('simulador-admin', require('./components/Admin/Simulator/Index'))

/**
 * Supervisor components
 */
Vue.component('supervisor-managers-list', require('./components/Supervisor/Managers/List.vue'))
Vue.component('supervisor-sellers-list', require('./components/Supervisor/Sellers/List.vue'))
Vue.component('supervisor-send-balance', require('./components/Supervisor/Balance/ListSellers'))
Vue.component('sp-reports-analitico', require('./components/Supervisor/Reports/Analitico'))
Vue.component('sp-reports-cashier', require('./components/Supervisor/Reports/Cashier.vue'))
Vue.component('sp-reports-financial', require('./components/Supervisor/Reports/Financial.vue'))
Vue.component('sp-acompanhamento-bolao', require('./components/Supervisor/Reports/AcompanhamentoBolao'))

/**
 * Manager components
 */
Vue.component('manager-cashier-summary', require('./components/Manager/Cashier/Summary.vue'))
Vue.component('manager-cashier-monitoring', require('./components/Manager/Cashier/Monitoring.vue'))
Vue.component('manager-acompanhamento-geral', require('./components/Manager/Bolao/AcompanhamentoGeral'))
Vue.component('manager-acompanhamento-pessoal', require('./components/Manager/Bolao/AcompanhamentoPessoal'))
Vue.component('manager-financial-summary', require('./components/Manager/Financial/Summary.vue'))
Vue.component('manager-financial-general', require('./components/Manager/Financial/General.vue'))
Vue.component('manager-sellers-list', require('./components/Manager/Sellers/List.vue'))
Vue.component('manager-send-balance', require('./components/Manager/Balance/ListSellers'))
Vue.component('manager-send-payments', require('./components/Manager/Payments/Index'))

/**
 * Seller components
 */
Vue.component('seller-cashier', require('./components/Seller/Cashier.vue'))
Vue.component('seller-financial', require('./components/Seller/Financial.vue'))
Vue.component('seller-clients', require('./components/Seller/Clients.vue'))
Vue.component('seller-acompanhamento', require('./components/Seller/AcompanhamentoBolao'))

/**
 * Technical components
 */
Vue.component('technical-reports-cashier', require('./components/Technical/Reports/Cashier.vue'))
Vue.component('technical-acompanhamento-bolao', require('./components/Technical/Reports/AcompanhamentoBolao'))
Vue.component('technical-sellers-list', require('./components/Technical/Sellers/List.vue'))
Vue.component('technical-managers-list', require('./components/Technical/Managers/List.vue'))
Vue.component('technical-leagues-list', require('./components/Technical/Leagues/List.vue'))
Vue.component('technical-matches-list', require('./components/Technical/Matches/List.vue'))
Vue.component('technical-quotations-list', require('./components/Technical/Quotations/List.vue'))
Vue.component('technical-send-balance', require('./components/Technical/Balance/ListSellers.vue'))
Vue.component('technical-send-expenses', require('./components/Technical/Expenses/ListSellers.vue'))

/**
 * Web (public site) components
 */
Vue.component('web-simulator', require('./components/Simulator/Web/Index.vue'))
Vue.component('web-simulator-leagues-menu', require('./components/Simulator/Web/LeaguesMenu.vue'))

const app = new Vue({
  el: '#app'
})
