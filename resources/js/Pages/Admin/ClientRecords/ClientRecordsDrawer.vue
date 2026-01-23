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
import Button from "primevue/button";
import Image from 'primevue/image';
import { dataHealth } from "@/Constant/Choices";
const { formatDate, getSexLabel, getEducationOptions, ClientPlan } = useClientHelpers();


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


const activeSource = computed(() => {
    if (selectedIntake.value) return selectedIntake.value;
    return {
        client_info: props.rowData?.latest_client_info || {}
    };
});


// --- Display fields dynamically ---
const displayFields = computed(() => {
    const s = activeSource.value.client_info || {};

    const fields = [
        {
            label: 'Photos',
            type: 'image',
            value: {
                front: s.photo_front,
                left: s.photo_left,
                right: s.photo_right,
            }
        },
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




const familyMembersData = computed(() => {
    if (!selectedIntake.value?.family_members?.length) return [];

    return selectedIntake.value.family_members.map((m) => ({
        // fam_img: m.fam_img_front,
        
        fields: [
              {
            label: 'Photos',
            type: 'image',
            value: {
                front: m.fam_img_front,
                left: m.fam_img_left,
                right: m.fam_img_right,
            }
        },
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
                label: 'Education Attainment',
                value: m.education_attainment
                    ? getEducationOptions(m.education_attainment)
                    : ''
            },
            { label: 'Skills/Occupation', value: m.skills_and_occupation || '' },
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
            },
            { label: 'Health Status', value: m.health_status || '' },
        ]
    }));
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
    const intake = selectedIntake.value;
 if (!s || !intake) return [];

    const fields = [
       
        { label: 'ID Presented', value: s.id_presented || '' },
         { label: 'Type of Client',value: selectedIntake.value?.category || 'N/A'
        },

        {
            label: 'Length of Stay in Malaysia',
            value: (() => {
                if (!s.length_stay_in_malaysia) return '';

                const main = `${s.length_stay_in_malaysia} ${lengthOptionMap[s.length_stay_in_malaysia_options] || ''
                    }`;

                const extra =
                    s.additional_length_option_if_with_years && s.length_value_if_with_years
                        ? ` and ${s.length_value_if_with_years} ${lengthOptionMap[s.additional_length_option_if_with_years]
                        }`
                        : '';

                return main + extra;
            })()
        },

        { label: 'How did the client go to Malaysia', value: s.client_went_malaysia || '' },
    ];


    if (s.valid_paper_type) {
        fields.push(
            { label: 'Valid Paper Type', value: s.valid_paper_type },
          
        );
    }

    fields.push(
          { label: 'Was the client employed?', value: s.client_employeed || '' },
        { label: 'Nature of Work', value: s.nature_of_work || '' },
        { label: 'Position Title', value: s.position_title || '' },
        { label: 'Employer Info', value: s.name_and_address_of_employee || '' },
        { label: 'Client Repatriated', value: s.client_repatriated || '' },
        { label: 'Travel Document No', value: s.travel_document_no || '' },
        { label: 'Issued By', value: s.issued_by || '' },
        { label: 'Date Issued', value: s.date_issued ? formatDate(s.date_issued) : '' },
        { label: 'Passport / IC No', value: s.passport_ic_no || '' },
        { label: 'Client Problem', value: s.client_problem || '' },
        { label: 'Client Plan', value: ClientPlan(s.client_plan) },
  
            ...(ClientPlan(s.client_plan) === 'Return to Malaysia'
                ? [{ label: 'Client Reason', value: s.client_reason || '' }]
                : ClientPlan(s.client_plan) === 'Remain in the Philippines'
                    ? [{ label: 'Client Employment', value: s.client_employment || '' }]
                    : []
            ),


        { label: 'Full Name', value: s.contact_person_fullname || '' },
        { label: 'Phone Number', value: s.contact_person_phonenumber || '' },
        { label: 'Relationship', value: s.contact_person_relationship || '' }
    );

      return fields;
    // return fields.filter(f => f.value !== null && f.value !== undefined && f.value !== '');

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
const DAYS_IN_WEEK = 7;
const DAYS_IN_MONTH = 30;
const DAYS_IN_YEAR = 365;

// Convert value + option to days
const convertToDays = (value, option) => {
    if (!value) return 0;
    const v = parseInt(value, 10) || 0;
    switch (option) {
        case 1: return v;               // Day(s)
        case 2: return v * DAYS_IN_WEEK; // Week(s)
        case 3: return v * DAYS_IN_MONTH; // Month(s)
        case 4: return v * DAYS_IN_YEAR;  // Year(s)
        default: return 0;
    }
};

// Format total days into Years, Months, Weeks, Days
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


// Compute length of stay for a single intake (row)
const formatStayDuration = (data) => {
    if (!data?.assessment) return '';
    const a = data.assessment;

    const totalDays =
        convertToDays(a.length_stay_in_malaysia, a.length_stay_in_malaysia_options) +
        convertToDays(a.length_value_if_with_years, a.additional_length_option_if_with_years);

    return formatTotalDays(totalDays);
};

// Compute total history summary across all cases

const historySummary = computed(() => {
    const cases = props.rowData?.all_category_cases || [];

    let totalDays = 0;

    cases.forEach(c => {
        const a = c.assessment;
        if (!a) return;


        totalDays += convertToDays(a.length_stay_in_malaysia, a.length_stay_in_malaysia_options);

        if (a.length_value_if_with_years && a.additional_length_option_if_with_years) {
            totalDays += convertToDays(a.length_value_if_with_years, a.additional_length_option_if_with_years);
        }
    });

    return {
        stay: formatTotalDays(totalDays),
        categoryCount: cases.filter(c => c.category).length
    };
});

watch(
    () => props.rowData,
    (row) => {
        if (row?._openIntake) {
            selectedIntake.value = row._openIntake;
            activeTab.value = "0"; // Client Info tab
        } else {
            selectedIntake.value = null;
        }
    },
    { immediate: true }
);


const onRowClick = e => {
    selectedIntake.value = e.data;
    activeTab.value = "0";
};


const getHealthColor = (val) => {
   
    const status = dataHealth.find(h => h.value === val);
   
    return status ? status.color : '#475569'; 
};

const getFamilyImage = (path) => {
    return path
        ? `/storage/${path}`
        : '/images/default-avatar.png';
};

</script>

<template>
    <Drawer v-model:visible="drawerVisibility" position="right" :style="{ width: '50%' }" class="pt-0">
        <template #header>
            <div class="w-full">
                <h1 class="text-xl font-bold">
                    {{ selectedIntake ? 'General Intake Info' : 'Case: ' + rowData?.display_case_no }}
                </h1>
                <Divider class="!mb-0" />
            </div>
        </template>

        <div v-if="rowData" class="space-y-0">
            <Tabs v-if="selectedIntake" v-model:value="activeTab" class="compact-tabs">

                <TabList class="">
                    <Tab value="0" class="!px-3 !py-2">
                        <span :class="[
                            'flex items-center gap-1 text-xs font-medium transition-colors',
                            activeTab === '0'
                                ? 'text-blue-800'
                                : 'text-gray-500 hover:text-gray-700'
                        ]">
                            Client Info
                        </span>
                    </Tab>

                    <Tab value="1" class="!px-3 !py-2">
                        <span :class="[
                            'flex items-center gap-1 text-xs font-medium transition-colors',
                            activeTab === '1'
                                ? 'text-blue-800'
                                : 'text-gray-500 hover:text-gray-700'
                        ]">
                            Family Background
                        </span>
                    </Tab>
                    <Tab value="2" class="!px-3 !py-2">
                        <span :class="[
                            'flex items-center gap-1 text-xs font-medium transition-colors',
                            activeTab === '2'
                                ? 'text-blue-800'
                                : 'text-gray-500 hover:text-gray-700'
                        ]">
                            Assessment
                        </span>
                    </Tab>
                    <Tab value="3" class="!px-3 !py-2">
                        <span :class="[
                            'flex items-center gap-1 text-xs font-medium transition-colors',
                            activeTab === '3'
                                ? 'text-blue-800'
                                : 'text-gray-500 hover:text-gray-700'
                        ]">
                            Services
                        </span>
                    </Tab>

                </TabList>

                <TabPanels class="!p-0">
   <TabPanel value="0">
    <div class="mt-4">
        <div class="flex border-gray-50 pb-1">
            <span class="w-1/3 text-gray-700 font-bold text-sm">Case No.</span>
            <span class="w-2/3 text-sm text-gray-700">
                {{ rowData?.display_case_no }}
            </span>
        </div>

        <div class="flex items-start">
            <div class="flex-1">
                <template v-for="item in displayFields" :key="item.label">
                    <div v-if="item.type !== 'image'" class="flex py-1 items-start">
                        <span class="w-1/2 text-gray-700 font-bold text-sm">
                            {{ item.label }}
                        </span>
                        <span class="w-2/3 text-sm text-gray-700">
                            {{ item.value || 'N/A' }}
                        </span>
                    </div>

                    <Divider 
                        v-if="item.label === 'Sex'" 
                        class="!my-2 -mr-[136px] relative z-10" 
                    />
                </template>
            </div>

            <div class="ml-6 shrink-0 flex flex-col items-center gap-2">
                <template v-for="item in displayFields" :key="item.label">
                    <div v-if="item.type === 'image'" class="flex flex-col items-center gap-2">
                        
                        <div class="relative">
                            <Image
                                v-if="item.value.front"
                                :src="getFamilyImage(item.value.front)"
                                class="w-24 h-24 rounded-md object-cover border border-gray-200 shadow-sm"
                                preview
                            />
                            <div v-else class="w-24 h-24 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[10px] text-center p-2">
                                No Front View
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div>
                                <Image
                                    v-if="item.value.left"
                                    :src="getFamilyImage(item.value.left)"
                                    class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                    preview
                                />
                                <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                    Left
                                </div>
                            </div>

                            <div>
                                <Image
                                    v-if="item.value.right"
                                    :src="getFamilyImage(item.value.right)"
                                    class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                    preview
                                />
                                <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                    Right
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </template>
            </div>
        </div>
    </div>
</TabPanel>

<TabPanel value="1">
    <div class="space-y-4 mt-2">
        
        <div
            v-for="(member, index) in familyMembersData"
            :key="index"
            class="border border-gray-100 rounded-lg p-4 bg-white shadow-sm"
        >
            <div class="flex items-start">
                <div class="flex-1">
                    <template v-for="item in member.fields" :key="item.label">
                        <div v-if="item.type !== 'image'" class="flex py-1 items-start">
                            <span class="w-1/2 text-gray-700 font-bold text-sm">
                                {{ item.label ||  'N/A'}}
                            </span>
                            
                            <div class="w-2/3 flex items-center gap-2">
                                <div
                                    v-if="item.label === 'Health Status' && item.value"
                                    :style="{ backgroundColor: getHealthColor(item.value) }"
                                    class="w-3 h-3 rounded-full shrink-0"
                                ></div>
                                <span class="text-sm text-gray-700">
                                    {{ item.value || 'N/A' }}
                                </span>
                            </div>
                        </div>

                       
                    </template>
                </div>

                <div class="ml-6 shrink-0 flex flex-col items-center gap-2">
                    <template v-for="item in member.fields" :key="item.label">
                        <div v-if="item.type === 'image'" class="flex flex-col items-center gap-2">
                            
                            <div class="relative">
                                <Image
                                    v-if="item.value.front"
                                    :src="getFamilyImage(item.value.front)"
                                    class="w-24 h-24 rounded-md object-cover border border-gray-200 shadow-sm"
                                    preview
                                />
                                <div v-else class="w-24 h-24 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[10px] text-center p-2">
                                    No Front View
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <div>
                                    <Image
                                        v-if="item.value.left"
                                        :src="getFamilyImage(item.value.left)"
                                        class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                        preview
                                    />
                                    <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                        Left
                                    </div>
                                </div>

                                <div>
                                    <Image
                                        v-if="item.value.right"
                                        :src="getFamilyImage(item.value.right)"
                                        class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                        preview
                                    />
                                    <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                        Right
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <p v-if="!familyMembersData.length" class="italic text-gray-400">No records.</p>
    </div>
</TabPanel>

                    <TabPanel value="2">
                        <div class=" mt-4">
                            <div v-for="item in assessmentFields" :key="item.label" class="grid grid-cols-3 gap-2 pb-1">
                                <template v-if="item.label === 'Full Name'">
                                    <h1 class="text-lg font-bold col-span-3 my-2 ">Contact Person in the Philippines
                                    </h1>
                                </template>
                                <span class=" font-bold text-sm text-gray-700">{{ item.label }}</span>
                                <span class="  col-span-2 text-sm">{{ item.value || 'N/A' }}</span>
                            </div>
                            <p v-if="!assessmentFields.length" class="italic text-gray-400">
                                No additional assessment info available.
                            </p>
                        </div>
                    </TabPanel>

                    <TabPanel value="3">
                        <div class="mt-4 space-y-2">
                            <div v-for="item in serviceFields" :key="item.label" class="flex pb-1">
                                <span class="w-1/2 font-bold text-sm text-gray-700">{{ item.label  }}</span>
                                <span class="w-2/3 text-sm">
                                    <div v-for="(v, i) in item.value" :key="i">
                                        {{ v }} 
                                    </div>
                                </span>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>

            <div v-else class="space-y-2">
              <div class="flex items-start">
            <div class="flex-1">
                <template v-for="item in displayFields" :key="item.label">
                    <div v-if="item.type !== 'image'" class="flex py-1 items-start">
                        <span class="w-1/2 text-gray-700 font-bold text-sm">
                            {{ item.label }}
                        </span>
                        <span class="w-2/3 text-sm text-gray-700">
                            {{ item.value || 'N/A' }}
                        </span>
                    </div>

                    <Divider 
                        v-if="item.label === 'Sex'" 
                        class="!my-2 -mr-[136px] relative z-10" 
                    />
                </template>
            </div>

            <div class="ml-6 shrink-0 flex flex-col items-center gap-2">
                <template v-for="item in displayFields" :key="item.label">
                    <div v-if="item.type === 'image'" class="flex flex-col items-center gap-2">
                        
                        <div class="relative">
                            <Image
                                v-if="item.value.front"
                                :src="getFamilyImage(item.value.front)"
                                class="w-24 h-24 rounded-md object-cover border border-gray-200 shadow-sm"
                                preview
                            />
                            <div v-else class="w-24 h-24 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[10px] text-center p-2">
                                No Front View
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div>
                                <Image
                                    v-if="item.value.left"
                                    :src="getFamilyImage(item.value.left)"
                                    class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                    preview
                                />
                                <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                    Left
                                </div>
                            </div>

                            <div>
                                <Image
                                    v-if="item.value.right"
                                    :src="getFamilyImage(item.value.right)"
                                    class="w-[52px] h-12 rounded-md object-cover border border-gray-200 shadow-sm"
                                    preview
                                />
                                <div v-else class="w-[52px] h-12 rounded-md bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-[8px]">
                                    Right
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </template>
            </div>
        </div>

                <Divider class="my-6" />

                <section>
                </section>


               
                <section>
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold">History of Intakes</h3>
                        <span class="text-sm text-gray-600">
                            <strong>Total Stay: </strong> {{ historySummary.stay }}
                            <strong>No of Deportees: </strong>{{ historySummary.categoryCount }}
                        </span>
                    </div>

                    <DataTable :value="categoryHistory" @row-click="onRowClick" selectionMode="single"
                        class="p-datatable-sm text-sm">
                        <Column field="category" header="Category">
                            <template #body="{ data }">
                                <CategoryTag :value="data.category" severity="secondary" />
                            </template>
                        </Column>
                        <Column header="Length of Stay">
                            <template #body="{ data }">
                                <span>{{ formatStayDuration(data) }}</span>
                            </template>
                        </Column>
                        <Column field="date" header="Date Encoded" />
                    </DataTable>
                </section>
            </div>
        </div>

         <div v-if="selectedIntake" class="mt-4 text-right">
                    <Button @click="selectedIntake = null" variant="text">
                        ← Back to Case Summary</Button>
                </div>
    </Drawer>
</template>
