<script setup>
import { defineProps, ref, computed } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import TextInputField from "@/Components/TextInputField.vue";
import ClientRecordsDrawer from "./ClientRecordsDrawer.vue";
import { useClientHelpers } from "@/Constant/useClientHelpers";
import CategoryTag from "@/Components/CategoryTag.vue";

const { getSexLabel,formatDate } = useClientHelpers();

const props = defineProps({
    clients: Array,
});

// ONLY 2 TAB VALUES
const activeIndex = ref("client");
const drawerVisible = ref(false);
const selectedRow = ref(null);
const searchQuery = ref("");

const generalRows = computed(() =>
    props.clients.flatMap(client =>
        client.all_category_cases.map(c => ({
            ...client,
            single_category: c.category,
            json_data: c,       // Full JSON data for drawer
            created_at: c.created_at
        }))
    ).sort((a,b)=>new Date(b.created_at) - new Date(a.created_at))
);

const filteredRows = computed(() => {
    let data = props.clients;
    if(activeIndex.value==='general') data = data.filter(row=>row.all_categories_names.includes('General Intake'));

    if(searchQuery.value){
        const q = searchQuery.value.toLowerCase();
        data = data.filter(row => 
            `${row.raw_client_data.lastname} ${row.raw_client_data.firstname}`.toLowerCase().includes(q) ||
            row.display_case_no.toLowerCase().includes(q)
        );
    }
    return data;
});

const onRowClick = (event) => {
    selectedRow.value = event.data;
    drawerVisible.value = true;
};



</script>

<template>
    <div class="w-full space-y-4">
        <Tabs v-model:value="activeIndex">

            <!-- TAB LIST (ONLY 2 TABS) -->
            <TabList>
                <Tab value="client">
                    <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === 'client'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                        All Client
                    </span>
                </Tab>

                <Tab value="general">
                    <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === 'general'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                        All General Intakes
                    </span>
                </Tab>

            </TabList>

            <!-- TAB PANELS -->
            <TabPanels>
                <TabPanel value="client">
                    <div class="">
                        <div class="flex ">
                            <div class="ml-auto w-64">
                                <TextInputField v-model="searchQuery" placeholder="Search..." />
                            </div>
                        </div>

                       <DataTable 
    :value="filteredRows" 
    @row-click="onRowClick" 
    selectionMode="single"
    class="p-datatable-sm text-sm w-full"
>
    <Column field="display_case_no" header="Case Number" />

    <Column header="Client's Name">
        <template #body="{ data }">
            <div class="px-2 py-1">
                {{ data.raw_client_data.lastname }}, {{ data.raw_client_data.firstname }} {{ data.raw_client_data.middlename }}
                {{ data.raw_client_data.extensionname }}
            </div>
        </template>
    </Column>

    <Column header="Sex">
        <template #body="{ data }">
            <div class="px-2 py-1">
                {{ getSexLabel(data.raw_client_data.sex) }}
            </div>
        </template>
    </Column>

    <Column header="No. of Deportation">
        <template #body="{ data }">
            <div class="px-2 py-1">
                {{ data.category_count }}
            </div>
        </template>
    </Column>

   
</DataTable>

                    </div>
                </TabPanel>








<TabPanel value="general">
    <div class="flex flex-col overflow-hidden h-auto lg:h-[440px]">

        <!-- SEARCH -->
        <div class="flex justify-end mb-2 shrink-0">
            <div class="w-full sm:w-64">
                <TextInputField
                    v-model="searchQuery"
                    placeholder="Search..."
                />
            </div>
        </div>

        <!-- TABLE WRAPPER (HORIZONTAL SCROLL ON MOBILE) -->
        <div class="flex-1 overflow-x-auto">
            <DataTable
                :value="generalRows"
                @row-click="onRowClick"
                selectionMode="single"
                class="p-datatable-sm text-sm min-w-[800px]"
                paginator
                :rows="10"
                :rowsPerPageOptions="[10, 20, 30, 50]"
                paginatorTemplate="RowsPerPageDropdown CurrentPageReport PrevPageLink PageLinks NextPageLink"
                currentPageReportTemplate="Showing {first}–{last} of {totalRecords}"
                removableSort
                scrollable
                scrollHeight="flex"
            >
                <Column field="display_case_no" header="Case Number" sortable />

                <Column header="Client's Name">
                    <template #body="{ data }">
                        {{ data.raw_client_data.lastname }}, {{ data.raw_client_data.firstname }} {{ data.raw_client_data.middlename }}
                {{ data.raw_client_data.extensionname }}
                    </template>
                </Column>

                <!-- HIDE ON SMALL -->
                <Column header="Sex" class="hidden md:table-cell">
                    <template #body="{ data }">
                           {{ getSexLabel(data.raw_client_data.sex) }}
                    </template>
                </Column>

                <Column header="Date Encoded" sortable field="created_at">
        <template #body="{ data }">
            <div class="px-2 py-1">
                  {{ formatDate(data.created_at) }}
            </div>
        </template>
    </Column>

                <!-- HIDE ON VERY SMALL -->
                <Column header="Category" class="hidden sm:table-cell">
                    <template #body="{ data }">
                        <CategoryTag
                            :value="data.single_category"
                            severity="secondary"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>

    </div>
</TabPanel>


            </TabPanels>
        </Tabs>

        <ClientRecordsDrawer v-model:visible="drawerVisible" :rowData="selectedRow" />
    </div>
</template>
