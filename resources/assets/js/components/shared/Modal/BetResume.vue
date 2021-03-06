<template>
    <div id="bilhete-bets">
        <modal ref="modal" :title="title" className="bet-resume">
            <div v-if="bet !== null"  class="modelo-bilhete">
                <div class="corpo-bilhete">
                    <h2 style="text-align: center;">{{ banca }}</h2>
                    <div class="detalhes-bilhete">
                        <span><b>DATA:</b> </span>
                        <span>{{ bet.bet_date }}</span>
                        <br>
                        <span><b>VENDEDOR:</b> </span>
                        <span>{{ bet.seller.username }}</span>
                        <br>
                        <span><b>CLIENTE:</b> </span>
                        <span>{{ bet.client_name }}</span>
                    </div>
                    <hr>

                    <div class="palpites-bilhete">
                        <div style="display: inline-block; width: 49%; text-align: left;">
                            OPÇÃO
                        </div>
                        <div style="display: inline-block; width: 49%; text-align: right;">
                            COTAÇÃO
                        </div>
                    </div>
                    <hr>

                    <div
                        v-for="tip in bet.tips"
                        :key="tip.id"
                        class="palpite"
                    >
                        <div>
                            <div style="display: inline-block; width: 99%; text-align: left;">
                                <b>{{ tip.match.sport.name }}</b> - <b>{{tip.match.human_date}}</b>
                            </div>
                        </div>

                        <div>
                            <div style="display: inline-block; width: 99%; text-align: left;">
                                {{ tip.match.league.country.name }}: {{  tip.match.league.name  }}
                            </div>
                        </div>

                        <div>
                            <b>{{ tip.match.match_name }}</b>
                        </div>

                        <div>
                            {{  tip.bet_name  }}
                        </div>
                        <div>
                            <div style="display: inline-block; width: 49%; text-align: left;">
                                {{ tip.choice_name}}
                            </div>
                            <div style="display: inline-block; width: 49%; text-align: right;">
                                {{ tip.value }}
                            </div>
                        </div>
                        <div style="display: inline-block; width: 50%; text-align: left;">
                            <b>Situação:</b>
                        </div>
                        <div style="display: inline-block; width: 48%; text-align: right; text-transform: capitalize;">
                            <span class="btn-vencedor" v-if="tip.status === 'win'">Vencedor</span>
                            <span class="btn-perdedor" v-else-if="tip.status === 'lose'">Perdedor</span>
                            <span class="btn-cancelado" v-else-if="tip.status === 'canceled'">Cancelado</span>
                            <span class="btn-aguardando" v-else-if="tip.status === 'pending'">Aguardando</span>
                        </div>
                        <hr>
                    </div>

                    <div class="resumo-bilhete">
                        <div style="display: inline-block; width: 49%; text-align: left;">
                            <span style="display: inline-block"><b>Quantidade de Jogos:</b></span>
                        </div>
                        <div style="display: inline-block; width: 49%; text-align: right;">
                            <span style="display: inline-block">
                                {{ bet.tips_quantity }}
                            </span>
                        </div>

                        <div id="conteudo_PanelCotacaoGeral">
                            <div style="display: inline-block; width: 49%; text-align: left;">
                                <span style="display: inline-block"><b>Cotação:</b></span>
                            </div>
                            <div style="display: inline-block; width: 49%; text-align: right;">
                                <span style="display: inline-block">
                                    {{ bet.quotation_total | formatQuotation }}
                                </span>
                            </div>
                        </div>

                        <div style="display: inline-block; width: 49%; text-align: left;">
                            <span style="display: inline-block"><b>Total Apostado:</b></span>
                        </div>
                        <div style="display: inline-block; width: 49%; text-align: right;">
                            <span style="display: inline-block">
                                {{ bet.bet_value | formatMoney }}
                            </span>
                        </div>

                        <div style="display: inline-block; width: 49%; text-align: left;">
                            <span style="display: inline-block"><b>Possível Retorno:</b></span>
                        </div>
                        <div style="display: inline-block; width: 49%; text-align: right;">
                            <span style="display: inline-block">
                                {{ bet.prize | formatMoney }}
                            </span>
                        </div>


                        <hr>
                        <div style="display: inline-block; width: 100%; text-align: center;">
                            <h4>CÓDIGO</h4>
                            <b>{{ bet.id }}</b>
                        </div>
                        <hr>
                        <div style="text-align: center;">
                            Eu li e concordo com as regras da {{ banca }}.
                            <h4>Boa sorte!</h4>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="bet !== null && canPrint" slot="footer" class="footer">
                <button @click.prevent="hideModal" class="btn btn-effect-ripple btn-warning">
                    <i class="fa fa-remove"></i> Fechar
                </button>
                <a :href="bet.print_url" target="_blank" class="btn btn-effect-ripple btn-primary hidden-sm hidden-xs">
                    <i class="fa fa-share-square-o"></i> Abrir para impressão
                </a>
                <button @click.prevent="shareOnWhats" class="btn btn-success btn-effect-ripple hidden-md hidden-lg">
                    <i class="fa fa-whatsapp"></i>
                    WhatsApp
                </button>
                <button @click.prevent="printBet" class="btn btn-effect-ripple btn-primary hidden-md hidden-lg">
                    <i class="fa fa-print"></i> Imprimir
                </button>
            </div>
        </modal>
    </div>
</template>

<script>
    import modal from './Modal.vue'

    export default {
        data () {
            return {
                banca: process.env.APP_NAME || 'Banca',
            }
        },
        components: {modal},
        props: {
            canPrint: {
                type: Boolean,
                required: false,
                default: false,
            },
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
            shareOnWhats () {
                try {
                    android.shareOnWhats(this.bet.id + "");
                } catch (err) {
                    window.open(`whatsapp://send?text=Use o link a seguir para acessar a sua simulação: \n\r\n\rhttps://${window.location.hostname}/web/bets/printing/${this.bet.id}`);
                    // showToast('Erro ao compartilhar no WhatsApp');
                }
            },
            printBet: function () {
                this.$parent.printBet(this.bet.id);
            }
        }
    }
</script>
