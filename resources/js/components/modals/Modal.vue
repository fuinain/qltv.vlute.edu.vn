<!-- resource/js/modals/Modal.vue -->
<template>
    <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modal">
        <div :class="['modal-dialog', `modal-${size}`]">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-bold">{{ title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            @click="handleClose(false)">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <div class="modal-footer">
                    <slot name="buttons">
                        <button v-if="showFooter" type="button" class="btn btn-primary text-bold" @click="confirm(true)">
                        <i class="far fa-save"></i>&nbsp;
                        {{ save }}
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
        showFooter: {
            type: Boolean,
            default: true
        },
        size: {
            type: String,
            default: 'lg',
            validator: function(value) {
                return ['sm', 'md', 'lg', 'xl'].includes(value);
            }
        }
    },
    data() {
        return {
            title: 'Không tiêu đề',
            save: 'Lưu thông tin'
        }
    },
    mounted() {
        const modalElement = this.$refs.modal;
        this.modalInstance = new window.bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
    },
    methods: {
        openModal() {
            this.modalInstance.show();
            return new Promise((resolve) => {
                this.resolveModal = resolve;
            });
        },
        show() {
            this.modalInstance.show();
        },
        closeModal() {
            this.modalInstance.hide();
        },
        submit() {
            this.confirm(true);
        },
        confirm(result) {
            if (this.resolveModal) {
                this.resolveModal(result);
                this.resolveModal = null;
            }
        },
        handleClose() {
            this.confirm(false);
        }
    }
}
</script>

<style scoped>
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Thêm style cho modal-xl */
:deep(.modal-xl) {
    max-width: 1140px;
}
</style>
