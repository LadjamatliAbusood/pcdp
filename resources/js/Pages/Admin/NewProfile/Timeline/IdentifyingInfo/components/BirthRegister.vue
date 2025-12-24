<script setup>
import { defineProps, defineEmits } from 'vue';
import SelectButton from 'primevue/selectbutton';
// Define props
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
});

// Emit `update:modelValue` when the value changes
const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <div class="mb-2">
            <label for="location" class="block text-sm font-medium text-gray-700">
                {{ name }}
            </label>
            <!-- Use SelectButton with proper binding -->
            <SelectButton
                :value="modelValue"  
                :options="options" 
                :disabled="disableSelect"  
                optionLabel="label"  
                optionValue="value"
                class="w-full" 
                @change="(e) => emit('update:modelValue', e.value)"  
            />
             <small class="text-sm font-semibold text-red-500" v-if="message">{{
                message
            }}</small>
        </div>
    </div>
</template>
