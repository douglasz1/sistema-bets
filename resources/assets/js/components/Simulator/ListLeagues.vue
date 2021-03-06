<template>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" :class="{'block-open': !leaguesMenu}">
        <div class="block full">
            <div class="block-title block-title-cursor btn-effect-ripple" @click="leaguesMenu = !leaguesMenu">
                <h2><i class="fa fa-bars"></i> Ligas</h2>
            </div>
            <transition name="fade">
                <div class="row" v-if="leaguesMenu">
                    <div class="col-sm-6" v-if="leagues.length > 0">
                        <a href="javascript:" @click="selectLeague('')" class="widget">
                            <div class="widget-content themed-background-primary clearfix">
                                <img src="/storage/flags/default.svg" class="pull-left flag-league">
                                <h4 class="widget-heading h5 text-light"><strong>Todas as ligas</strong></h4>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6" v-for="league in leagues" :key="league.id">
                        <a href="javascript:" @click="selectLeague(league.league_id)" class="widget">
                            <div class="widget-content themed-background-primary clearfix">
                              <img :src="'/storage/flags/' + league.flag" class="pull-left flag-league">
                                <h4 class="widget-heading h5 text-light">
                                  <strong style="text-transform: uppercase">
                                    {{ league.sport.name }} -
                                  </strong>
                                  <strong>
                                    <!-- {{ `${league.country.name}: ${league.name} (${league.matches_count})` }} -->
                                    {{ `${league.name} (${league.matches_count})` }}
                                  </strong>
                                </h4>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-12" v-if="leagues.length === 0">
                        <h4 class="widget-heading h4">
                            <strong>Nenhuma liga com partidas para hoje</strong>
                        </h4>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
  export default {
    data() {
      return {
        leaguesMenu: false
      }
    },
    props: ["leagues"],
    computed: {
      campeonatosOrdenados() {
        return _.orderBy(this.leagues, ['country.name'], ['asc'])
      }
    },
    methods: {
      selectLeague(leagueId) {
        this.leaguesMenu = false;
        this.$parent.getSelectedLeague(leagueId);
      },
    }
  }
</script>

<style scoped>
    .widget-content .pull-left {
        margin-right: 5px;
    }
    .widget, .block {
        margin-bottom: 7px;
    }

    .widget-content {
        padding: 0;
        white-space: nowrap;
    }

    h4 {
        overflow: hidden;
        font-size: 15px;
    }

    .block-open .block.full {
        padding-bottom: 0;
    }

    .block-open .block-title {
        margin-bottom: 0;
    }

    @media screen and (max-width: 767px) {
        .widget {
            margin-bottom: 2px;
        }
    }
</style>
