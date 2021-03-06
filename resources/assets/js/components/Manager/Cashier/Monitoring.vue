<template>
  <div>
    <div class="block full bordered">
      <div class="block-title">
        <h2>Opções de filtragem</h2>
      </div>
      <div class="btn-group btn-group-full p-lr p-tb">
        <button :class="{'btn-primary': status === 'all'}" @click.prevent="status = 'all'" class="btn btn-default">Todos</button>
        <button :class="{'btn-primary': status === 'pending'}" @click.prevent="status = 'pending'" class="btn btn-default">Andamento</button>
        <button :class="{'btn-primary': status === 'lose'}" @click.prevent="status = 'lose'" class="btn btn-default">Perdidas</button>
        <button :class="{'btn-primary': status === 'win'}" @click.prevent="status = 'win'" class="btn btn-default">Premiadas</button>
      </div>
      <div class="btn-group btn-group-full btn-group-space p-lr p-b">
        <div class="form-group">
          <input v-model="startDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <input v-model="endDate" type="date" class="form-control">
        </div>
        <div class="form-group">
          <v-select
            v-model="seller"
            placeholder="Cambistas"
            :options="sellers">
          </v-select>
        </div>
        <button @click="getBets" type="submit" class="btn btn-success">
          <i class="fa fa-filter"></i> Filtrar
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
              <td><b>Bilhete</b></td>
              <td><b>Código</b></td>
              <td><b>Vendedor</b></td>
              <td><b>Nome</b></td>
              <td><b>Jogos</b></td>
              <td><b>Valor</b></td>
              <td><b>Prêmio</b></td>
              <td><b>Comissão</b></td>
              <td><b>Data</b></td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="bet in bets" :key="bet.id">
              <td>
                <button
                  @click.prevent="openBet(bet)"
                  :class="{'wasOpened': bet.id === latestOpenedBet}"
                  class="btn btn-info">
                  <i class="fa fa-folder-open"></i>
                </button>
              </td>
              <td>
                <span class="label label-default">{{ bet.id }}</span>
                <br>
                <span class="label label-default" v-if="bet.status === 'pending'">Andamento</span>
                <span class="label label-warning" v-else-if="bet.status === 'canceled'">Cancelada</span>
                <span class="label label-success" v-else-if="bet.status === 'win'">Ganhou</span>
                <span class="label label-danger" v-else-if="bet.status === 'lose'">Perdeu</span>
                <br>
                <span class="label label-info" v-if="bet.origin === 'live'">Ao vivo</span>
              </td>
              <td>{{ bet.seller.name }}</td>
              <td><strong>{{ bet.client_name }}</strong></td>
              <td>{{ bet.tips_quantity }}</td>
              <td><b>{{ bet.bet_value | formatMoney }}</b></td>
              <td>
                <span class="label"
                  :class="{'label-success': bet.status === 'win', 'label-danger': bet.status === 'lose', 'label-warning': bet.status === 'canceled', 'label-default': bet.status === 'pending'}">
                  <b>{{ bet.prize | formatMoney }}</b>
                </span>
              </td>
              <td>{{ bet.commission | formatMoney }}</td>
              <td>{{ bet.human_date }}</td>
            </tr>
            <tr v-if="bets.length === 0">
              <td colspan="9" class="text-center">
                <strong>Nenhuma aposta encontrada</strong>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <modal ref="modal" title="Resumo da simulação" :bet.sync="bet"></modal>
  </div>
</template>

<script>
    import vSelect from 'vue-select'
    import modal from '../../shared/Modal/BetResume.vue'
    import {showToast} from '../../../functions/showToast'

    export default {
        components: {vSelect, modal},
        data () {
            return {
                loading: false,
                startDate: null,
                endDate: null,
                status: 'all',
                bets: [],
                bet: null,
                latestOpenedBet: null,
                sellers: [],
                seller: null
            }
        },
        methods: {
            getBets: function () {
                this.loading = true;
                this.$http.get('/manager/cashier/monitoring/report', {
                    params: {
                        start_date: this.startDate,
                        end_date: this.endDate,
                        status: this.status,
                        seller_id: this.seller ? this.seller.value : null
                    }
                }).then(response => {
                    response.json().then(res => {
                        this.bets = res.bets;
                        this.loading = false;
                    });
                }, error => {
                    this.loading = false;
                    showToast(error.data.message);
                });
            },
            getSellers: function () {
                this.$http.get('/manager/sellers/pluck')
                    .then(response => {
                        response.json().then(res => {
                            this.sellers = res.sellers;
                            this.sellers.unshift({label: 'Todos', value: 0});
                        })
                    }, err => showToast(err.data.message));
            },
            openBet: function (bet) {
                this.latestOpenedBet = bet.id;
                this.bet = null;
                this.$http.get('/api/bet/' + bet.id)
                    .then(response => {
                        response.json().then(res => {
                            this.bet = res.bet;
                            this.$refs.modal.showModal();
                        });
                    }, err => showToast(err.data.message));
            }
        },
        created() {
          this.startDate = new Date().toISOString().slice(0, 10);
          this.endDate = this.startDate;
          this.getSellers();
          this.getBets();
        },
    }
</script>

<style scoped>
    .wasOpened {
      background-color: #01567d;
      border-color: #0b4763;
      color: whitesmoke;
    }
</style>
