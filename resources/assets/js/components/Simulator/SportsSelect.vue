<template>
  <div class="row">
    <div class="col-md-12">
      <div class="sport-list">
        <div
          v-if="aoVivoDisponivel"
          class="sport-list__item">
          <a href="/live">
            <img
              alt="Jogos ao vivo"
              class="sport-list__img"
              title="Jogos ao vivo"
              src="/images/sports/live.png">
            <span>Ao vivo</span>
          </a>
        </div>
        <div
          v-if="bolaoDisponivel"
          class="sport-list__item">
          <a href="/bolao">
            <img
              alt="Bolão"
              class="sport-list__img"
              title="Bolão"
              src="/images/sports/bolao.png">
            <span>Bolão</span>
          </a>
        </div>
        <div
          v-for="sport in sports"
          :key="sport.id"
          class="sport-list__item">
          <a @click.prevent="selectSport(sport)" href="javascript:">
            <img
              class="sport-list__img"
              :title="sport.name"
              :src="`/images/sports/${sport.slug}.png`">
            <span>{{ sport.name }}</span>
          </a>
        </div>
        <div class="sport-list__item hide">
          <a href="javascript:">
            <img
              class="sport-list__img"
              title="Outros"
              src="/images/sports/others.png">
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'SportsSelect',
    props: {
      sports: {
        required: true
      },
      aoVivo: {
        default: false,
        type: Boolean,
        required: false
      },
      logged: {
        default: false,
        type: Boolean,
        required: false
      }
    },
    computed: {
      bolaoDisponivel() {
        return (process.env.BOLAO_DISPONIVEL == 'true' && this.logged)
      },
      aoVivoDisponivel() {
        return (process.env.AO_VIVO_DISPONIVEL == 'true' && this.aoVivo && this.logged)
      },
    },
    methods: {
      selectSport(sport) {
        this.$emit('selectSport', sport)
      }
    }
  }
</script>
