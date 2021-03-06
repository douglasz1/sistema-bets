<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full p-lr p-tb">
        <div class="form-group">
          <input v-model="dataInicial" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="dataFinal" type="date" class="form-control">
        </div>
        <button @click="buscarApostas" type="submit" class="btn btn-success">
          <i class="fa fa-filter"></i> Filtrar
        </button>
      </div>
    </div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Resumo de apostas</h2>
      </div>
      <div class="table-responsive">
        <table class="table table-vcenter table-borderless table-condensed table-striped table-hover">
          <thead>
          <tr>
            <td><b>Jogos</b></td>
            <td><b>Aberto</b></td>
            <td><b>Prêmio</b></td>
            <td><b>Entrada</b></td>
            <td><b>Comissão</b></td>
            <td><b>Saída</b></td>
            <td><b>Saldo</b></td>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ apostas.length }}</td>
            <td>{{ bilhetesAbertos }}</td>
            <td>{{ bilhetesPremiados }}</td>
            <td class="text-success">{{ valorEntrada | formatMoney }}</td>
            <td class="text-warning">{{ comissoesVendedor | formatMoney }}</td>
            <td class="text-danger">{{ valorPremios | formatMoney }}</td>
            <td
              :class="{'text-success': saldo > 0, 'text-danger': saldo < 0}">
              <b>{{ saldo | formatMoney }}</b>
            </td>
          </tr>
          </tbody>
        </table>
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
            <td><b>Código</b></td>
            <td><b>operador</b></td>
            <td><b>Nome</b></td>
            <td><b>Valor</b></td>
            <td><b>Total pontos</b></td>
            <td><b>Comissão</b></td>
            <td><b>Data</b></td>
            <td><b>Opções</b></td>
          </tr>
          </thead>
          <tbody>
          <tr v-for="aposta in apostas" :key="aposta.id">
            <td>
              <span class="label label-default">{{ aposta.id }}</span>
              <br>
              <span class="label label-default" v-if="aposta.situacao === 'pendente'">Andamento</span>
              <span class="label label-warning" v-else-if="aposta.situacao === 'cancelado'">Cancelada</span>
            </td>
            <td>{{ aposta.vendedor.name }}</td>
            <td><strong>{{ aposta.cliente }}</strong></td>
            <td>{{ aposta.valor | formatMoney }}</td>
            <td>{{ aposta.total_pontos }}</td>
            <td>{{ aposta.comissao | formatMoney }}</td>
            <td>{{ formatarData(aposta.created_at) }}</td>
            <td>
              <button
                @click.prevent="abrirBilhete(aposta)"
                :class="{'wasOpened': aposta.id === latestOpenedBet}"
                class="btn btn-info">
                Bilhete
              </button>
            </td>
          </tr>
          <tr v-if="apostas.length === 0">
            <td colspan="7" class="text-center">
              <strong>Nenhuma aposta encontrada</strong>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <modal ref="modalResumo" title="Resumo da simulação" :aposta.sync="aposta"></modal>
  </div>
</template>

<script>
  import moment from 'moment'
  import modal from '../../shared/Modal/ResumoApostaBolao'

  export default {
    components: {modal},
    data() {
      return {
        aposta: null,
        latestOpenedBet: null,
        dataInicial: null,
        dataFinal: null,
        apostas: []
      }
    },
    methods: {
      formatarData(data) {
        return moment(data).format('DD/MM HH:mm:ss')
      },
      buscarApostas() {
        this.$http.get('/manager/bolao/acompanhamento/geral', {
          params: {
            dataInicial: this.dataInicial,
            dataFinal: this.dataFinal
          }
        }).then(response => {
          response.json().then(res => this.apostas = res.apostas);
        }, error => console.log(error));
      },
      abrirBilhete(aposta) {
        this.latestOpenedBet = aposta.id;
        this.aposta = null;
        this.$http.get(`/api/bolao/bilhete/${aposta.id}`)
          .then(response => {
            response.json().then(res => {
              this.aposta = res.aposta;
              this.$refs.modalResumo.showModal();
            });
          }, err => console.error(err));
      },
    },
    computed: {
      valorEntrada() {
        const list = this.apostas;
        let value = _.sumBy(list, aposta => aposta.situacao !== 'cancelado' ? parseFloat(aposta.valor) : 0);
        return parseFloat(value).toFixed(2);
      },
      valorPremios() {
        const list = this.apostas;
        let value = _.sumBy(list, aposta => aposta.situacao === 'win' ? parseFloat(aposta.prize) : 0);
        return parseFloat(value).toFixed(2);
      },
      comissoesVendedor() {
        const list = this.apostas;
        let value = _.sumBy(list, aposta => aposta.situacao !== 'cancelado' ? parseFloat(aposta.commission) : 0);
        return parseFloat(value).toFixed(2);
      },
      bilhetesPremiados() {
        const list = this.apostas;
        let winners = _.filter(list, aposta => aposta.situacao === 'win');
        return winners.length
      },
      bilhetesAbertos() {
        const list = this.apostas;
        let pending = _.filter(list, aposta => aposta.situacao === 'pendente');
        return pending.length
      },
      saldo() {
        let value = parseFloat(this.valorEntrada) - parseFloat(this.valorPremios) - parseFloat(this.comissoesVendedor);
        return value.toFixed(2);
      },
    },
    created() {
      this.dataInicial = moment().format('YYYY-MM-DD');
      this.dataFinal = this.dataInicial;
      this.buscarApostas();
    }
  }
</script>

<style scoped>
  .wasOpened {
    background-color: #01567d;
    border-color: #0b4763;
    color: whitesmoke;
  }
</style>
