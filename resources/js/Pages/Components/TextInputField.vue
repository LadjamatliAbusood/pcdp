<script setup>
import  InputText  from 'primevue/inputtext'

import Message from 'primevue/message';

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
    readonly: {
        type: Boolean,
        default: false,
    },
        placeholder:{
        type: String,
        default: '',
    },
});
</script>
<template>
    <div>
        <div class="mb-2">
            <!-- <label for="name"
             class="block text-md font-medium text-gray-700"
             >
             {{
                name
            }}</label> -->
            <InputText
            :placeholder="placeholder"
                :type="type"
                v-model="model"
                :readonly="readonly"
                :class="[
                    'font-semibold rounded-lg block w-full',
                    'border-black',
                    $attrs.class,
                ]"
                
            />
            <small class="text-sm font-semibold text-red-500" v-if="message">{{
                message
            }}</small>
            <!-- <Message v-if="message" severity="error" icon="pi pi-times-circle" class="mb-2">{{
                message
            }}</Message> -->
        </div>
    </div>
</template>