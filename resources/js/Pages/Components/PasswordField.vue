
<template>
    <div>
        <div class="mb-2">
            <label for="name" class="block text-md font-medium text-gray-700">{{
                name
            }}</label>
            <Password
                :type="type"
                v-model="model"
                 :feedback="false"
                fluid
                toggleMask 
                class=" text-gray-900 font-semibold rounded-lg block w-full "
            />
            <small v-if="message" class="text-sm font-semibold text-red-500">{{ Array.isArray(message) ? message[0] : message }}</small>
        </div>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import Password from 'primevue/password';

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
    message: String,
   
});


</script>

