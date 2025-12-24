
<script setup>
import { ref, computed, watch, defineProps, defineEmits } from 'vue';
import RadioButton from 'primevue/radiobutton';
import TextInputField from './TextInputField.vue';

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
  othersValue: {
    type: String,
    default: 'Others', 
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  message: String,
});

const emit = defineEmits(['update:modelValue']); 
const localSelection = ref(null);
const customText = ref('');
const othersValue = props.othersValue;
const isCustomValue = computed(() => {
 
  return props.modelValue && !props.options.some(opt => opt.value === props.modelValue);
});


if (isCustomValue.value) {

  localSelection.value = othersValue;
  customText.value = props.modelValue;
} else {

  localSelection.value = props.modelValue;
}


watch(localSelection, (newSelection) => {
  if (newSelection !== othersValue) {

    emit('update:modelValue', newSelection);
  } else {
  
    emit('update:modelValue', customText.value);
  }
});


watch(customText, (newCustomText) => {
  if (localSelection.value === othersValue) {
   
    emit('update:modelValue', newCustomText);
  }
});

</script>
<template>
  <div>
    <div class="grid grid-cols-4 gap-1 items-start py-2">
      
      <div v-for="option in options" :key="option.value" class="field-radiobutton mt-2">
        <div class="flex items-center">
          <RadioButton
            :inputId="option.value"
            :value="option.value"
            v-model="localSelection" 
            :name="name"
            :disabled="disabled"
          />
          <label :for="option.value" class="ml-2 mr-4 text-base font-medium text-gray-700">{{ option.label }}</label>
        </div>
      </div>
    </div>
    
    <div v-if="localSelection === othersValue" class="mt-3">
      <TextInputField
        type="text"
        v-model="customText"
        :disabled="disabled"
        placeholder="Please specify your choice" 
        class="w-full text-base font-medium text-gray-700" 
      />
    </div>

    <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
  </div>
</template>