<script setup>
import { defineProps, ref, computed } from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import Badge from "primevue/badge";
import TextInputField from "@/Components/TextInputField.vue";
import ClientRecordsDrawer from "./ClientRecordsDrawer.vue";
import { useClientHelpers } from "@/Constant/useClientHelpers";

const { getSexLabel } = useClientHelpers();

const props = defineProps({
    clients: Array,
});

const activeIndex = ref("All");
const drawerVisible = ref(false);
const selectedRow = ref(null);
const searchQuery = ref("");

// GROUPING LOGIC: Merge same Case No into 1 row and count categories
const flattenedRows = computed(() => {
    const grouped = {};

    props.clients.forEach((client) => {
        client.client_caseno?.forEach((caseno) => {
            const key = caseno.case_no;

            if (!grouped[key]) {
                grouped[key] = {
                    ...client,
                    display_case_no: caseno.case_no,
                    category_count: caseno.category_case?.length || 0,
                    // Collect all category objects for the drawer to display
                    all_category_cases: [...(caseno.category_case || [])],
                    // For the Tab filtering
                    all_categories_names:
                        caseno.category_case?.map(
                            (c) => c.client_category?.category
                        ) || [],
                    latest_date: caseno.created_at,
                };
            } else {
                // If same case_no exists, sum the category count
                grouped[key].category_count +=
                    caseno.category_case?.length || 0;

                // Add the category objects to the master list for this case
                if (caseno.category_case) {
                    grouped[key].all_category_cases.push(
                        ...caseno.category_case
                    );

                    // Update the string array for filtering
                    caseno.category_case.forEach((c) => {
                        const name = c.client_category?.category;
                        if (
                            name &&
                            !grouped[key].all_categories_names.includes(name)
                        ) {
                            grouped[key].all_categories_names.push(name);
                        }
                    });
                }
            }
        });
    });

    return Object.values(grouped);
});

const categories = computed(() => {
    const list = new Set();
    flattenedRows.value.forEach((row) => {
        row.all_categories_names.forEach((cat) => list.add(cat));
    });
    return ["All", ...Array.from(list)];
});

const filteredRows = computed(() => {
    let data = flattenedRows.value;

    if (activeIndex.value !== "All") {
        data = data.filter((row) =>
            row.all_categories_names.includes(activeIndex.value)
        );
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter((row) => {
            const fullName = `${row.lastname} ${row.firstname}`.toLowerCase();
            return (
                fullName.includes(query) ||
                row.display_case_no.toLowerCase().includes(query)
            );
        });
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
            <TabList>
                <Tab v-for="cat in categories" :key="cat" :value="cat">{{
                    cat
                }}</Tab>
            </TabList>

            <TabPanels>
                <TabPanel v-for="cat in categories" :key="cat" :value="cat">
                    <div
                        class="bg-white p-4 rounded-lg shadow-md border border-gray-100"
                    >
                        <div class="flex justify-between mb-4 items-center">
                            <h2 class="text-lg font-bold">
                                {{ cat }} Case Records
                            </h2>
                            <div class="w-64">
                                <TextInputField
                                    v-model="searchQuery"
                                    placeholder="Search..."
                                />
                            </div>
                        </div>

                        <DataTable
                            :value="filteredRows"
                            @row-click="onRowClick"
                            selectionMode="single"
                            class="p-datatable-sm text-sm w-full"
                        >
                            <Column
                                field="display_case_no"
                                header="Case Number"
                            />
                            <Column header="Client's Full Name">
                                <template #body="{ data }">
                                    {{ data.lastname }}, {{ data.firstname }}
                                    {{ data.middlename }}
                                </template>
                            </Column>
                            <Column header="Sex">
                                <template #body="{ data }">
                                    {{ getSexLabel(data.sex) }}
                                </template>
                            </Column>
                            <Column header="No. of Deportation">
                                <template #body="{ data }">
                                    {{ data.category_count }}
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </TabPanel>
            </TabPanels>
        </Tabs>

        <ClientRecordsDrawer
            v-model:visible="drawerVisible"
            :rowData="selectedRow"
        />
    </div>
</template>
