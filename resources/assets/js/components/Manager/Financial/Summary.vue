<template>
    <div>
        <div class="block full">
            <div class="block-title">
                <h2><i class="fa fa-bars"></i> Opções de filtragem</h2>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <label for="start_date">De</label>
                        <input v-model="startDate" type="date" name="start_date" id="start_date"
                               class="form-control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <label for="end_date">Até</label>
                        <input v-model="endDate" type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="form-group">
                        <button @click="getBets" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="block full block-with-table">
            <div class="block-title">
                <h2><i class="fa fa-bars"></i> Resumo financeiro do vendedor</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-condensed table-hover">
                    <thead>
                    <tr>
                        <td><b>Palpites</b></td>
                        <td><b>Vl. jogos</b></td>
                        <td><b>Vl. premia.</b></td>
                        <td><b>Vl. comis.</b></td>
                        <td><b>Vl. total</b></td>
                        <td v-if="profit_percentage != 0"><b>Comissão do lucro</b></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{ bets1Value | formatMoney }}</td>
                        <td>{{ bets1WinnersValue | formatMoney }}</td>
                        <td>{{ bets1Commissions | formatMoney }}</td>
                        <td>{{ bets1TotalValue | formatMoney }}</td>
                        <td v-if="profit_percentage != 0">{{ bets1ProfitCommissions | formatMoney }}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>{{ bets2Value | formatMoney }}</td>
                        <td>{{ bets2WinnersValue | formatMoney }}</td>
                        <td>{{ bets2Commissions | formatMoney }}</td>
                        <td>{{ bets2TotalValue | formatMoney }}</td>
                        <td v-if="profit_percentage != 0">{{ bets2ProfitCommissions | formatMoney }}</td>
                    </tr>
                    <tr>
                        <td>3+</td>
                        <td>{{ bets3Value | formatMoney }}</td>
                        <td>{{ bets3WinnersValue | formatMoney }}</td>
                        <td>{{ bets3Commissions | formatMoney }}</td>
                        <td>{{ bets3TotalValue | formatMoney }}</td>
                        <td v-if="profit_percentage != 0">{{ bets3ProfitCommissions | formatMoney }}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td>{{ betsValue | formatMoney }}</td>
                        <td>{{ betsWinnersValue | formatMoney }}</td>
                        <td>{{ betsCommissions | formatMoney }}</td>
                        <td>{{ betsTotalValue | formatMoney }}</td>
                        <td v-if="profit_percentage != 0">{{ betsProfitCommissions | formatMoney }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import {showToast} from '../../../functions/showToast'

    export default {
        data () {
            return {
                startDate: null,
                endDate: null,
                status: 'all',
                profit_percentage: 0,
                bets: []
            }
        },
        methods: {
            getBets: function () {
                this.$http.get('/manager/financial/summary/report', {
                    params: {
                        start_date: this.startDate,
                        end_date: this.endDate,
                        status: this.status
                    }
                }).then(response => {
                    response.json().then(res => {
                        this.bets = res.bets;
                        return this.profit_percentage = res.profit_percentage / 100;
                    });
                }, err => showToast(err.data.message));
            }
        },
        created() {
            this.getBets();
            this.startDate = new Date().toISOString().slice(0, 10);
            this.endDate = this.startDate;
        },
        computed: {
            bets1: function () {
                const list = this.bets;
                return _.filter(list, bets => bets.tips_quantity === 1);
            },
            bets1Value: function () {
                const list = this.bets1;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.bet_value) : 0);
            },
            bets1WinnersValue: function () {
                const list = this.bets1;
                return _.sumBy(list, bet => bet.status === "win" ? parseFloat(bet.prize) : 0);
            },
            bets1Commissions: function () {
                const list = this.bets1;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.commission) : 0);
            },
            bets1TotalValue: function () {
                let commissions = parseFloat(this.bets1Commissions),
                    betValue = parseFloat(this.bets1Value),
                    betWinnersValue = parseFloat(this.bets1WinnersValue);

                return betValue - commissions - betWinnersValue;
            },
            bets1ProfitCommissions: function () {
                return parseFloat(this.bets1TotalValue) * parseFloat(this.profit_percentage);
            },
            bets2: function () {
                const list = this.bets;

                return _.filter(list, bets => bets.tips_quantity === 2);
            },
            bets2Value: function () {
                const list = this.bets2;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.bet_value) : 0);
            },
            bets2WinnersValue: function () {
                const list = this.bets2;
                return _.sumBy(list, bet => bet.status === "win" ? parseFloat(bet.prize) : 0);
            },
            bets2Commissions: function () {
                const list = this.bets2;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.commission) : 0);
            },
            bets2TotalValue: function () {
                let commissions = parseFloat(this.bets2Commissions),
                    betValue = parseFloat(this.bets2Value),
                    betWinnersValue = parseFloat(this.bets2WinnersValue);

                return betValue - commissions - betWinnersValue;
            },
            bets2ProfitCommissions: function () {
                return parseFloat(this.bets2TotalValue) * parseFloat(this.profit_percentage);
            },
            bets3: function () {
                const list = this.bets;

                return _.filter(list, bets => bets.tips_quantity >= 3);
            },
            bets3Value: function () {
                const list = this.bets3;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.bet_value) : 0);
            },
            bets3WinnersValue: function () {
                const list = this.bets3;
                return _.sumBy(list, bet => bet.status === "win" ? parseFloat(bet.prize) : 0);
            },
            bets3Commissions: function () {
                const list = this.bets3;
                return _.sumBy(list, bet => bet.status !== "canceled" ? parseFloat(bet.commission) : 0);
            },
            bets3TotalValue: function () {
                let commissions = parseFloat(this.bets3Commissions),
                    betValue = parseFloat(this.bets3Value),
                    betWinnersValue = parseFloat(this.bets3WinnersValue);

                return betValue - commissions - betWinnersValue;
            },
            bets3ProfitCommissions: function () {
                return parseFloat(this.bets3TotalValue) * parseFloat(this.profit_percentage);
            },
            betsValue: function () {
                return parseFloat(this.bets1Value) + parseFloat(this.bets2Value) + parseFloat(this.bets3Value);
            },
            betsWinnersValue: function () {
                return parseFloat(this.bets1WinnersValue) + parseFloat(this.bets2WinnersValue) + parseFloat(this.bets3WinnersValue);
            },
            betsCommissions: function () {
                return parseFloat(this.bets1Commissions) + parseFloat(this.bets2Commissions) + parseFloat(this.bets3Commissions);
            },
            betsTotalValue: function () {
                return parseFloat(this.bets1TotalValue) + parseFloat(this.bets2TotalValue) + parseFloat(this.bets3TotalValue);
            },
            betsProfitCommissions: function () {
                return parseFloat(this.bets1ProfitCommissions) + parseFloat(this.bets2ProfitCommissions) + parseFloat(this.bets3ProfitCommissions);
            },
        }
    }
</script>

<style scoped>
    .block.full {
        padding-bottom: 0;
    }

    .block-title {
        margin-bottom: 5px;
    }

    label {
        display: none;
    }

    tr > td {
        text-align: right;
    }

    .table tfoot > tr > td {
        background-color: #dddddd;
    }
</style>