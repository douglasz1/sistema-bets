<template>
  <div class="boxes">
    <div class="row row-reduced-margin">
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-futbol-o text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ bets.length }}</strong>
            </h2>
            <span class="text-muted">JOGOS</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-usd text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ loseQty }}</strong>
            </h2>
            <span class="text-muted">PERDIDOS</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-smile-o text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ pendingQty }}</strong>
            </h2>
            <span class="text-muted">ANDAMENTO</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-share text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ winnersQty }}</strong>
            </h2>
            <span class="text-muted">PREMIADOS</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row row-reduced-margin">
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-futbol-o text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ betInput | formatMoney }}</strong>
            </h2>
            <span class="text-muted">ENTRADA</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-share text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ betWinners | formatMoney }}</strong>
            </h2>
            <span class="text-muted">SAÍDA</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-smile-o text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ betCommissions | formatMoney }}</strong>
            </h2>
            <span class="text-muted">COMISSÃO</span>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 col-xs-3">
        <div class="widget">
          <div class="widget-content widget-content-mini text-right clearfix">
            <div class="widget-icon pull-left themed-background-primary">
              <i class="fa fa-usd text-light-op"></i>
            </div>
            <h2 class="widget-heading h5">
              <strong>{{ betBalance | formatMoney }}</strong>
            </h2>
            <span class="text-muted">SALDO</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      bets: {
        type: Array,
        required: true,
        default: []
      },
    },
    data () {
      return {}
    },
    computed: {
      betInput () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status !== 'canceled' ? parseFloat(bet.bet_value) : 0);
        return parseFloat(value).toFixed(2);
      },
      betWinners () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status === 'win' ? parseFloat(bet.prize) : 0);
        return parseFloat(value).toFixed(2);
      },
      betCommissions () {
        const list = this.bets;
        let value = _.sumBy(list, bet => bet.status !== 'canceled' ? parseFloat(bet.commission) : 0);
        return parseFloat(value).toFixed(2);
      },
      betBalance () {
        let value = parseFloat(this.betInput) - parseFloat(this.betWinners) - parseFloat(this.betCommissions);
        return parseFloat(value).toFixed(2);
      },
      winnersQty () {
        const list = this.bets
        let winners = _.filter(list, bet => bet.status === 'win')
        return winners.length
      },
      pendingQty () {
        const list = this.bets
        let pending = _.filter(list, bet => bet.status === 'pending')
        return pending.length
      },
      loseQty () {
        const list = this.bets
        let lose = _.filter(list, bet => bet.status === 'lose')
        return lose.length
      }
    },
  }
</script>

<style scoped>
  @media screen and (max-width: 767px) {
    .widget-content {
      position: relative;
    }
    .widget-icon {
      position: absolute;
      left: 5px;
      opacity: .2;
    }
  }

  .widget-icon {
    width: 44px;
    height: 44px;
    line-height: 40px;
  }

  span.text-muted {
    font-size: 12px;
  }
</style>
