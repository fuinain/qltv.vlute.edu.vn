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
            default: 'Select an option'
        },
        options: {
            type: Array,
            required: true
        },
        value: {
            type: Array,
            default: () => []
        },
        selectedOptions: {
            type: Array,
            default: () => []
        }
    },
    mounted() {
        $(this.$refs.select).select2({
            placeholder: this.label,
            width: "100%",
            tags: true
        });

        $(this.$refs.select).val(this.value).trigger("change");

        $(this.$refs.select).on("change", () => {
            let selectedValues = $(this.$refs.select).val();
            // console.log("Giá trị được chọn:", selectedValues); // 🚀 Debug giá trị select2
            this.$emit("update:value", selectedValues);
            this.$emit("update:selectedOptions", this.options.filter(option => selectedValues.includes(option.value)));
        });
    },
    watch: {
        value(newVal) {
            this.$nextTick(() => {
                const parsedNewVal = typeof newVal === 'string' ? JSON.parse(newVal) : newVal;
                const currentValues = $(this.$refs.select).val() || [];
                if (JSON.stringify(parsedNewVal) !== JSON.stringify(currentValues)) {
                    // console.log("Cập nhật Select2 với giá trị mới:", parsedNewVal);
                    $(this.$refs.select).val(parsedNewVal).trigger("change");
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

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

</style>
