<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import Select from 'primevue/select';

// Define props for the component
const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [String, Number], 
    required: true,
  },
  options: {
    type: Array,
    required: true,
  },
  disableSelect: {
    type: Boolean,
    default: false,
  },
  message: String,
  placeholder: {
    type: String,
    default: '',
  },
});

// Define emits to communicate back with the parent component
const emit = defineEmits(['update:modelValue']);

// Create a local reactive variable for the selected value
const selectedValue = ref(props.modelValue);

// Watch for changes in the prop and update the local variable
watch(() => props.modelValue, (newVal) => {
  selectedValue.value = newVal;
});

// Emit the update event when the value changes
watch(selectedValue, (newValue) => {
  emit('update:modelValue', newValue);
});
</script>

<template>
  <div>
    <div class="mb-2">
      <label for="location" class="block text-sm font-medium text-gray-700">
        {{ name }}
      </label>


      


      <Select
        :placeholder="placeholder"
        v-model="selectedValue" 
        :options="options"
        :disabled="disableSelect"
        optionLabel="label"
        optionValue="value"
        class="w-full"
      />
      <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
    </div>
  </div>
</template>
