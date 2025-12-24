

<script setup>
import { ref, computed, watch, defineProps, defineEmits } from 'vue';
import RadioButton from 'primevue/radiobutton';

const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
 message: String,
});

const emit = defineEmits(['update:modelValue']);  


const localValue = ref(props.modelValue);

const errorMessage = computed(() => {
  return props.errorMessages[props.name] || null;
});


watch(localValue, (newValue) => {
  emit('update:modelValue', newValue);  
});

watch(() => props.modelValue, (newModelValue) => {
  
    if (newModelValue !== localValue.value) {
        localValue.value = newModelValue;
    }
});
</script>

<template>
  <div>
    <div v-for="option in options" :key="option.value" class="mb-2">
      <div class="flex items-center">
        <RadioButton
          :inputId="option.value"
          :value="option.value"
          v-model="localValue"  
          :name="name"
          :disabled="disabled"
        />
        <label :for="option.value" class="ml-2 font-medium text-gray-800 cursor-pointer">
          {{ option.label }}
        </label>
      </div>

      <div v-if="localValue === option.value" class="ml-7 mt-2">
        <slot :name="option.value.replace(/\s+/g, '_')"></slot>
      </div>
    </div>

    <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
  </div>
</template>