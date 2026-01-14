<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { debounce, pickBy } from "lodash";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import TextInputField from "@/Components/TextInputField.vue";
import ClientRecordsDrawer from "./ClientRecordsDrawer.vue";
import ClientNav from "./ClientNav.vue";
import InertiaPaginator from "@/Components/InertiaPaginator.vue";
import { useClientHelpers } from "@/Constant/useClientHelpers";

const { getSexLabel } = useClientHelpers();

const props = defineProps({
    clients: Object, 
    filters: Object,
});

const drawerVisible = ref(false);
const selectedRow = ref(null);


const searchQuery = ref(props.filters?.search || '');

watch(searchQuery, debounce((value) => {
    router.get(window.location.pathname,
        pickBy({
            search: value,
            per_page: props.clients.per_page,
            page: 1, 
        }),
        { preserveState: true, replace: true }
    );
}, 300));


const onRowClick = (event) => {
    drawerVisible.value = true;
    selectedRow.value = event.data;
};

// Helpers for Length of Stay
const DAYS_IN_WEEK = 7;
const DAYS_IN_MONTH = 30;
const DAYS_IN_YEAR = 365;

const convertToDays = (value, option) => {
    if (!value) return 0;
    const v = parseInt(value, 10) || 0;
    switch (option) {
        case 1: return v;
        case 2: return v * DAYS_IN_WEEK;
        case 3: return v * DAYS_IN_MONTH;
        case 4: return v * DAYS_IN_YEAR;
        default: return 0;
    }
};

const formatTotalDays = (days) => {
    if (!days) return '0 Day(s)';
    let remaining = days;
    const years = Math.floor(remaining / DAYS_IN_YEAR);
    remaining -= years * DAYS_IN_YEAR;
    const months = Math.floor(remaining / DAYS_IN_MONTH);
    remaining -= months * DAYS_IN_MONTH;
    const weeks = Math.floor(remaining / DAYS_IN_WEEK);
    remaining -= weeks * DAYS_IN_WEEK;

    const parts = [];
    if (years) parts.push(`${years} Year(s)`);
    if (months) parts.push(`${months} Month(s)`);
    if (weeks) parts.push(`${weeks} Week(s)`);
    if (remaining) parts.push(`${remaining} Day(s)`);
    return parts.join(', ');
};

const getTotalStay = (row) => {
    const cases = row.all_category_cases || [];
    let totalDays = 0;

    cases.forEach(c => {
        const a = c.assessment;
        if (!a) return;

        totalDays += convertToDays(a.length_stay_in_malaysia, a.length_stay_in_malaysia_options);

        if (a.length_value_if_with_years && a.additional_length_option_if_with_years) {
            totalDays += convertToDays(a.length_value_if_with_years, a.additional_length_option_if_with_years);
        }
    });

    return formatTotalDays(totalDays);
};

// Table data comes from paginator's .data
const tableRows = computed(() => props.clients?.data || []);

</script>

<template>
  <ClientNav />

  <div class=" bg-white mt-1 w-full space-y-2">
    <div class="flex flex-col overflow-hidden h-[calc(100vh-120px)]">

      <!-- SEARCH -->
      <div class="flex justify-end p-1  shrink-0">
        <div class="w-full sm:w-64">
          <TextInputField
            v-model="searchQuery"
            placeholder="Search..."
          />
        </div>
      </div>

      <!-- TABLE -->
      <div class="flex-1 overflow-x-auto">
        <DataTable
          :value="tableRows"
          @row-click="onRowClick"
          selectionMode="single"
          class="p-datatable-sm text-sm min-w-[800px]"
          removableSort
          scrollable
          scrollHeight="flex"
        >
          <Column field="display_case_no" header="Case Number" sortable />

          <Column header="Client's Name">
            <template #body="{ data }">
              {{ data.latest_client_info?.lastname }},
              {{ data.latest_client_info?.firstname }}
              {{ data.latest_client_info?.middlename }}
              {{ data.latest_client_info?.extensionname }}
            </template>
          </Column>

          <Column header="Sex">
            <template #body="{ data }">
              {{ getSexLabel(data.latest_client_info?.sex) }}
            </template>
          </Column>

          <Column header="No. of Deportation">
            <template #body="{ data }">
              <div class="px-2 py-1">{{ data.category_count }}</div>
            </template>
          </Column>

          <Column header="Length of Stay">
            <template #body="{ data }">
              <span class="px-2 py-1">{{ getTotalStay(data) }}</span>
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
      <div class="shrink-0">
        <InertiaPaginator :paginator="clients" 
          :filters="{ search: searchQuery }" />
      </div>
    </div>

    <ClientRecordsDrawer v-model:visible="drawerVisible" :rowData="selectedRow" />
  </div>
</template>
