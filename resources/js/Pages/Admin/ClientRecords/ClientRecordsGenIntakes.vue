<script setup>
import { defineProps, ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import MultiSelect from "primevue/multiselect";

import TextInputField from "@/Components/TextInputField.vue";
import ClientRecordsDrawer from "./ClientRecordsDrawer.vue";
import ClientNav from "./ClientNav.vue";
import CategoryTag from "@/Components/CategoryTag.vue";
import InertiaPaginator from "@/Components/InertiaPaginator.vue";

import { useClientHelpers } from "@/Constant/useClientHelpers";
import { debounce, pickBy } from "lodash";

const { getSexLabel, formatDate } = useClientHelpers();

const props = defineProps({
    clients: Object,
    filters: Object,
    searchTerm: String,
    categories: Array,
});


const drawerVisible = ref(false);
const selectedRow = ref(null);
const searchQueryGeneral = ref(props.searchTerm);


const selectedCategories = ref(
  Array.isArray(props.filters?.categories) 
    ? props.filters.categories 
    : (props.filters?.categories ? [props.filters.categories] : [])
);


const categoryOptions = computed(() => props.categories ?? []);


const generalRows = computed(() => {
    let rows = (props.clients?.data || [])
        .flatMap(client =>
            client.all_category_cases.map(c => ({
                ...client,
                single_category: c.category,
                json_data: c,
                created_at: c.created_at,
            }))
        );

    
    // if (selectedCategories.value.length) {
    //     rows = rows.filter(r =>
    //         selectedCategories.value.includes(r.single_category)
    //     );
    // }

    return rows.sort(
        (a, b) => new Date(b.created_at) - new Date(a.created_at)
    );
});


const onRowClick = (event) => {
    drawerVisible.value = true;
    selectedRow.value = {
        ...event.data,
        _openIntake: event.data.json_data ?? null,
    };
};


watch([searchQueryGeneral, selectedCategories], debounce(() => {
    const query = {
        search: searchQueryGeneral.value,
        per_page: props.clients.per_page,
        page: 1,
    };

    if (selectedCategories.value.length) {
    query.categories = selectedCategories.value; 
}

    router.get(window.location.pathname, query, {
        preserveState: true,
        replace: true,
    });
}, 300));



</script>




<template>
  <ClientNav />

  <div class="bg-white mt-1 w-full space-y-2">
  <div class="flex flex-col overflow-hidden h-[calc(100vh-120px)]">

    <div class="flex p-1 shrink-0 gap-2 ml-auto">
      <div class="w-1/2 sm:w-64">
        <TextInputField
          v-model="searchQueryGeneral"
          placeholder="Search..."
          class="text-sm w-full h-full"
        />
      </div>

      <div class="w-1/2 sm:w-64">
        <MultiSelect
          v-model="selectedCategories"
          :options="categoryOptions"
          optionLabel="label"
          optionValue="value"
          filter 
          placeholder="Filter by Category"
          class="text-sm w-full"
        />
      </div>
    </div>

    <div class="flex-1 overflow-hidden"> 
      <DataTable
        :value="generalRows"
        @row-click="onRowClick"
        selectionMode="single"
        class="p-datatable-sm text-sm"
        removableSort
        scrollable
        scrollHeight="flex"
      
        tableStyle="min-width: 1000px" 
      
        headerStyle="white-space: nowrap"
      >
        <Column field="display_case_no" header="Case Number" sortable class="whitespace-nowrap"/>

        <Column header="Client's Name">
          <template #body="{ data }">
            <span>
              {{ data.json_data.client_info.lastname }},
              {{ data.json_data.client_info.firstname }}
              {{ data.json_data.client_info.middlename }}
              {{ data.json_data.client_info.extensionname }}
            </span>
          </template>
        </Column>

        <Column header="Sex" class="hidden md:table-cell">
          <template #body="{ data }">
            {{ getSexLabel(data.json_data.client_info.sex) }}
          </template>
        </Column>

        <Column header="Date Encoded" field="created_at" sortable class="whitespace-nowrap">
          <template #body="{ data }">
            <span class="whitespace-nowrap">{{ formatDate(data.created_at) }}</span>
          </template>
        </Column>

        <Column header="Category" class="hidden sm:table-cell whitespace-nowrap" >
          <template #body="{ data }">
            <CategoryTag
              :value="data.single_category"
              severity="secondary"
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

    <div class="shrink-0">
      <InertiaPaginator 
        :paginator="clients" 
        :filters="{ 
          search: searchQueryGeneral,
          categories: selectedCategories
        }" 
      />
    </div>
  </div>

  <ClientRecordsDrawer v-model:visible="drawerVisible" :rowData="selectedRow" />
</div>
</template>





