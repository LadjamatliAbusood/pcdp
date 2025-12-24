<template>
  <div>
    <Paginator
      :first="first"
      :rows="rows"
      :totalRecords="totalRecords"
      @page="onPageChange"
      class="w-full text-gray-700 text-sm"
    >
      <template #start="slotProps">
        <div class="text-gray-700 text-sm">
          Showing {{ slotProps.state.first + 1 }} -
          {{ Math.min(slotProps.state.first + slotProps.state.rows, totalRecords) }}
          of {{ totalRecords }}
        </div>
      </template>
    </Paginator>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Paginator from 'primevue/paginator';

const props = defineProps({
  totalRecords: { type: Number, required: true },
  rows: { type: Number, default: 10 },
  currentPage: { type: Number, default: 1 }
});

const emit = defineEmits(['update:page']);
const first = ref((props.currentPage - 1) * props.rows);

watch(() => props.currentPage, (newPage) => {
  first.value = (newPage - 1) * props.rows;
});

const onPageChange = (event) => {
  first.value = event.first;
  emit('update:page', {
    page: event.page + 1, // Laravel pages are 1-based
    rows: event.rows,
    first: event.first
  });
};
</script>
