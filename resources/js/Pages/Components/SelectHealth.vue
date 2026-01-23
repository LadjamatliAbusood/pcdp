<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import Select from 'primevue/select';

const props = defineProps({
  modelValue: {
    type: [String, Number, Object],
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
    default: 'Select here...',
  },
});

const emit = defineEmits(['update:modelValue']);

// Local state for the Select component
const selectedValue = ref(props.modelValue);

// Sync local state when prop changes (e.g., form reset or edit)
watch(() => props.modelValue, (newVal) => {
  selectedValue.value = newVal;
});

// Emit changes back to the parent
watch(selectedValue, (newValue) => {
  emit('update:modelValue', newValue);
});

/**
 * Logic to inject the sub-header.
 * In your image, "For Referral" appears right before "Social Services Section".
 */
const shouldShowHeader = (option) => {
  return option && option.label === 'Social Services Section';
};

/**
 * Helper to safely find the selected object from the options array
 * to prevent the "Cannot read properties of undefined (reading 'find')" error.
 */
const getSelectedOption = (val) => {
  if (!val || !props.options) return null;
  return props.options.find(o => o.value === val);
};
</script>

<template>
  <div class="w-full">
 <Select
  v-model="selectedValue"
  :options="options"
  optionLabel="label"
  optionValue="value"
  dataKey="value" 
  :placeholder="placeholder"
  :disabled="disableSelect"
  class="w-full"
  :pt="{
    root: { class: 'flex items-center' }
  }"
>
      <template #value="slotProps">
        <div v-if="slotProps.value" class="flex items-center gap-2">
          <span 
            class="w-3 h-3 rounded-full" 
            :style="{ backgroundColor: getSelectedOption(slotProps.value)?.color || '#cbd5e1' }"
          ></span>
          <span class="font-semibold rounded-lg block">
            {{ getSelectedOption(slotProps.value)?.label }}
          </span>
        </div>
        <span v-else class="font-semibold rounded-lg block">
          {{ placeholder }}
        </span>
      </template>

      <template #option="slotProps">
        <div class="flex flex-col w-full">
          <div 
            v-if="shouldShowHeader(slotProps.option)" 
            class="text-[11px] uppercase tracking-wider font-bold text-gray-400 mb-2 mt-2"
          >
            For Referral
          </div>
          
          <div class="flex items-center gap-3 py-1">
            <span 
              class="w-3.5 h-3.5 rounded-full flex-shrink-0" 
              :style="{ backgroundColor: slotProps.option.color }"
            ></span>
            <span class="text-sm text-gray-700 font-semibold rounded-lg block">{{ slotProps.option.label }}</span>
          </div>
        </div>
      </template>
    </Select>

    <transition name="p-connected-overlay">
      <small 
        v-if="message" 
        class="text-sm font-semibold text-red-500"
      >
        {{ message }}
      </small>
    </transition>
  </div>
</template>

<style scoped>
/* Adjusting PrimeVue Select internal styles to match your image padding */
:deep(.p-select-option) {
  padding: 0.5rem 0.75rem !important;
}

/* Ensuring the "For Referral" text isn't treated as a clickable area visually */
.text-gray-400 {
  user-select: none;
  pointer-events: none;
}

/* Styling the border to look like standard Tailwind inputs */
:deep(.p-select) {
  border-color: #d1d5db; /* gray-300 */
  border-radius: 0.5rem; /* rounded-lg */
}

:deep(.p-select:not(.p-disabled).p-focus) {
  border-color: #3b82f6; /* blue-500 */
  box-shadow: 0 0 0 1px #3b82f6;
}
</style>