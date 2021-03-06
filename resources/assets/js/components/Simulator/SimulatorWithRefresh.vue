<template>
    <vue-pull-refresh :on-refresh="onRefresh" :config="pullRefreshLabels">
        <simulator ref="simulator"></simulator>
    </vue-pull-refresh>
</template>

<script>
    import simulator from './Index.vue'
    import vuePullRefresh from 'vue-pull-refresh'

    export default {
        components: {vuePullRefresh},
        data: function () {
            return {
                pullRefreshLabels: {
                    errorLabel: 'Erro ao atualizar',
                    startLabel: 'Puxe para atualizar',
                    readyLabel: 'Solte para atualizar',
                    loadingLabel: 'Atualizando',
                },
            }
        },
        methods: {
            onRefresh: function() {
                return this.$refs.simulator.getLeagues('today');
            },
        }
    }
</script>