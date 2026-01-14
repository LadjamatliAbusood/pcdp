<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import Select from 'primevue/select';

const props = defineProps({
  name: String,
  modelValue: [String, Number],
  options: Array,
  disableSelect: Boolean,
  message: String,
  placeholder: String,
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectedValue = ref(props.modelValue);

watch(() => props.modelValue, val => {
  selectedValue.value = val;
});

watch(selectedValue, val => {
  emit('update:modelValue', val);
  emit('change', val); // 🔑 important
});
</script>

<template>
  <Select
    v-model="selectedValue"
    :options="options"
    optionLabel="label"
    optionValue="value"
    :placeholder="placeholder"
    class="w-full"
  />
</template>
