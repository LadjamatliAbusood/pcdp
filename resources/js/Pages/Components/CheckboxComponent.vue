<template>
<div>
 <div class="grid grid-cols-2 gap-x-6 gap-y-4 items-start py-2">
 
 <div v-for="option in options" :key="option.value" class="flex items-center">
 <input
 type="checkbox"
 :id="option.value"
 :value="option.value"
 v-model="localValue"
 :disabled="disabled"
 :name="name"
 class="h-5 w-5 text-blue-600 border-2 border-gray-300 rounded-base checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition-all duration-200"
 />
 <label :for="option.value" class="ml-2 cursor-pointer block text-base font-medium text-gray-700">{{ option.label }}</label>
 </div>
 </div>

 <div v-if="localValue.includes('others')" class="mt-4">
 <InputText 
 v-model="othersInput" 
 placeholder="Please specify" 
 class="w-full block text-base font-medium text-gray-700" 
 @blur="emitFinalValue"
 @keyup.enter="emitFinalValue" 
 />
 </div>

 <small class="text-md font-semibold text-red-500" v-if="message">{{ message }}</small>
</div>
</template>



<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';

const props = defineProps({

  modelValue: {
    type: Array,
    default: () => [],

  },

  options: {
    type: Array,
    required: true,

  },

  name: String,
  disabled: {
    type: Boolean,
    default: false,

  },

  message: String,

});


const emit = defineEmits(['update:modelValue']);
const getFixedOptionValues = () => props.options.filter(opt => opt.value !== 'others').map(opt => opt.value);

const localValue = ref([]);
const othersInput = ref('');



// --- Setup Initial State ---

const setupInitialState = () => {
  const fixedOptions = getFixedOptionValues();
  const initialModelValue = props.modelValue || [];

  const customValue = initialModelValue.find(val => !fixedOptions.includes(val) && val !== 'others');
  othersInput.value = customValue || '';

  const standardSelections = initialModelValue.filter(val => fixedOptions.includes(val));
  localValue.value = standardSelections;

  if (customValue || initialModelValue.includes('others')) {

    if (!localValue.value.includes('others')) {

      localValue.value.push('others');

    }

  }

};

setupInitialState();

const emitFinalValue = () => {
  const fixedOptions = getFixedOptionValues();
  let finalArray = [];

  finalArray = localValue.value.filter(val => fixedOptions.includes(val));

  const customValue = othersInput.value.trim();
  if (customValue !== '') {
    finalArray.push(customValue);
  } else if (localValue.value.includes('others')) {
    finalArray.push('others');
  }
  emit('update:modelValue', finalArray);

};



watch(localValue, (newValue) => {
  if (!newValue.includes('others')) {
    othersInput.value = '';
  }
  emitFinalValue();
}, { deep: true });

watch(() => props.modelValue, () => {
  setupInitialState();
}, { deep: true });



</script>