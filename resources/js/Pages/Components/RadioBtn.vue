

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
    <div v-for="option in options" :key="option.value" class="field-radiobutton mt-2">
      <RadioButton
        :inputId="option.value"
        :value="option.value"
        v-model="localValue"  
        :name="name"
        :disabled="disabled"
        
      />
      <label :for="option.value" class="ml-2" >{{ option.label }}</label>
    </div>

       <small class="text-sm font-semibold text-red-500" v-if="message">{{
                message
            }}</small>
  </div>
</template>