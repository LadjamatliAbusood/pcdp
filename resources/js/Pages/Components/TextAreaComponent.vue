<script setup>
import  Textarea  from 'primevue/textarea';

// Define your model (use ref for reactivity)
const model = defineModel({
    type: null,
    required: true,
});
// Define props
defineProps({
  name: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'text',
  },
  message: String,
  readonly: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <div>
    <div class="mb-2">
      <label for="name" class="block text-md font-medium text-gray-700">{{ name }}</label>
      <!-- Use InputTextarea instead of InputText -->
      <Textarea
        v-model="model" 
        :placeholder="placeholder"
        :readonly="readonly"
        :rows="5"  
        :class="[
          'bg-white border text-gray-900 font-semibold rounded-lg block w-full p-2.5',
          'border-black',
          $attrs.class
        ]"
      />
      <!-- Display the error message if provided -->
      <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
    </div>
  </div>
</template>
