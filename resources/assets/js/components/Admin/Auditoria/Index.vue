<template>
  <div class="block full bordered">
    <div class="block-title">
      <h2>Operadores com apostas</h2>
    </div>
    <div class="btn-group btn-group-full btn-group-space p-lr p-tb">
      <div class="form-group">
        <input v-model="dataInicial" type="date" class="form-control">
      </div>
      <div class="form-group">
        <input v-model="dataFinal" type="date" class="form-control">
      </div>
      <button v-if="loading" type="button" class="btn btn-primary">
        <i class="fa fa-spinner fa-spin"></i>
        Carregando
      </button>
      <button v-else @click.prevent="listar" class="btn btn-success">
        <i class="fa fa-filter"></i> Filtrar
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter table-condensed table-striped table-hover">
        <thead>
          <tr>
            <td>
              <b>Alterado por</b>
            </td>
            <td>
              <b>Usuário</b>
            </td>
            <td>
              <b>Dados alterados</b>
            </td>
            <td>
              <b>Data/Hora</b>
            </td>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs" :key="log.id">
            <td>{{ log.causer.name }}</td>
            <td>
              <b>{{ log.subject.name }}</b>
            </td>
            <td>
              <ul v-for="(value, key) in log.properties.attributes" :key="key">
                <li>
                  {{ pegarNome(key) }}:
                  <span class="text-info">({{ log.properties.old[key] }})</span>
                  <b class="text-warning">{{ value }}</b>
                </li>
              </ul>
            </td>
            <td>{{ formatarData(log.created_at) }}</td>
          </tr>
          <tr v-if="logs.length === 0">
            <td colspan="4" class="text-center">
              <b>Nenhum cambista encontrado</b>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "auditoria",
  data() {
    return {
      loading: true,
      dataFinal: "",
      dataInicial: "",
      filtro: null,
      logs: []
    };
  },
  methods: {
    formatarData(data) {
      return moment(data).format("DD/MM/YYYY H:m");
    },
    pegarNome(chave) {
      const campos = {
        name: "Nome de usuário",
        username: "Login",
        quotation_modifier: "Alterar cotações",
        profit_percentage: "Comissão do lucro",
        manager_commission: "",
        balance: "Saldo",
        daily_limit: "Limite diário 1 palpite",
        limit: "Limite",
        sales_goal: "Meta de vendas",
        one_tip_quotation_min: "Cotação mínima para 1 palpite",
        two_tip_quotation_min: "Cotação mínima para 2 palpites",
        three_tip_quotation_min: "Cotação mínima para 3 palpites",
        max_prize: "Prêmio máximo",
        max_prize_multiplier: "Múltiplicador",
        tips_min: "Mínimo de jogos",
        tips_max: "Máximo de jogos",
        commission1: "Comissão para 1 jogo",
        value_min1: "Valor mínimo para 1 jogo",
        value_max1: "Valor máximo para 1 jogo",
        commission2: "Comissão para 2 jogos",
        value_min2: "Valor mínimo para 2 jogos",
        value_max2: "Valor máximo para 2 jogos",
        commission3: "Comissão de 3 a 5 jogos",
        value_min3: "Valor mínimo para 3 a 5 jogos",
        value_max3: "Valor máximo para 3 a 5 jogos",
        commission6: "Comissão de 6 a 10 jogos",
        value_min6: "Valor mínimo para 6 a 10 jogos",
        value_max6: "Valor máximo para 6 a 10 jogos",
        commission11: "Comissão de 11 a 15 jogos",
        value_min11: "Valor mínimo para 11 a 15 jogos",
        value_max11: "Valor máximo para 11 a 15 jogos",
        commission16: "Comissão para 16+ jogos",
        value_min16: "Valor mínimo para 16+ jogo",
        value_max16: "Valor máximo para 16+ jogo",
        comments: "Descrição",
        user_id: "Gerente ID",
        company_id: "Empresa ID",
        comissao_ao_vivo: "Comissão para ao vivo",
        ao_vivo: "Ao vivo disponível"
      };
      return campos[chave];
    },
    listar() {
      this.loading = true;

      this.$http
        .get("/admin/auditoria/listar", {
          params: { dataInicial: this.dataInicial, dataFinal: this.dataFinal }
        })
        .then(response => response.json())
        .then(resp => {
          this.loading = false;
          this.logs = resp.logs;
        })
        .catch(err => {
          this.loading = false;
          console.log(err);
        });
    }
  },
  created() {
    this.listar();
    this.dataInicial = new Date().toISOString().slice(0, 10);
    this.dataFinal = this.dataInicial;
  }
};
</script>
