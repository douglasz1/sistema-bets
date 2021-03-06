<template>
    <div class="block full bordered">
        <div class="block-title">
            <h2>Validar PIN</h2>
        </div>
        <div class="row p-tb p-lr">
            <div class="col-md-6 p-b">
                <div class="form-group">
                    <input type="number" v-model="code" autofocus autocomplete="off" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a style="width: 100%;" :href="`/home?pin=${this.code}`" class="btn btn-success">
                        <i class="fa fa-check"></i>
                        <strong>Validar</strong>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center loading" v-show="loading">
                <i class="fa fa-spinner fa-2x fa-spin text-success"></i>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        data () {
            return {
                code: '',
                bet: null,
                loading: false,
            }
        },
        methods: {
            printBet (id) {
              try {
                return android.getTicket(id + "")
              } catch (err) {
                return showToast(err.message)
              }
            },
        }
    }
</script>
