<script setup>
import { defineProps, defineEmits, ref, computed, watch } from "vue";
import Drawer from "primevue/drawer";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Divider from "primevue/divider";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import CategoryTag from "@/Components/CategoryTag.vue";
import { useClientHelpers } from "@/Constant/useClientHelpers";

const { formatDate, getSexLabel, getEducationOptions } = useClientHelpers();

const props = defineProps({
  visible: Boolean,
  rowData: Object
});

const emit = defineEmits(["update:visible"]);

const selectedIntake = ref(null);
const activeTab = ref("0");

const drawerVisibility = computed({
  get: () => props.visible,
  set: v => emit("update:visible", v)
});

watch(drawerVisibility, v => {
  if (!v) selectedIntake.value = null;
});

// Use selected intake if clicked, otherwise fallback to first case
const activeSource = computed(() => {
  if (selectedIntake.value) return selectedIntake.value;
  return props.rowData?.all_category_cases?.[0] || {};
});

// --- Display fields dynamically ---
const displayFields = computed(() => {
  const s = activeSource.value.client_info || {};

  const fields = [
    { label: 'Nickname', value: s.nickname || '' },
    { label: 'First Name', value: s.firstname || '' },
    { label: 'Middle Name', value: s.middlename || '' },
    { label: 'Last Name', value: s.lastname || '' },
    { label: 'Extension', value: s.extensionname || '' },
    { label: 'Sex', value: s.sex ? getSexLabel(s.sex) : '' },
    { label: 'Birth Date', value: s.birthdate ? formatDate(s.birthdate) : '' },
  ];

  // Insert birth registration fields here, before Birth Place
  if (s.birth_registered_with_local) {
    if (s.birth_registered_with_local.toLowerCase() === 'yes') {
      fields.push({ label: 'Birth Registered At', value: s.registered_with_local_where || '' });
    } else if (s.birth_registered_with_local.toLowerCase() === 'no') {
      fields.push({ label: 'Reason for Non-Registration of Birth', value: s.registered_with_local_reason_why || '' });
    }
  }

  // Then continue with Birth Place and other fields
  fields.push(
    { label: 'Birth Place', value: s.birth_place || '' },
    { label: 'Civil Status', value: s.civil_status || '' },
    { label: 'Religion', value: s.religion || '' },
    { label: 'Dialect Spoken', value: s.dialect || '' },
    
    {
      label: 'Address in Philippines',
      value: [
        s.address_in_ph_house_no,
        s.address_in_ph_street,
        s.address_in_ph_brgy,
        s.address_in_ph_city,
        s.address_in_ph_province,
        s.address_in_ph_region
      ].filter(Boolean).join(', ')
    },
    { label: 'Education Background', value: s.education_attainment ? getEducationOptions(s.education_attainment) : '' },
{ 
  label: 'Eligibility', 
  value: s.eligibility 
    ? `${s.eligibility} ${s.eligibility_date_acquired ? '(' + formatDate(s.eligibility_date_acquired) + ')' : ''}` 
    : '' 
},
   { label: 'Skills', value: s.skills || '' },

   {
   label: 'Estimated Income',
  value: [
    s.estimated_income_foriegn ? `${Number(s.estimated_income_foriegn).toLocaleString()} ${s.estimated_code_currency || ''}` : '',
    s.estimated_income_local ? `${Number(s.estimated_income_local).toLocaleString()} ${s.estimated_code || ''}` : ''
  ].filter(Boolean).join(' | ')
},

  );
  
  

  return fields;
});




const familyFields = computed(() => {
  if (!selectedIntake.value?.family_members?.length) return [];

  return selectedIntake.value.family_members.flatMap((m, index) => [
  
    { label: 'Nickname', value: m.nickname || '' },
    { label: 'First Name', value: m.firstname || '' },
    { label: 'Middle Name', value: m.middlename || '' },
    { label: 'Last Name', value: m.lastname || '' },
    { label: 'Extension', value: m.extensionname || '' },
    { label: 'Sex', value: m.sex ? getSexLabel(m.sex) : '' },
    { label: 'Birth Date', value: m.birthdate ? formatDate(m.birthdate) : '' },
    { label: 'Civil Status', value: m.civil_status || '' },
    { label: 'Relationship', value: m.relationship || '' },
    {
      label: 'Education',
      value: m.education_attainment ? getEducationOptions(m.education_attainment) : ''
    },
    { label: 'Skills / Occupation', value: m.skills_and_occupation || '' },
    { label: 'Health Status', value: m.health_status || '' },
    {
      label: 'Estimated Income',
      value: [
        m.estimated_income_foriegn
          ? `${Number(m.estimated_income_foriegn).toLocaleString()} ${m.estimated_code_currency || ''}`
          : '',
        m.estimated_income_local
          ? `${Number(m.estimated_income_local).toLocaleString()} ${m.estimated_code || ''}`
          : ''
      ].filter(Boolean).join(' | ')
    }
  ]);
});



const serviceFields = computed(() => {
  const s = selectedIntake.value?.services;
  if (!s) return [];

  return [
    {
      label: 'Interventions Needed',
      value: Array.isArray(s.interventions_needed)
        ? s.interventions_needed
        : [s.interventions_needed].filter(Boolean)
    },
    {
      label: 'Referred To',
      value: Array.isArray(s.referred_to)
        ? s.referred_to
        : [s.referred_to].filter(Boolean)
    }
  ];
});
const lengthOptionMap = {
  1: 'Day(s)',
  2: 'Week(s)',
  3: 'Month(s)',
  4: 'Year(s)'
};



const assessmentFields = computed(() => {
  const s = selectedIntake.value?.assessment;
  if (!s) return [];

  const fields = [
    { label: 'Type of Client', value: s.typeofclient || '' },
    { label: 'ID Presented', value: s.id_presented || '' },
    {
  label: 'Length of Stay in Malaysia',
  value: (() => {
    if (!s.length_stay_in_malaysia) return '';

    // Main length + option
    const mainLength = `${s.length_stay_in_malaysia} ${
      lengthOptionMap[s.length_stay_in_malaysia_options] || ''
    }`;

    // Add additional length if present
    const additionalLength =
      s.additional_length_option_if_with_years && s.length_value_if_with_years
        ? ` and ${s.length_value_if_with_years} ${lengthOptionMap[s.additional_length_option_if_with_years]}`
        : '';

    return mainLength + additionalLength;
  })()
},

    { label: 'How did the client go to Malaysia', value: s.client_went_malaysia || '' },
    { label: 'Valid Paper Type', value: s.valid_paper_type || '' },
    { label: 'Employment Status', value: s.client_employeed || '' },
    { label: 'Nature of Work', value: s.nature_of_work || '' },
    { label: 'Position Title', value: s.position_title || '' },
    { label: 'Employer Info', value: s.name_and_address_of_employee || '' },
    { label: 'Client Repatriated', value: s.client_repatriated || '' },
    { label: 'Travel Document No', value: s.travel_document_no || '' },
    { label: 'Issued By', value: s.issued_by || '' },
    { label: 'Date Issued', value: s.date_issued ? formatDate(s.date_issued) : '' },
    { label: 'Passport / IC No', value: s.passport_ic_no || '' },
    { label: 'Client Problem', value: s.client_problem || '' },
    { label: 'Client Plan', value: s.client_plan || '' },
    { label: 'Client Reason', value: s.client_reason || '' },
    { label: 'Client Employment', value: s.client_employment || '' },
    { label: 'Full Name', value: s.contact_person_fullname || '' },
    { label: 'Phone Number', value: s.contact_person_phonenumber || '' },
    { label: ' Relationship', value: s.contact_person_relationship || '' },
  ];

  return fields.filter(f => f.value !== null && f.value !== undefined && f.value !== '');
});




// History
const categoryHistory = computed(() => {
  return (props.rowData?.all_category_cases || [])
    .slice()
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .map(i => ({
      ...i,
      date: formatDate(i.created_at)
    }));
});

const onRowClick = e => {
  selectedIntake.value = e.data;
  activeTab.value = "0";
};
</script>

<template>
    <Drawer v-model:visible="drawerVisibility" position="right" :style="{ width: '50%' }">
        <template #header>
            <div class="w-full">
                <h1 class="text-xl font-bold">
                    {{ selectedIntake ? 'General Intake Info' : 'Case: ' + rowData?.display_case_no }}
                </h1>
                <Divider />
            </div>
        </template>

        <div v-if="rowData" class="space-y-4">
            <!-- Tabs for selected intake -->
            <Tabs v-if="selectedIntake" v-model:value="activeTab">

                <TabList>
                    <Tab value="0">
                        <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === '0'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                            Client Info
                        </span>
                    </Tab>

                    <Tab value="1">
                        <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === '1'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                            Family Background
                        </span>
                    </Tab>
                    <Tab value="2">
                        <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === '2'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                            Assessment
                        </span>
                    </Tab>
                    <Tab value="3">
                        <span :class="[
                        'flex items-center gap-1 text-sm transition-colors',
                        activeIndex === '3'
                            ? 'text-blue-800'
                            : 'text-gray-500 hover:text-gray-700'
                    ]">
                            Recomendation Services and Assistance
                        </span>
                    </Tab>

                </TabList>

                <TabPanels>
                    <TabPanel value="0">
                        <div class="space-y-2 mt-4">
                            <div v-for="item in displayFields" :key="item.label" class="flex border-gray-50 pb-1">
                                <span class="w-1/3 text-gray-900 font-medium ">{{ item.label }}</span>
                                <span class="w-2/3">{{ item.value }}</span>
                            </div>
                        </div>
                    </TabPanel>

                    <TabPanel value="1">
                        <div class="space-y-2 mt-4">
                            <div v-for="item in familyFields" :key="item.label" class="flex pb-1">
                                <span class="w-1/3 font-medium text-gray-900">{{ item.label }}</span>
                                <span>{{ item.value }}</span>
                            </div>
                            <p v-if="!familyFields.length" class="italic text-gray-400">No records.</p>
                        </div>
                    </TabPanel>

                    <TabPanel value="2">
                        <div class="space-y-2 mt-4">
                            <div v-for="item in assessmentFields" :key="item.label" class="grid grid-cols-3 gap-2 pb-1">
                                <!-- Insert H1 above the Contact Person fields -->
                                <template v-if="item.label === 'Full Name'">
                                    <h1 class="text-lg font-bold col-span-3 mb-2 ">Contact Person in the Philippines</h1>
                                </template>

                                <span class="font-medium text-gray-900">{{ item.label }}</span>
                                <span class="col-span-2">{{ item.value }}</span>
                            </div>

                            <p v-if="!assessmentFields.length" class="italic text-gray-400">
                                No additional assessment info available.
                            </p>
                        </div>
                    </TabPanel>





                    <TabPanel value="3">
                        <div v-for="item in serviceFields" :key="item.label" class="flex pb-1">
                            <span class="w-1/3 font-medium text-gray-900">{{ item.label }}</span>

                            <span class="w-2/3">
                                <div v-for="(v, i) in item.value" :key="i">
                                    {{ v }}
                                </div>
                            </span>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <!-- Display client info if no intake selected -->
            <div v-else class="space-y-2">
                <div v-for="item in displayFields" :key="item.label" class="flex">
                    <span class="w-1/3 text-gray-900 font-medium">{{ item.label }}</span>
                    <span class="w-2/3">{{ item.value }}</span>
                </div>
            </div>

            <Divider class="my-6" />

            <section>
                <h3 class="font-bold mb-2">History of Intakes</h3>
                <DataTable :value="categoryHistory" @row-click="onRowClick" selectionMode="single"
                    class="p-datatable-sm">
                    <Column field="category" header="Category">
                        <template #body="{ data }">
                            <CategoryTag :value="data.category" severity="secondary" />
                        </template>
                    </Column>
                    <Column field="stay_duration" header="Stay" />
                    <Column field="date" header="Date" />
                </DataTable>

                <div v-if="selectedIntake" class="mt-4 text-right">
                    <button @click="selectedIntake = null" class="text-blue-600 font-bold hover:underline">← Back to
                        Case Summary</button>
                </div>
            </section>
        </div>
    </Drawer>
</template>
