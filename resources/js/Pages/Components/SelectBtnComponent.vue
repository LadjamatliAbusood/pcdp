<script setup>
import { defineProps, defineEmits } from 'vue';
// PrimeVue SelectButton import removed for custom implementation

// Define props
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    // The value received from the parent v-model
    modelValue: {
        type: [String, Number],
        // Allows the component to gracefully handle an unselected state (null)
        default: null, 
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

// Emit `update:modelValue` for v-model compatibility
const emit = defineEmits(['update:modelValue']);


const onSelect = (value) => {
    if (!props.disableSelect) {
        emit('update:modelValue', value);
    }
};
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
                        'flex-1 px-4 py-2 text-sm font-semibold transition-all duration-200 ',
                        
                        
                        // New Button Styling: Individual border and rounded corners
                        'rounded-lg border',
                        
                        // State Styling
                        modelValue === option.value
                            // Active: Vibrant Blue background, white text, and border color matches background
                            ? 'bg-blue-500 text-white border-blue-500' 
                            // Inactive: White background, gray text, and standard border
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50', 
                    ]"
                >
                    {{ option.label }}
                </button>
            </div>
            
            <small class="text-sm font-semibold text-red-500 mt-1 block" v-if="message">{{
                 message
            }}</small>
        </div>
    </div>
</template>