<script setup>
import { defineProps, ref, computed } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import ClientRecordsDrawer from './ClientRecordsDrawer.vue';
import { useClientHelpers } from '@/Constant/useClientHelpers';
import { router } from '@inertiajs/vue3';
import TextInputField from '@/Components/TextInputField.vue';
import PaginatorComponent from '@/Components/PaginatorComponent.vue';

const { getSexLabel, formatDate } = useClientHelpers();

const props = defineProps({
    clients: Array,
    
});

// --- STATE ---
const activeIndex = ref('All'); 
const drawerVisible = ref(false);
const selectedClient = ref(null);
const searchQuery = ref('');

// --- COMPUTED: CATEGORY LIST ---
const categories = computed(() => {
    const list = new Set();
    props.clients.forEach(client => {
        client.client_caseno?.forEach(caseno => {
            caseno.category_case?.forEach(catCase => {
                if (catCase.client_category?.category) {
                    list.add(catCase.client_category.category);
                }
            });
        });
    });
    return ['All', ...Array.from(list)];
});


const filteredClients = computed(() => {
    let data = props.clients;


    if (activeIndex.value !== 'All') {
        data = data.filter(client => 
            client.client_caseno?.some(caseno => 
                caseno.category_case?.some(catCase => 
                    catCase.client_category?.category === activeIndex.value
                )
            )
        );
    }

 

    return data;
});

// --- METHODS ---
const onRowClick = (event) => {
    selectedClient.value = event.data;
    drawerVisible.value = true;
};

const getStatusSeverity = (status) => {
    switch (status?.toLowerCase()) {
        case 'success': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'info';
    }
};

const handleFingerprintClick = (row) => {
    const status = row.client_caseno?.[0]?.fingerprint_status;
    if (status === 'Pending') {
        router.visit(route('client.show', row.id));
    }
};
</script>

<template>
    <div class="w-full space-y-4">
        <Tabs v-model:value="activeIndex">
            <TabList>
                <Tab v-for="cat in categories" :key="cat" :value="cat">
                    <span class="flex items-center gap-2 text-sm text-gray-900">

                        {{ cat }}
                    </span>
                </Tab>
            </TabList>

            <TabPanels>
                <TabPanel v-for="cat in categories" :key="cat" :value="cat">
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-100">

                        <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
                            <div>
                                <h2 class="text-base font-bold text-gray-700 capitalize">{{ cat }} Records</h2>
                                <p class="text-sm text-gray-500">Total: {{ filteredClients.length }} items</p>
                            </div>
                            <div class="w-full max-w-xs">
                                <TextInputField placeholder="Search name or case no..." v-model="searchQuery"
                                    type="text" />
                            </div>
                        </div>

                        <DataTable 
                        :value="filteredClients"
                         scrollable 
                         scrollHeight="250px"
                            class="p-datatable-sm text-sm  w-full" 
                            selectionMode="single" 
                            dataKey="id" 
                            @row-click="onRowClick">
                            <Column header="Case No" sortable field="case_no">
                                <template #body="{ data }">
                                    <span>
                                        {{ data.client_caseno?.[0]?.case_no ?? 'N/A' }}
                                    </span>
                                </template>
                            </Column>

                            <Column header="Full Name">
                                <template #body="{ data }">

                                    {{ data.lastname }}, {{ data.firstname }} {{ data.middlename }} {{
                                    data.extensionname }}


                                </template>
                            </Column>

                            <Column header="Gender" sortable field="sex">
                                <template #body="{ data }">
                                    {{ getSexLabel(data.sex) }}
                                </template>
                            </Column>

                            <Column field="created_at" header="Date Encoded" sortable>
                                <template #body="slotProps">
                                    <div v-for="item in slotProps.data.client_caseno" :key="item.id">
                                        {{ formatDate(item.created_at) }}
                                    </div>
                                </template>
                            </Column>

                            <Column header="Category">
                                <template #body="{ data }">
                                    <div class="">
                                        <Tag v-for="caseno in data.client_caseno" :key="caseno.id"
                                            :value="caseno.category_case?.[0]?.client_category?.category ?? 'N/A'"
                                            severity="secondary" rounded />
                                    </div>
                                </template>
                            </Column>

                            <Column header="Fingerprint">
                                <template #body="{ data }">
                                    <Tag :value="data.client_caseno?.[0]?.fingerprint_status ?? 'N/A'"
                                        :severity="getStatusSeverity(data.client_caseno?.[0]?.fingerprint_status)"
                                        class="cursor-pointer" @click.stop="handleFingerprintClick(data)" />
                                </template>
                            </Column>
                        </DataTable>

                        <div class="mt-4">
                            <PaginatorComponent :totalRecords="filteredClients.length" :rows="10" />
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </Tabs>

        <ClientRecordsDrawer v-model:visible="drawerVisible" :client="selectedClient" />
    </div>
</template>

