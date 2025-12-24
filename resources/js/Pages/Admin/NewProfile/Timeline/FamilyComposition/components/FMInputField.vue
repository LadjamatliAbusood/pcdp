<script setup>
import { InputText } from 'primevue';
const model = defineModel({
    type: null,
    required: true,
});
defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "text",
    },
    placeholder:{
        type: String,
        default: '',
    },
    // The error message provided by memberForm.errors.field_name
    message: String, 
    readonly: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <div>
        <div class="mb-2">
            <label :for="name" class="block text-md font-medium text-gray-700">{{
                name
            }}</label>
            <InputText
                :placeholder="placeholder"
                :type="type"
                v-model="model"
                :readonly="readonly"
                :class="[
                    'bg-white border text-gray-900 font-semibold rounded-lg block w-full p-2.5 transition duration-150',
                    // Dynamically set border color based on validation error
                    message ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                    $attrs.class,
                ]"
            />
            <!-- Display the validation error message -->
            <small class="text-md font-semibold text-red-600 mt-1 block" v-if="message">{{
                message
            }}</small>
        </div>
    </div>
</template>