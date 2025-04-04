<template>
  <div class="form-group">
    <label>{{ label }}</label>
    <textarea
        :placeholder="placeholder"
        :class="textareaClass"
        :id="textareaId"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
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
      default: 'Chưa đặt tên',
    },
    textareaClass: {
      type: String,
      default: 'form-control',
    },
    textareaId: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
  },
};
</script>
