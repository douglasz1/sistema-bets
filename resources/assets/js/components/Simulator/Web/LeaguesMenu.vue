<template>
  <ul class="sidebar-nav">
    <li>
      <a @click.prevent="selectLeague('')" href="javascript:">
        <img class="sidebar-nav-icon" src="/storage/flags/default.svg">
        <span class="sidebar-nav-mini-hide">Todas as ligas</span>
      </a>
    </li>
    <li v-for="sport in sports">
      <a
        href="javascript:"
        class="sidebar-nav-menu"
        @click.prevent="sportOpened = sport.id"
        :class="{'open': sportOpened === sport.id}">
        <i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
        <span class="sidebar-nav-mini-hide">{{ sport.name }}</span>
      </a>
      <ul>
        <li
          v-for="league in campeonatosOrdenados(sport.leagues)"
          :key="league.id">
          <a
            @click="selectLeague(league.league_id)"
            :title="`${league.sport.name} - ${league.name} (${league.matches_count})`"
            href="javascript:">
            <img :src="'/storage/flags/' + league.flag" class="pull-left flag-league">
             {{ `${league.country.name}: ${league.name} (${league.matches_count})` }}
            <!--{{ `${league.name} (${league.matches_count})` }}-->
          </a>
        </li>
      </ul>
    </li>
  </ul>
</template>

<script>
  import {EventBus} from '../../../functions/EventBus'

  export default {
    data() {
      return {
        sportOpened: 1,
        sports: [],
      }
    },
    methods: {
      campeonatosOrdenados(leagues) {
        return _.orderBy(leagues, ['country.name'], ['asc'])
      },
      selectLeague(leagueId) {
        EventBus.$emit('change-league', leagueId);
        window.scrollTo(0, 0);
      },
      getLeagues() {
        this.$http.post('/web/leagues')
          .then(response => this.sports = response.data.sports)
          .catch(err => console.log(err));
      },
    },
    created() {
      this.getLeagues();
    }
  }
</script>

<style scoped>
  .flag-league {
    margin-right: 5px;
  }
  .sidebar-nav ul a {
    margin: 0;
    border-left: none;
    padding-left: 5px;
    padding-top: 5px;
  }
  li a {
    font-size: 14px;
    white-space: nowrap;
  }
</style>
