<template>
    <div class="form-group">
        <label>{{ label }}</label>
        <select ref="select" class="form-control select2" multiple>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.text }}
            </option>
        </select>
    </div>
</template>

<script>


export default {
    props: {
        label: {
            type: String,
            default: 'Select an option',
        },
        options: {
            type: Array,
            required: true,
        },
        modelValue: { // ✅ Chuẩn Vue 3 v-model
            type: Array,
            default: () => [],
        },
    },
    emits: ['update:modelValue'],
    mounted() {
        $(this.$refs.select).select2({
            placeholder: this.label,
            width: "100%",
            theme: "bootstrap4",
            tags: false, // ✅ Không cho nhập tay
        });

        $(this.$refs.select).val(this.modelValue).trigger("change");

        $(this.$refs.select).on("change", () => {
            const selectedValues = $(this.$refs.select).val();
            this.$emit('update:modelValue', selectedValues);
        });
    },
    watch: {
        modelValue(newVal) {
            this.$nextTick(() => {
                const parsed = typeof newVal === 'string' ? JSON.parse(newVal) : newVal;
                const current = $(this.$refs.select).val() || [];
                if (JSON.stringify(parsed) !== JSON.stringify(current)) {
                    $(this.$refs.select).val(parsed).trigger('change');
                }
            });
        }
    },
    beforeUnmount() {
        $(this.$refs.select).select2('destroy');
    }
};
</script>

<style>
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
}
</style>
