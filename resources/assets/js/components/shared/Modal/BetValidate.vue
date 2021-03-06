<template>
    <modal ref="modal" :title="title" className="bet-validate">
        <div v-if="bet !== null">
            <table class="table">
                <tr>
                    <td>
                        Nome: <b>{{ bet.client_name }}</b>
                    </td>
                    <td>
                        Data: <b>{{ bet.bet_date }}</b>
                    </td>
                </tr>
            </table>
            <table class="table">
                <tr v-for="tip in bet.tips">
                    <td>
                        <b>{{ tip.match.match_name }}</b> <br>
                        {{ tip.match.human_date }} -
                        {{ tip.choice_name }}
                        <b>({{ tip.value | formatQuotation }})</b>
                    </td>
                </tr>
            </table>
            N&deg; Jogos: {{ bet.tips.length }} /
            Total: <b>{{ bet.bet_value | formatMoney }}</b><br>
            Possível prêmio: <b>{{ bet.prize | formatMoney }}</b>
        </div>
        <div slot="footer">
            <button @click.prevent="hideModal" class="btn btn-effect-ripple btn-warning">
                <i class="fa fa-reply"></i> Voltar
            </button>
            <button @click.prevent="storeBet" class="btn btn-success btn-effect-ripple">
                <i class="fa fa-check"></i> Validar
            </button>
        </div>
    </modal>
</template>

<script>
    import modal from './Modal.vue'

    export default {
        components: {modal},
        props: {
            title: {
                required: true,
                type: String,
                default: 'Modal title'
            },
            bet: {
                required: true,
                default: null
            },
        },
        methods: {
            hideModal: function () {
                this.$refs.modal.hideModal();
            },
            showModal: function () {
                this.$refs.modal.showModal();
            },
            storeBet: function () {
                this.$parent.storeBet();
                this.hideModal();
            }
        }
    }
</script>

<style scoped>
    table {
        margin-bottom: 5px;
    }
    td {
        padding: 5px 0;
    }
    tr {
        border-bottom: solid 1px #eeeeee;
    }
</style>