<template>
    <div class="form-group">
        <label>{{ label }} <span v-if="note" class="input-note">{{ note }}</span></label>
        <select
            :class="selectClass"
            :id="selectId"
            :value="modelValue"
            :disabled="isDisabled"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
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
            default: 'Chưa đặt tên',
        },
        placeholder: {
            type: String,
            default: 'Chọn một tùy chọn',
        },
        selectClass: {
            type: String,
            default: 'form-control form-control-md',
        },
        selectId: {
            type: String,
            default: '',
        },
        modelValue: {
            type: [String, Number],
            default: '',
        },
        options: {
            type: Array,
            default: () => [],
        },
        isDisabled: {
            type: Boolean,
            default: false,
        },
        note: {
            type: String,
            default: '',
        },
    },
}
</script>
<style scoped>
.input-note {
  color: #dc3545;
  font-size: 12px;
  font-style: italic;
  font-weight: bold;
}
</style>