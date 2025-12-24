<script setup>
import Timeline from "primevue/timeline";
import 'primeicons/primeicons.css'

defineProps({
  steps: Array,
  active: Number,
});
</script>

<template>

 <Timeline
    :value="steps"
    layout="vertical"
    
  >
    <!-- MARKER -->
    <template #marker="slotProps">
      <span
        class="w-4 h-4 border rounded-full flex items-center justify-center"
        :class="{
          'bg-blue-600 border-blue-600 text-white': slotProps.index < active,  // completed
          'bg-blue-600 border-blue-600': slotProps.index === active,           // active
          'bg-white border-gray-400': slotProps.index > active                 // upcoming
        }"
      >
        <!-- check for completed -->
        <i
          v-if="slotProps.index < active"
          class="pi pi-check-circle"
        ></i>

        <!-- dot for active -->
        <div
          v-else-if="slotProps.index === active"
          class="w-2 h-2 bg-white rounded-full"
        />
      </span>
    </template>

    <!-- CONTENT -->
    <template #content="slotProps">
      <p
        class="font-medium ml-4"
        :class="{
          'text-blue-600': slotProps.index === active,
          'text-gray-700': slotProps.index !== active
        }"
      >
        {{ slotProps.item.label }}
      </p>
    </template>
  </Timeline>
  
 
</template>
