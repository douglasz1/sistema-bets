<template>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <div class="block full simulador" :class="{'aberto': showSimulator}">
            <div class="block-title">
                <div class="hidden-sm hidden-xs">
                  <i class="fa fa-money"></i>
                  <span>Simular Aposta</span>
                </div>
                <div class="hidden-md hidden-lg" @click="showSimulator = !showSimulator">
                  <div v-if="showSimulator">
                    <strong>Cupom</strong>
                  </div>
                  <div v-else>
                    <span>{{ choices.length }}</span>
                    <span>|</span>
                    <span>{{ prizeTotal | formatMoney }}</span>
                  </div>
                </div>
            </div>
            <div class="simular-apostas">
                <div class="lista-apostas">
                    <div class="aposta" v-for="(choice, index) in choices" :key="choice.id">
                        <a class="pull-right text-danger" href="javascript:" @click="choices.splice(index, 1)">
                            <i class="fa fa-times-rectangle fa-2x"></i>
                        </a>
                        <b>{{ choice.sport_name }}</b>
                        <br>
                        <b>{{ choice.league_name }}</b>
                        <br>
                        <b>{{ choice.match_name }}</b>
                        <br>
                          {{ choice.bet_name }}
                        <br>
                        <b>{{ choice.choice_name }}:</b> {{ choice.value | formatQuotation }}
                        <br>
                        {{ choice.match_date }}
                    </div>
                </div>
                <form action="javascript:" @submit.prevent="storeBet">
                    <div :class="{'has-error': errors.choices }">
                        <form-error v-if="errors.choices" :errors="errors.choices"></form-error>
                    </div>

                    <hr>

                    <div class="btn-group btn-group-full p-tb">
                      <button @click.prevent="bet_value = 2" class="btn btn-default">2</button>
                      <button @click.prevent="bet_value = 5" class="btn btn-default">5</button>
                      <button @click.prevent="bet_value = 10" class="btn btn-default">10</button>
                      <button @click.prevent="bet_value = 20" class="btn btn-default">20</button>
                      <button @click.prevent="bet_value = 50" class="btn btn-default">50</button>
                      <button @click.prevent="bet_value = 100" class="btn btn-default">100</button>
                    </div>

                    <div class="btn-group btn-group-full btn-group-space p-b">
                      <div class="form-group" :class="{'has-error': errors.bet_value }">
                        <div class="input-group">
                          <span class="input-group-addon">R$</span>
                          <input v-model="bet_value" type="number" class="form-control" min="1" required placeholder="Valor">
                        </div>
                        <form-error v-if="errors.bet_value" :errors="errors.bet_value"></form-error>
                      </div>
                      <div v-if="isSeller" class="form-group" :class="{'has-error': errors.name }">
                        <select v-model="name" class="form-control" required>
                          <option value="0" selected>Escolha um cliente</option>
                          <option :value="client.id" v-for="client in clients" :key="client.id">
                            {{ client.name }}
                          </option>
                        </select>
                        <form-error v-if="errors.name" :errors="errors.name"></form-error>
                      </div>
                    </div>

                    <div class="form-group detalhes-apostas">
                      Jogos: <b>{{ choices.length }}</b> |
                      Cotações: <b>{{ quotationTotal | formatQuotation }}</b> |
                      Prêmio: <b>{{ prizeTotal | formatMoney }}</b>
                    </div>

                    <div class="text-center loading" v-if="loading">
                        <i class="fa fa-spinner fa-2x fa-spin text-info"></i>
                    </div>
                    <div class="form-group buttons" v-else>
                        <button v-if="isSeller" type="reset" class="btn btn-lg btn-danger" @click="cancelBet">
                            <i class="fa fa-remove"></i> Cancelar
                        </button>
                        <button v-if="isSeller" type="submit" class="btn btn-lg btn-success">
                            <i class="fa fa-check"></i> Salvar
                        </button>
                    </div>
                </form>

                <h4 v-if="isSeller">Eu li e concordo com as regras da {{ banca }}</h4>
                <br>
                <h4 v-if="isSeller">Cadastrar novo cliente</h4>

                <form v-if="isSeller" action="javascript:" @submit.prevent="storeClient">
                  <div class="form-group" :class="{'has-error': errors.new_client }">
                    <input type="text" v-model="newClient" class="form-control" required placeholder="Nome do novo cliente">
                    <button type="submit" class="btn btn-primary">
                        Criar
                    </button>
                    <form-error v-if="errors.new_client" :errors="errors.new_client"></form-error>
                  </div>
                </form>

            </div>
        </div>
        <bet-resume ref="betResume" title="Resumo da simulação" :bet.sync="betResult" :canPrint="true"></bet-resume>
    </div>
</template>

<script>
  import betResume from '../shared/Modal/BetResume'
  import formError from '../shared/FormError'
  import { showToast } from '../../functions/showToast'

  export default {
    components: {betResume, formError},
    props: ['maxPrize', 'maxPrizeMultiplier'],
    data () {
      return {
        banca: process.env.APP_NAME || 'Banca',
        loading: false,
        newClient: '',
        choices: [],
        clients: [],
        showSimulator: false,
        name: '0',
        bet_value: 5,
        errors: [],
        betResult: null,
        betId: null,
        isSeller: false,
      }
    },
    methods: {
      addQuotations (choiceData) {
        const list = this.choices
        let result = _.findIndex(list, choice => choice.match_id === choiceData.match_id)

        if (result >= 0) {
          let result2 = _.findIndex(list, choice => choice.id === choiceData.id)

          if (result2 < 0) {
            this.choices.splice(result, 1)
            this.choices.push(choiceData)
          } else {
            this.choices.splice(result2, 1)
          }
        } else {
          this.choices.push(choiceData)
        }
      },
      verifyChoice (choice_id) {
        const list = this.choices
        let result = _.findIndex(list, choice => choice.id === choice_id)

        return (result >= 0)
      },
      verifyMatchSelected (matchId) {
        const list = this.choices;
        let result = _.findIndex(list, choice => {
          return choice.match_id === matchId && (choice.bet_slug !== 'full_time_result' && choice.bet_slug !== 'to_win_match')
        });

        return (result >= 0)
      },
      cancelBet () {
        this.betResult = []
        this.choices = []
        this.name = '0'
        this.bet_value = 5
        this.showSimulator = false
        this.betId = null
      },

      storeClient () {
        if(this.newClient.length <= 0){
          return showToast('Você precisa adicionar um nome ao seu cliente!');
        }
        this.loading = true;
        this.$http.post('/seller/clients/store', {
        name: this.newClient
        }).then(response => {
          response.json().then(res => {
            this.clients.push(res.client);
            this.loading = false;
          });
          this.$swal(
            '',
            'Cliente cadastrado com sucesso.',
            'success'
          )
        }, err => {
          this.$swal(
            '',
            'Você já cadastrou um usuário com esse nome.',
            'error'
          )
          this.loading = false;
        });
      },

      storeBet () {
        if (this.choices.length < 1) {
          return showToast('Você precisa adicionar ao menos um palpite!');
        }
        if (this.name == '0') {
          return showToast('Você precisa escolher um cliente para a aposta!');
        }

        this.errors = []
        this.loading = true
        this.$http.post('/bets/store', {
          name:      this.name,
          bet_value: this.bet_value,
          choices:   this.choices,
          betId:     this.betId
        }).then(response => {
          response.json()
            .then(res => {
              this.cancelBet()
              this.betResult = res.bet
              this.$refs.betResume.showModal()
              this.loading = false
            })
        }, err => {
          err.json().then(err => {
            this.errors = err
            this.loading = false
          })
        })
      },

      printBet (id) {
        try {
          return android.getTicket(id + "")
        } catch (err) {
          return showToast(err.message)
        }
      },
      getClients() {
        try {
          this.loading = true;

          this.$http.get("/seller/clients/list").then((response) => {
            response.json().then((res) => {
              this.clients = res.clients;
              this.loading = false;
              this.isSeller = true;
            });
          });
        } catch (err) {
        } finally {
          this.loading = false;
        }
      },

      betConstruct(){
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        if(urlParams.has('pin')){
          this.showSimulator = true;
          this.loading = true;
          this.$http.post(`bets/search`,{
            code: urlParams.get('pin')
            }).then(response => {
              response.json().then(res => {
                const { bet } = res;
                const { tips } = bet;

                this.betId = bet.id;
                this.bet_value = parseInt(bet.bet_value);

                tips.forEach(tip => {
                  this.addQuotations({
                    id:          tip.odd_id,
                    match_id:    tip.match.id,
                    sport_name:  tip.match.sport.name,
                    league_name: `${tip.match.league.country.name}: ${tip.match.league.name}`,
                    bet_slug:    tip.bet_slug,
                    bet_name:    tip.bet_name,
                    choice_name: tip.choice_name,
                    choice_slug: tip.choice_slug,
                    value:       tip.value,
                    match_name:  tip.match.match_name,
                    match_date:  tip.match.human_date
                  })
                });
              this.loading = false;
            });
          }, err => {
            this.$swal(
              '',
              'A simulação já foi validada.',
              'error'
            )
            this.loading = false;
          });
        }
      },
    },
    computed: {
      quotationTotal () {
        const list = this.choices
        return _.reduce(list, (acc, choice) => acc * parseFloat(choice.value), 1)
      },
      prizeTotal () {
        let maxPrizeMultiplier = parseFloat(this.maxPrizeMultiplier),
          maxPrize = parseFloat(this.maxPrize),
          betValue = parseFloat(this.bet_value),
          prize = betValue * parseFloat(this.quotationTotal).toFixed(2)

        if (!isNaN(prize) && this.choices.length > 0) {
          let betMaxValue = betValue * maxPrizeMultiplier

          if (prize > betMaxValue && betMaxValue < maxPrize) {
            return betMaxValue
          }

          return prize > maxPrize ? maxPrize : prize
        }
        return 0
      }
    },
    created() {
      this.getClients();
      this.betConstruct();
    }

  }
</script>

<style scoped>
    .form-group {
        margin-bottom: 5px;
    }

    .col-lg-4, .col-md-4 {
        padding-left: 5px;
    }

    .fa-money {
        font-size: 1.5em;
    }

    .loading {
        margin: 5px 0 15px 0;
    }

</style>
