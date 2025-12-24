<script setup>
import { defineProps, defineEmits, ref, watch, computed } from 'vue'; 

import TextInputField from './TextInputField.vue'; 

// Define props
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
   
    modelValue: {
        type: [String, Number, null], 
        default: null, 
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
});

const emit = defineEmits(['update:modelValue']);


const othersInput = ref(''); 


const othersOptionValue = computed(() => {
   
    const otherOption = props.options.find(
        opt => opt.value === 'others' || opt.value === 100
    );
   
    return otherOption ? otherOption.value : null; 
});



const isStandardOption = (value) => {
    
    return props.options.some(opt => opt.value === value && opt.value !== othersOptionValue.value);
};


const getSelectedButtonValue = () => {
    const otherValue = othersOptionValue.value;

    if (isStandardOption(props.modelValue)) {
     
        return props.modelValue;
    }
    
   
    const isCustomText = props.modelValue && props.modelValue !== otherValue && !isStandardOption(props.modelValue); 

   
    if (otherValue !== null && (props.modelValue === otherValue || isCustomText)) {
        return otherValue;
    }
  
    return null;
};


const onSelect = (selectedValue) => {
    if (props.disableSelect) return;

    const otherValue = othersOptionValue.value;

    if (selectedValue === otherValue) {

        emit('update:modelValue', othersInput.value || otherValue); 
    } else {
       
        othersInput.value = ''; 
        
   
        const newValue = props.modelValue === selectedValue ? null : selectedValue;
        emit('update:modelValue', newValue);
    }
};


watch(othersInput, (newCustomValue) => {
 
    if (props.modelValue !== newCustomValue && getSelectedButtonValue() === othersOptionValue.value) {
        emit('update:modelValue', newCustomValue);
    }
});


watch(() => props.modelValue, (newValue) => {
    const otherValue = othersOptionValue.value;
    
  
    const isCustomText = newValue && newValue !== otherValue && !isStandardOption(newValue);
    
    if (isCustomText) {
        othersInput.value = newValue;
    } else if (newValue === otherValue || !newValue || isStandardOption(newValue)) {
    
        othersInput.value = '';
    }
}, { immediate: true }); 
</script>

<template>
    <div>
        <div class="mb-2">
            <label :for="name" class="block text-sm font-medium text-gray-700">
                {{ name }}
            </label>
            
            <!-- Custom Select Button Group -->
            <div 
                class="flex w-full gap-2" 
                :class="{ 'opacity-60 pointer-events-none': disableSelect }"
            >
                <button
                    v-for="option in options"
                    :key="option.value"
                    :disabled="disableSelect"
                    @click="onSelect(option.value)"
                    type="button"
                    :class="[
                        'flex-1 px-4 py-2 text-sm font-semibold transition-all duration-200',
                        'rounded-lg border',
                        
                       
                        getSelectedButtonValue() === option.value
                            
                             ? 'bg-blue-500 text-white border-blue-500' 
                            // Inactive: White background, gray text, and standard border
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50',  
                    ]"
                >
                    {{ option.label }}
                </button>
            </div>

          
            <small class="text-sm font-semibold text-red-500 mt-1 block" v-if="message">
                {{ message }}
            </small>
        </div>
    </div>
</template>