<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'; 
// Assuming TextInputField.vue is available in the same directory or path
import TextInputField from './TextInputField.vue'; 

// Define props
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    // The value received from the parent v-model
    modelValue: {
        type: [String, Number, null], // Allow null for initial state flexibility
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


const isStandardOption = (value) => {
    return props.options.some(opt => opt.value === value && opt.value !== 'others');
};


const getSelectedButtonValue = () => {
    if (isStandardOption(props.modelValue)) {
        // Highlight a standard option (e.g., Male)
        return props.modelValue;
    }
    
    
    const isCustomText = props.modelValue && props.modelValue !== 'others' && !isStandardOption(props.modelValue); 

   
    if (props.options.some(opt => opt.value === 'others') && (props.modelValue === 'others' || isCustomText)) {
        return 'others';
    }

    return null;
};


const onSelect = (selectedValue) => {
    if (props.disableSelect) return;

    if (selectedValue === 'others') {

        emit('update:modelValue', othersInput.value || 'others'); 
    } else {
        // Standard option:
        othersInput.value = ''; // Clear custom input when a standard option is selected
        
        const newValue = props.modelValue === selectedValue ? null : selectedValue;
        emit('update:modelValue', newValue);
    }
};


watch(othersInput, (newCustomValue) => {

    if (props.modelValue !== newCustomValue && getSelectedButtonValue() === 'others') {
        emit('update:modelValue', newCustomValue);
    }
});

watch(() => props.modelValue, (newValue) => {
   
    const isCustomText = newValue && newValue !== 'others' && !isStandardOption(newValue);
    
    if (isCustomText) {
        othersInput.value = newValue; // Sync the custom text to the local input field
    } else if (newValue === 'others' || !newValue || isStandardOption(newValue)) {

        othersInput.value = '';
    }
}, { immediate: true }); 
</script>

<template>
    <div>
        <div class="mb-2">
          
            
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
                        
                        
                        // New Button Styling: Individual border and rounded corners
                        'rounded-lg border',
                        
                        // State Styling: Use getSelectedButtonValue to determine active state
                        getSelectedButtonValue() === option.value
                            // Active: Vibrant Blue background, white text, and border color matches background
                            ? 'bg-blue-500 text-white border-blue-500' 
                            // Inactive: White background, gray text, and standard border
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50', 
                    ]"
                >
                    {{ option.label }}
                </button>
            </div>

            <!-- Show input if 'others' is the highlighted selection, which covers:
                 1. modelValue === 'others'
                 2. modelValue is custom text
            -->
            <div v-if="getSelectedButtonValue() === 'others'" class="mt-2">
                <TextInputField 
                    v-model="othersInput" 
                    type="text" 
                    placeholder="Please specify"
                    message="" 
                />
            </div>
            
            <!-- Display message if any validation error exists -->
            <small class="text-sm font-semibold text-red-500 mt-1 block" v-if="message">
                {{ message }}
            </small>
        </div>
    </div>
</template>