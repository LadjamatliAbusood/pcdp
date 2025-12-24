<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import  Chip  from 'primevue/chip';  // Import the Chip component
import  InputText  from 'primevue/inputtext';  // Import InputText for input field

// Define props
const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  modelValue: {
    type: Array,  // Use Array to bind to the religion[] field in the form
    required: true,
  },
  message: String,
  placeholder: {
    type: String,
    default: '',
  },
  readonly: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);  // Emit the update for v-model binding

// Local state to manage chip input
const inputValue = ref('');  // Stores the input value before adding a chip

// Add chip when comma or enter is pressed
const addChip = () => {
  // Trim any extra spaces or commas
  const value = inputValue.value.trim().replace(/,$/, '');
  if (value && !props.modelValue.includes(value)) {
    props.modelValue.push(value);
    inputValue.value = '';  // Clear the input field
    emit('update:modelValue', [...props.modelValue]);  // Update the parent model
  }
};

// Remove a chip from the list
const removeChip = (chip) => {
  const index = props.modelValue.indexOf(chip);
  if (index !== -1) {
    props.modelValue.splice(index, 1);
    emit('update:modelValue', [...props.modelValue]);  // Update the parent model
  }
};

// Watch for comma or Enter key input to add chip
const onKeyup = (event) => {
  if (event.key === ',' || event.key === 'Enter') {
    addChip();  // Add chip on comma or enter
  }
};

</script>

<template>
  <div class="mb-4">
    <!-- Label for the input field -->
    <label for="name" class="block text-sm font-medium text-gray-700">
      {{ name }}
    </label>

    <!-- Input field for entering chip values -->
    <div class="flex flex-col space-y-1  text-md font-medium text-gray-700">
      <InputText
        v-model="inputValue"
        :placeholder="placeholder"
        :readonly="readonly"
        @keyup="onKeyup"
        class="p-2 border rounded-lg"
      />

      <!-- Display the chips below the input -->
      <div class="flex flex-wrap">
        <Chip
          v-for="(chip, index) in props.modelValue"
          :key="index"
          :label="chip"
          removable
          @remove="removeChip(chip)"
          class="mx-1 my-1"
     
       
        />
      </div>
    </div>

    <!-- Validation error message -->
    <small class="text-sm font-semibold text-red-500" v-if="message">{{ message }}</small>
  </div>
</template>
