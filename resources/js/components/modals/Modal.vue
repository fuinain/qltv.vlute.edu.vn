<!-- resource/js/modals/Modal.vue -->
<template>
    <div class="modal fade" tabindex="-1" aria-hidden="true" ref="modal">
        <div class="modal-dialog modal-lg">
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
                    <button type="button" class="btn btn-primary text-bold" @click="confirm(true)">
                        <i class="far fa-save"></i>&nbsp;
                        {{ save }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
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
        closeModal() {
            this.modalInstance.hide();
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
</style>
