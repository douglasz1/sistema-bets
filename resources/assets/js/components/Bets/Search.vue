<template>
    <div id="bilhete-bets">
        <form @submit.prevent="search" action="javascript:" method="post" class="navbar-form-custom">
            <div class="input-group">
                <input v-model="code" type="number" id="top-search" class="form-control" placeholder="Buscar bilhete…">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-search btn-effect-ripple btn-default">Ok</button>
                </span>
            </div>
        </form>
        <modal ref="modalSearch" title="Resultado da pesquisa" className="bet-search-resume">
            <div v-if="bet !== null" class="modelo-bilhete">
                <div class="corpo-bilhete">
                    <h2 style="text-align: center;">{{ banca }}</h2>
                    <div class="detalhes-bilhete">
                        <span><b>DATA: </b></span>
                        <span>{{ bet.bet_date }}</span>
                        <br>
                        <span><b>VENDEDOR: </b></span>
                        <span>{{ bet.seller.username }}</span>
                        <br>
                        <span><b>CLIENTE: </b></span>
                        <span>{{ bet.client_name }}</span>
                        <br>
                    </div>
                    <hr align="center">
                    <div class="palpites-bilhete">
                        <div style="display: inline-block; width: 49%; text-align: left;">
                            <b>OPÇÃO</b>
                        </div>
                        <div style="display: inline-block; width: 49%; text-align: right;">
                            <b>COTAÇÃO</b>
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
                                {{ bet.tips.length }}
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
                        <div style="display: inline-block; width: 99%; text-align: center;">
                            Bilhete
                        </div>
                        <div style="display: inline-block; width: 99%; text-align: center;">
                            {{ bet.id }}
                        </div>
                        <hr>
                        <div style="text-align: center;">
                            Eu li e concordo com as regras da {{ banca }}.
                            <h4>Boa sorte!</h4>
                        </div>
                    </div>
                </div>
                <b
                    class="btn-situacao"
                    :class="`btn-${bet.status}`">
                        <span v-if="bet.status === 'win'">Vencedor</span>
                        <span v-else-if="bet.status === 'lose'">Perdedor</span>
                        <span v-else-if="bet.status === 'canceled'">Cancelado</span>
                        <span v-else-if="bet.status === 'pending'">Aguardando</span>
                </b>
            </div>

            <div v-if="bet !== null" slot="footer" class="footer">
                <button @click.prevent="hideModal" class="btn btn-effect-ripple btn-warning">
                    <i class="fa fa-remove"></i> Fechar
                </button>
            </div>

        </modal>
    </div>
</template>



<script>
    import modal from '../shared/Modal/Modal.vue'
    import {showToast} from '../../functions/showToast'

    export default {
        components: {modal},
        data () {
            return {
                bet: null,
                code: '',
                banca: process.env.APP_NAME || 'Banca',
            }
        },
        methods: {
            search: function () {
                this.$http.get('/api/app/bet/' + this.code)
                    .then(response => {
                        response.json().then(res => {
                            this.bet = res.bet;
                            this.showModal();
                        });
                    }, err => {
                        err.json().then(err => {
                            showToast(err.message);
                        })
                    });
            },
            hideModal: function () {
                document.querySelector('.navbar-fixed-top').style.zIndex = 1030;
                this.$refs.modalSearch.hideModal();
            },
            showModal: function () {
                document.querySelector('.navbar-fixed-top').style.zIndex = 1041;
                this.$refs.modalSearch.showModal();
            },
        }
    }
</script>
