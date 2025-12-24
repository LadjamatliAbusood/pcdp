<script setup>
import { ref } from "vue";
import Select from "primevue/select";

const model = defineModel({
    type: null,
    required: true,
});

defineProps({
    name: {
        type: String,
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
</script>

<template>
  <div>
    <div class="mb-2">
      <label class="block text-sm font-medium text-gray-700">{{ name }}</label>
      
      <!-- PrimeVue Select expects options prop -->
      <Select
        v-model="model"
        :options="options"
        option-label="label"
        option-value="value"
        class="w-full"
        :class="{ 'border-red-500': message }"
        :disabled="disableSelect"
      />

      <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
    </div>
  </div>
</template>
