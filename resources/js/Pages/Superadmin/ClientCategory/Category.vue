<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { debounce, pickBy } from 'lodash';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import TextInputField from '@/Components/TextInputField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectComponent from '../../Components/SelectComponent.vue';
import InertiaPaginator from '../../Components/InertiaPaginator.vue';
import { StatusOptions } from '@/Constant/Choices';
import Main from '../DataSettings/Main.vue'
import useNotify from '@/Message/Notify';

const notify = useNotify();
const props = defineProps({
    clients: Object, 
    filters: Object,
});

const editingClient = ref(null);
const searchQuery = ref(props.filters?.search ?? ''); // Use null coalescing

const form = useForm({
    category: '',
    status: 1,
});

const label = computed(() => editingClient.value ? 'Update Clients Category' : 'Add Clients Category');

watch(searchQuery, debounce((value) => {
    router.get(window.location.pathname, 
        pickBy({ 
            search: value, 
            per_page: props.clients.per_page // Keep the current limit
        }), 
        { preserveState: true, replace: true }
    );
}, 300));

const editClient = (client) => {
    editingClient.value = client;
    form.category = client.category;
    form.status = client.status;
};
const submit = () => {
    const isEdit = !!editingClient.value;

    const routeName = isEdit
        ? route('client-category.update', editingClient.value.id)
        : route('client-category.store');

    const method = isEdit ? 'put' : 'post';

    form[method](routeName, {
        onSuccess: () => {
            notify.success(
                isEdit
                    ? 'Updated successfully!'
                    : 'Created successfully!'
            );

            editingClient.value = null;
            form.reset();
        },
    });
};
</script>

<template>
  <Main/>
    <section class="relative w-full mt-1">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="bg-white p-6 rounded-lg shadow-md h-fit border border-gray-200">
                <h3 class="text-md font-bold mb-4  text-gray-700">{{ label }}</h3>
                <form @submit.prevent="submit" class="flex flex-col gap-4">
                    <TextInputField
                        placeholder="Client's Category"
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
                    />
                    <Button 
                        v-if="editingClient" 
                        @click="editingClient = null; form.reset()" 
                        severity="danger" 
                        variant="text" 
                        class="w-full text-xs font-bold"
                    >
                        Cancel edit
                    </Button>
                </form>
            </div>

     <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2 border border-gray-200 flex flex-col h-[calc(100vh-120px)]">
    
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 shrink-0">
        <h3 class="text-md font-bold text-gray-700">
            List of Client's Category
        </h3>

        <div class="w-full sm:w-64">
            <TextInputField v-model="searchQuery" placeholder="Search..." />
        </div>
    </div>

    <!-- TABLE (SCROLLABLE AREA) -->
    <div class="flex-1 overflow-auto">
        <DataTable
            :value="clients?.data || []"
           
            class="p-datatable-sm text-sm min-w-[600px]"
             removableSort
                scrollable
                scrollHeight="flex"
        >
            <Column field="category" header="Client's Category">
                <template #body="slotProps">
                    <span class="text-sm text-gray-800">
                        {{ slotProps.data.category }}
                    </span>
                </template>
            </Column>

            <Column field="status" header="Status">
                <template #body="slotProps">
                    <Tag
                        :value="slotProps.data.status == 1 ? 'Active' : 'Deactive'"
                        :severity="slotProps.data.status == 1 ? 'success' : 'danger'"
                        class=" text-sm"
                    />
                </template>
            </Column>

            <Column header="Action" headerStyle="width: 5rem">
                <template #body="slotProps">
                    <Button
                        icon="pi pi-pencil"
                        severity="warn"
                        variant="text"
                        rounded
                        @click="editClient(slotProps.data)"
                    />
                </template>
            </Column>

            <template #empty>
                <div class="text-center p-4 text-gray-400 italic">
                    No data found...
                </div>
            </template>
        </DataTable>
    </div>

    <!-- PAGINATOR -->
    <div class="mt-4 shrink-0">
          <InertiaPaginator :paginator="clients" 
          :filters="{ search: searchQuery }" />
    </div>
</div>


        </div>
    </section>
</template>