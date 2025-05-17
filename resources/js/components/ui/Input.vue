<template>
  <div class="form-group" v-if="visible">
    <label>{{ label }} <span v-if="note" class="input-note">{{ note }}</span></label>
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
    note: {
      type: String,
      default: '',
    }
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
