<template>
  <div>
    <div class="list-group-item col-md-12">
      <div
        v-for="q in quotations[0]"
        :key="q.id"
        @click="addQuotations(q)"
        :class="{'btn-selected': verifyChoice(q.id) }"
        class="btn-quotation"
      >
        <div class="btn-quotation__flexwrap">
          <div class="btn-quotation__label-wrapper">
            <span class="btn-quotation__label">{{ q.choice_name }}</span>
          </div>
          <div class="btn-quotation__odds-wrapper">
            <span class="btn-quotation__odds">{{ alterarCotacao(q.value) | formatQuotation }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="list-group-item col-md-12">
      <div
        v-for="q in quotations[1]"
        :key="q.id"
        @click="addQuotations(q)"
        :class="{'btn-selected': verifyChoice(q.id) }"
        class="btn-quotation"
      >
        <div class="btn-quotation__flexwrap">
          <div class="btn-quotation__label-wrapper">
            <span class="btn-quotation__label">{{ q.choice_name }}</span>
          </div>
          <div class="btn-quotation__odds-wrapper">
            <span class="btn-quotation__odds">{{ alterarCotacao(q.value) | formatQuotation }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="list-group-item col-md-12">
      <div
        v-for="q in quotations[2]"
        :key="q.id"
        @click="addQuotations(q)"
        :class="{'btn-selected': verifyChoice(q.id) }"
        class="btn-quotation"
      >
        <div class="btn-quotation__flexwrap">
          <div class="btn-quotation__label-wrapper">
            <span class="btn-quotation__label">{{ q.choice_name }}</span>
          </div>
          <div class="btn-quotation__odds-wrapper">
            <span class="btn-quotation__odds">{{ alterarCotacao(q.value) | formatQuotation }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ThreeOptions",
  props: ["quotations"],
  methods: {
    alterarCotacao(valor) {
      return this.$parent.alterarCotacao(valor);
    },
    addQuotations(quotation) {
      this.$parent.addQuotations(quotation);
    },
    verifyChoice(choice_id) {
      return this.$parent.verifyChoice(choice_id);
    }
  }
};
</script>

<style scoped>
.list-group-item > .btn-quotation {
  margin: 10px 0;
}
.list-group-item > .btn-quotation:first-child {
  margin-top: 0;
}
.list-group-item > .btn-quotation:last-child {
  margin-bottom: 0;
}
</style>
