<template>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Imprimir tabela de cotações </h2>
        </div>
        <div v-for="league in leagues" :key="league.id" class="widget">
            <div @click.prevent="printLeague(league.league_id)" class="widget-content themed-background-primary clearfix">
                <img :src="'/storage/' + league.flag"
                     class="pull-left flag-league">
                <h4 class="widget-heading h4 text-light">
                    <strong>{{ league.name }}</strong>
                </h4>
            </div>
        </div>
    </div>
</template>

<script>
    import {showToast} from '../../functions/showToast'

    export default {
        data () {
            return {
                leagues: [],
                userId: '',
            }
        },
        methods: {
            getLeagues: function () {
                this.$http.post('/leagues/toprint').then(response => {
                    response.json().then(res => {
                        this.leagues = res.leagues;
                        this.userId = res.userId;
                    });
                },
                err => showToast(err.message))
            },
            printLeague: function (id) {
                try {
                    return android.getLeague(id, this.userId);
                } catch (err) {
                    return showToast(err.message);
                }
            }
        },
        created () {
            this.getLeagues();
        }
    }
</script>

<style scoped>
    .widget {
        cursor: pointer;
        margin-bottom: 12px !important;
    }
</style>
