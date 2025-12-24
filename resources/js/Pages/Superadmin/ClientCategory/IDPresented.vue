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
const editingIDPresented = ref(null);
const id_presented = ref([]);
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
  editingIDPresented.value ? 'Update ID Presented' : 'Add ID Presented'
);

// Form
const form = useForm({
  id_presented: '',
  status: 1,
});


const fetchIDPresented = async (page = 1, search = searchQuery.value) => {
  try {
    const res = await axios.get('/client-idpresented', {
      params: { 
        page, 
        per_page: rowsPerPage.value,
        search: search 
      },
    });
    id_presented.value = res.data.data;
    totalRecords.value = res.data.total; 
    currentPage.value = res.data.current_page;
  } catch (err) {
    console.error(err);
  }
};

watch(searchQuery, (newSearchQuery) => {
    fetchIDPresented(1, newSearchQuery);
});


// Submit
const submit = () => {
  if (editingIDPresented.value) {
    form.put(route('client-idpresented.update', editingIDPresented.value.id), {
      onSuccess: async () => {
        notify.success('ID updated successfully!');
        editingIDPresented.value = null;
        form.reset();
        await fetchIDPresented(currentPage.value); 
      },
    });
  } else {
    form.post(route('client-idpresented.store'), {
      onSuccess: async () => {
        notify.success('ID created successfully!');
        form.reset();
        await fetchIDPresented(currentPage.value);
      },
    });
  }
};

// Edit category
const editIDpresented = (id_presented) => {
  editingIDPresented.value = id_presented;
  form.id_presented = id_presented.id_presented;
  form.status = id_presented.status;
};

// Handle page change from reusable paginator
const handlePageChange = (pageData) => {
  // Use the combined fetch function, maintaining the current search query
  fetchIDPresented(pageData.page, searchQuery.value); 
};

// Fetch on mount
onMounted(() => {
  fetchIDPresented();
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
              placeholder="Client ID Presented"
              v-model="form.id_presented"
              :message="form.errors.id_presented"
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
          <h3 class="text-sm font-semibold">List of ID Presented</h3>
          
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
          :value="id_presented"
          class="text-sm"
          emptyMessage="No ID found."
          responsiveLayout="scroll"
         
          size="small"
        >
          <Column field="id_presented" header="ID presented" sortable />

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
                @click.prevent="editIDpresented(data)"
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