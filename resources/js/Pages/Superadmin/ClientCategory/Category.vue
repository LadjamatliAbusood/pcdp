<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import TextInputField from '@/Components/TextInputField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectComponent from '../../Components/SelectComponent.vue';
import { StatusOptions } from '@/Constant/Choices';
import useNotify from '@/Message/Notify';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import PaginatorComponent from '@/Components/PaginatorComponent.vue';
import debounce from 'lodash/debounce';

const notify = useNotify();
const editingCategory = ref(null);
const categories = ref([]);
const totalRecords = ref(0);
const rowsPerPage = ref(4);
const currentPage = ref(1);

// Live search input
const searchQuery = ref(''); 

// Form label
const label = computed(() =>
  editingCategory.value ? 'Update Category' : 'Add Category'
);

// Form
const form = useForm({
  category: '',
  status: 1,
});

// Fetch categories from server
const fetchCategories = async (page = 1, search = '') => {
  try {
    const res = await axios.get('/client-category', {
      params: { 
        page, 
        per_page: rowsPerPage.value,
        search
      },
    });
    categories.value = res.data.data;
    totalRecords.value = res.data.total;
    currentPage.value = res.data.current_page;
  } catch (err) {
    console.error(err);
  }
};

// Debounced search
const debouncedFetch = debounce(() => fetchCategories(1, searchQuery.value), 300);
watch(searchQuery, debouncedFetch);

// Submit form
const submit = () => {
  if (editingCategory.value) {
    form.put(route('client-category.update', editingCategory.value.id), {
      onSuccess: async () => {
        notify.success('Category updated successfully!');
        editingCategory.value = null;
        form.reset();
        await fetchCategories(currentPage.value, searchQuery.value); 
      },
    });
  } else {
    form.post(route('client-category.store'), {
      onSuccess: async () => {
        notify.success('Category created successfully!');
        form.reset();
        await fetchCategories(currentPage.value, searchQuery.value);
      },
    });
  }
};

// Edit category
const editCategory = (category) => {
  editingCategory.value = category;
  form.category = category.category;
  form.status = category.status;
};

// Handle page change
const handlePageChange = ({ page, rows }) => {
  rowsPerPage.value = rows;
  fetchCategories(page, searchQuery.value);
};

// Fetch on mount
onMounted(() => fetchCategories());

</script>

<template>
<section class="relative">
  <div class="flex flex-col lg:flex-row gap-6">

    <!-- Form -->
    <div class="lg:w-auto flex justify-center h-fit">
      <div class="bg-white p-6 rounded-lg shadow-md max-w-md w-full">
        <h3 class="text-sm font-semibold mb-4">{{ label }}</h3>
        <form class="flex flex-col gap-3" @submit.prevent="submit">
          <TextInputField
            placeholder="Client Category"
            v-model="form.category"
            :message="form.errors.category"
          />
          <SelectComponent
            v-model="form.status"
            :options="StatusOptions"
            :message="form.errors.status"
          />
          <PrimaryButton
            :label="label"
            :disabled="form.processing"
            :processing="form.processing"
            class="mt-1"
          />
        </form>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white p-4 rounded-md shadow lg:flex-1 w-full">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold">List of Categories</h3>
        <div class="w-full max-w-xs ml-4">
          <TextInputField
            placeholder="Search..."
            v-model="searchQuery"
            type="text"
          />
        </div>
      </div>

      <DataTable
        scrollable
        :value="categories"
        class="text-sm"
        emptyMessage="No categories found."
        responsiveLayout="scroll"
        size="small"
      >
        <Column field="category" header="Category" sortable />
        <Column field="status" header="Status" sortable>
          <template #body="{ data }">
            <span
              class="px-2 py-0.5 rounded-full text-xs font-medium"
              :class="{
                'bg-green-100 text-green-700': data.status === 1,
                'bg-red-100 text-red-700': data.status === 2
              }"
            >
              {{ data.status === 1 ? 'Active' : 'Deactive' }}
            </span>
          </template>
        </Column>
        <Column header="Action">
          <template #body="{ data }">
            <Button
              icon="pi pi-pencil"
              severity="warn"
              variant="text"
              @click.prevent="editCategory(data)"
            />
          </template>
        </Column>
      </DataTable>

      <div class="mt-3">
        <PaginatorComponent
          :totalRecords="totalRecords"
          :rows="rowsPerPage"
          @update:page="handlePageChange"
        />
      </div>
    </div>
  </div>
</section>
</template>
