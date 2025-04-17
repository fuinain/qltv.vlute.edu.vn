<template>
  <div class="form-group" v-if="visible">
    <label>{{ label }}</label>
    <input :placeholder="placeholder" :type="type" :class="inputClass" :id="inputId" :value="modelValue"
      :disabled="isDisabled" @input="$emit('update:modelValue', $event.target.value)" />
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
    },
    placeholder: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'text',
    },
    inputClass: {
      type: String,
      default: 'form-control',
    },
    inputId: {
      type: String,
      default: '',
    },
    modelValue: {
      type: [String, Number],
      default: '',
    },
    isDisabled: {
      type: Boolean,
      default: false,
    },
    visible: {
      type: Boolean,
      default: true,
    },
  },
}
</script>
