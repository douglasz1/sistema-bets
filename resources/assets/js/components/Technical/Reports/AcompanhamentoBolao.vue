<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full btn-group-space tres-botoes p-lr p-tb">
        <div class="form-group">
          <v-select
            placeholder="Empresa"
            v-model="company"
            :on-change="onChangeCompany"
            :options="companies">
          </v-select>
        </div>
        <div class="form-group">
          <v-select
            placeholder="Gerente"
            v-model="manager"
            :on-change="onChangeManager"
            :options="managers">
          </v-select>
        </div>
        <div class="form-group">
          <v-select
            v-model="seller"
            placeholder="Operador"
            :options="sellers">
          </v-select>
        </div>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-b">
        <div class="form-group">
          <input v-model="dataInicial" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="dataFinal" type="date" class="form-control">
        </div>
        <div class="form-group m-t-sm">
          <input v-model="apostaId" @keyup="debounceSearch" pattern="[0-9]" type="number"
                 class="form-control" placeholder="N. Bilhete">
        </div>
        <button @click.prevent="pegarApostas" class="btn m-t-sm"
                :class="{'btn-success': !carregando, 'btn-primary': carregando}">
          <i v-if="carregando" class="fa fa-spinner fa-spin"></i>
          <i class="fa fa-filter" v-else></i> Filtrar
        </button>
      </div>
    </div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Últimas apostas</h2>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td class="link" @click.prevent="alterarOrdenacao('id')">
              <div>
                <b>Código</b>
                <i
                  v-if="ordernarPor === 'id'"
                  :class="{'fa-chevron-down': ordemClassificacao === 'desc', 'fa-chevron-up': ordemClassificacao === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Data</b></td>
            <td><b>Operador</b></td>
            <td class="link" @click.prevent="alterarOrdenacao('valor')">
              <div>
                <b>Valor</b>
                <i
                  v-if="ordernarPor === 'valor'"
                  :class="{'fa-chevron-down': ordemClassificacao === 'desc', 'fa-chevron-up': ordemClassificacao === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Nome</b></td>
            <td class="link" @click.prevent="alterarOrdenacao('total_pontos')">
              <div>
                <b>Pontos</b>
                <i
                  v-if="ordernarPor === 'total_pontos'"
                  :class="{'fa-chevron-down': ordemClassificacao === 'desc', 'fa-chevron-up': ordemClassificacao === 'asc'}"
                  class="fa"></i>
              </div>
            </td>
            <td><b>Comissão</b></td>
            <td><b>Opções</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(aposta, index) in apostasOrdenadas" :key="aposta.id">
            <td>
              <span class="label label-default">{{ aposta.id }}</span>
              <br>
              <span class="label label-default" v-if="aposta.situacao === 'pendente'">Andamento</span>
              <span class="label label-warning" v-else-if="aposta.situacao === 'cancelado'">Cancelada</span>
            </td>
            <td>
              <span>{{ formatDate(aposta.created_at) }}</span>
            </td>
            <td>
              {{ aposta.vendedor.company.name }}
              <br>
              {{ aposta.vendedor.name }}
            </td>
            <td><b>{{ aposta.valor | formatMoney }}</b></td>
            <td><b>{{ aposta.cliente }}</b></td>
            <td>{{ aposta.total_pontos }}</td>
            <td>{{ aposta.comissao | formatMoney }}</td>
            <td>
              <button
                @click.prevent="abrirAposta(aposta)"
                :class="{'wasOpened': aposta.id === ultimaApostaAberta}"
                class="btn btn-info">
                Bilhete
              </button>
              <button
                v-if="aposta.situacao !== 'cancelado'"
                @click.prevent="perguntarParaCancelar(aposta)"
                class="btn btn-danger">
                <i class="fa fa-ban"></i>
              </button>
            </td>
          </tr>
          <tr v-if="apostas.length === 0">
            <td colspan="8" class="text-center">
              <strong>Nenhuma aposta encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <ResumoBilheteBolao ref="resumoBilheteBolao" />
  </div>
</template>

<script>
  import moment from 'moment'
  import vSelect from 'vue-select'
  import ResumoBilheteBolao from './ResumoBilheteBolao'

  export default {
    components: {vSelect, ResumoBilheteBolao},
    data() {
      return {
        carregando: false,
        dataInicial: null,
        dataFinal: null,
        ultimaApostaAberta: null,
        apostaId: null,
        apostas: [],
        ordernarPor: 'created_at',
        ordemClassificacao: 'desc',
        managers: [],
        companies: [],
        sellers: [],
        manager: null,
        company: null,
        seller: null
      }
    },
    methods: {
      alterarOrdenacao(ordernarPor) {
        if (this.ordernarPor === ordernarPor) {
          this.ordemClassificacao = this.ordemClassificacao === 'asc' ? 'desc' : 'asc';
          return
        }
        this.ordernarPor = ordernarPor;
        this.ordemClassificacao = 'desc'
      },
      formatDate(date) {
        return moment(date).format('DD/MM HH:mm:ss')
      },
      debounceSearch() {
      },
      buscarApostas() {
        this.$http
          .post('/technical/bolao/acompanhamento/busca', {apostaId: this.apostaId})
          .then(response => {
            this.apostas = response.body.apostas;
          })
          .catch(() => this.$swal('Ops!', 'Erro ao listar apostas', 'error'))
          .finally(() => this.carregando = false)
      },
      pegarApostas() {
        this.carregando = true;

        this.$http
          .get('/technical/bolao/acompanhamento/relatorio', {
            params: {
              dataInicial: this.dataInicial,
              dataFinal: this.dataFinal,
              empresaId: this.company ? this.company.value : null,
              gerenteId: this.manager ? this.manager.value : null,
              vendedorId: this.seller ? this.seller.value : null
            }
          })
          .then(response => {
            this.apostas = response.body.apostas
          })
          .catch(() => this.$swal('Ops!', 'Erro ao listar apostas', 'error'))
          .finally(() => this.carregando = false)
      },
      getCompanies() {
        this.$http.get('/technical/companies/pluck')
          .then(response => {
            this.companies = response.body.companies;
            this.companies.unshift({label: 'Todas', value: 0})
          }, () => this.$swal('Ops!', 'Erro ao listar empresas', 'error'))
      },
      getManagers(companyId) {
        this.$http.get('/technical/managers/pluck', {params: {companyId: companyId}})
          .then(response => {
            this.managers = response.body.managers;
            this.managers.unshift({label: 'Todos', value: 0})
          }, err => this.$swal('Ops!', 'Erro ao listar gerentes', 'error'))
      },
      getSellers(companyId, managerId) {
        this.$http.get('/technical/sellers/pluck', {params: {companyId, managerId}})
          .then(response => {
            this.sellers = response.body.sellers;
            this.sellers.unshift({label: 'Todos', value: 0})
          }, err => this.$swal('Ops!', 'Erro ao listar operadores', 'error'))
      },
      onChangeCompany(company) {
        this.company = company;
        this.manager = null;
        this.managers = [];
        this.seller = null;
        this.sellers = [];

        if (company !== null) {
          let companyId = company.value !== 0 ? company.value : null;
          this.getManagers(companyId);
          this.getSellers(companyId, null)
        }
      },
      onChangeManager(manager) {
        this.manager = manager;
        this.seller = null;
        this.sellers = [];
        if (manager !== null) {
          let managerId = manager.value !== 0 ? manager.value : null;
          this.getSellers(null, managerId)
        }
      },
      abrirAposta(aposta) {
        this.ultimaApostaAberta = aposta.id;
        this.$refs.resumoBilheteBolao.getBet(aposta.id)
      },
      perguntarParaCancelar(aposta) {
        this.$swal({
          title: 'Cancelar simulação',
          html: `Deseja cancelar a simulação <b>${aposta.id}</b>?`,
          type: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: '<i class="glyphicon glyphicon-chevron-left"></i> Voltar',
          confirmButtonText: '<i class="glyphicon glyphicon-ok"></i> Sim, cancelar'
        }).then(result => {
          if (result) this.cancelarAposta(aposta)
        }).catch(() => false)
      },
      cancelarAposta(aposta) {
        this.$http.put(`/technical/bolao/acompanhamento/cancelar/${aposta.id}`)
          .then(() => {
            aposta.situacao = 'cancelado';
            this.$swal(
              'Cancelado!',
              'A aposta foi cancelada com sucesso.',
              'success'
            )
          })
          .catch(() => this.$swal(
            'Erro ao cancelar!',
            'Ocorreu algum erro ao cancelar, por favor, tente novamente mais tarde',
            'error'
          ))
      },
    },
    computed: {
      apostasOrdenadas() {
        const ordernarPor = this.ordernarPor;
        const ordemClassificacao = this.ordemClassificacao;
        const numericValues = ['id', 'valor', 'total_pontos'];

        if (numericValues.indexOf(ordernarPor) < 0) {
          return _.orderBy(this.apostas, ordernarPor, ordemClassificacao);
        }

        return _.orderBy(this.apostas, bet => parseFloat(bet[`${ordernarPor}`]), ordemClassificacao)
      }
    },
    created() {
      this.debounceSearch = _.debounce(() => this.buscarApostas(), 500)
    },
    mounted() {
      this.getCompanies();
      this.getManagers();
      this.getSellers();
      this.dataInicial = moment().format('YYYY-MM-DD');
      this.dataFinal = this.dataInicial;
      this.pegarApostas()
    }
  }
</script>

<style scoped>
  .link {
    cursor: pointer;
  }
  thead > tr div {
    display: flex;
    align-items: center;
  }
  td .fa-chevron-down,
  td .fa-chevron-up {
    font-size: .5em;
    margin-left: 5px;
  }
  .wasOpened {
    background-color: #01567d;
    border-color: #0b4763;
    color: whitesmoke;
  }

  tbody tr td:nth-child(2) {
    width: 65px;
    max-width: 65px;
    white-space: normal;
  }
</style>
