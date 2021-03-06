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
                        <b>{{ choice.choice_name }}:</b>  {{ choice.value | formatQuotation }}
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
                        <button type="reset" class="btn btn-lg btn-danger" @click="cancelBet">
                            <i class="fa fa-remove"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-lg btn-success">
                            <i class="fa fa-check"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <modal ref="modal" className="bet-code" title="Código da simulação">
            Simulação salva com sucesso!
            <h3 class="text-center">Código: {{ betResult.id }}</h3>
        </modal>
    </div>
</template>

<script>
  import formError from '../../shared/FormError'
  import modal from '../../shared/Modal/Modal'
  import { showToast } from '../../../functions/showToast'

  export default {
    components: {formError, modal},
    props: ['maxPrize', 'maxPrizeMultiplier'],
    data () {
      return {
        loading: false,
        choices: [],
        showSimulator: false,
        bet_value: 5,
        errors: [],
        betResult: []
      }
    },
    methods: {
      addQuotations (choiceData) {
        const list = this.choices
        let existsMatch = _.findIndex(list, choice => choice.match_id === choiceData.match_id)

        if (existsMatch >= 0) {
          let existsQuotation = _.findIndex(list, choice => choice.id === choiceData.id)

          if (existsQuotation < 0) {
            this.choices.splice(existsMatch, 1)
            this.choices.push(choiceData)
          } else {
            this.choices.splice(existsQuotation, 1)
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
        const list = this.choices
        let result = _.findIndex(list, choice => {
          // return choice.match_id === matchId && choice.bet_slug !== 'full_time_result'
          return choice.match_id === matchId && (choice.bet_slug !== 'full_time_result' && choice.bet_slug !== 'to_win_match')
        })

        return (result >= 0)
      },
      cancelBet () {
        this.betResult = []
        this.choices = []
        this.name = ''
        this.bet_value = 5
        this.showSimulator = false
      },
      storeBet () {
        if (this.choices.length <= 0) {
          return showToast('Você precisa adicionar ao menos um palpite!')
        }

        this.errors = []
        this.loading = true
        this.$http.post('/web/bets/store', {
          bet_value: this.bet_value,
          choices: this.choices
        }).then(response => {
          response.json().then(res => {
            this.cancelBet()
            this.betResult = res.bet
            this.$refs.modal.showModal()
            this.loading = false
          })
        }, err => {
          err.json().then(err => {
            this.errors = err
            this.loading = false
          })
        })
      }
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
