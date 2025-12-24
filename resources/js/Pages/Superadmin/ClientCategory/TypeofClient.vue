<script setup>
import { ref, computed, onMounted, watch } from 'vue';
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

const notify = useNotify();
const editingClient = ref(null);
const categories = ref([]);
const totalRecords = ref(0);
const rowsPerPage = ref(3);
const currentPage = ref(1);

// ✨ NEW: State for the live search input
const searchQuery = ref(''); 

const props = defineProps({
  totalRecords: Number,
  rows: Number,
  rowsPerPageOptions: Array,
  currentPage: Number,
});

watch(() => props.currentPage, (newPage) => {
  first.value = (newPage - 1) * props.rows;
});

// Label for form
const label = computed(() =>
  editingClient.value ? 'Update Client' : 'Add Client'
);

// Form
const form = useForm({
  typeofclient: '',
  status: 1,
});


const fetchCategories = async (page = 1, search = searchQuery.value) => {
  try {
    const res = await axios.get('/client-type', {
      params: { 
        page, 
        per_page: rowsPerPage.value,
        search: search 
      },
    });
    categories.value = res.data.data;
    totalRecords.value = res.data.total; 
    currentPage.value = res.data.current_page;
  } catch (err) {
    console.error(err);
  }
};

watch(searchQuery, (newSearchQuery) => {
    fetchCategories(1, newSearchQuery);
});


// Submit
const submit = () => {
  if (editingClient.value) {
    form.put(route('client-type.update', editingClient.value.id), {
      onSuccess: async () => {
        notify.success('Client updated successfully!');
        editingClient.value = null;
        form.reset();
        await fetchCategories(currentPage.value); 
      },
    });
  } else {
    form.post(route('client-type.store'), {
      onSuccess: async () => {
        notify.success('Client created successfully!');
        form.reset();
        await fetchCategories(currentPage.value);
      },
    });
  }
};

// Edit category
const editCategory = (category) => {
  editingClient.value = category;
  form.typeofclient = category.typeofclient;
  form.status = category.status;
};

// Handle page change from reusable paginator
const handlePageChange = (pageData) => {
  // Use the combined fetch function, maintaining the current search query
  fetchCategories(pageData.page, searchQuery.value); 
};

// Fetch on mount
onMounted(() => {
  fetchCategories();
});

</script>

<template>
  <section class="relative">
    <div class="flex flex-col lg:flex-row gap-6">

      <div class="lg:w-auto flex justify-center h-fit"> 
        
        <div class="bg-white p-6 rounded-lg shadow-md max-w-md w-full">
          
          <h3 class="text-sm font-semibold mb-4">{{ label }}</h3>
          <form class="flex flex-col gap-3" @submit.prevent="submit">
            
            <TextInputField
              placeholder="Type of Client"
              v-model="form.typeofclient"
              :message="form.errors.typeofclient"
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

      <div class="bg-white p-4 rounded-md shadow lg:flex-1 w-full">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold">List of Client</h3>
          
          <div class="w-full max-w-xs ml-4">
            <TextInputField
              placeholder="Search...."
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
          <Column field="typeofclient" header="Client" sortable />

          <Column field="status" header="Status" sortable >
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