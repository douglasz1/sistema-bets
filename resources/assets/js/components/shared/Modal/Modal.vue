<template>
    <div id="modal-regular" class="modal" :class="className" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><strong>{{ title }}</strong></h3>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button @click.prevent="hideModal" type="button" class="btn btn-effect-ripple btn-danger">
                            <i class="fa fa-remove"></i> Fechar
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            className: {
                required: false,
                default: '',
            },
            title: {
                required: true,
                type: String,
                default: 'Título do modal'
            },
        },
        methods: {
            hideModal: function () {
                if (this.className && this.className !== '') {
                    $('.' + this.className).modal('hide');
                    return false;
                }

                $('.modal').modal('hide');
            },
            showModal: function () {
                if (this.className && this.className !== '') {
                    $('.' + this.className).modal('show');
                    return false;
                }

                $('.modal').modal('show');
            }
        },
    }
</script>

<style scoped>
    hr {
        margin: 5px;
    }

    @media screen and (max-width: 767px) {
        .close {
            font-size: 2em;
        }
    }
</style>