<script setup>
import { defineProps, defineEmits, computed } from "vue";
import Drawer from "primevue/drawer";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Divider from "primevue/divider";
import { useClientHelpers } from "@/Constant/useClientHelpers";

const { shortformatDate, getSexLabel, getEducationOptions } =
    useClientHelpers();

const props = defineProps({
    visible: Boolean,
    rowData: Object,
});

const emit = defineEmits(["update:visible"]);

const drawerVisibility = computed({
    get: () => props.visible,
    set: (val) => emit("update:visible", val),
});

// Helper for currency formatting
const formatCurrency = (amount, code) => {
    if (!amount) return null;
    return `${Number(amount).toLocaleString("en-US")} ${code || ""}`;
};

// 1. Organize fields for the UI
const personalInfo = computed(() => [
    { label: "Nickname", value: props.rowData?.nickname },
    { label: "First Name", value: props.rowData?.firstname },
    { label: "Middle Name", value: props.rowData?.middlename },
    { label: "Last Name", value: props.rowData?.lastname },
    { label: "Extension", value: props.rowData?.extensionname },
]);
const demographicFields = computed(() => [
    { label: "Sex", value: getSexLabel(props.rowData?.sex) },
    { label: "Birth Date", value: shortformatDate(props.rowData?.birthdate) },
    { label: "Birth Place", value: props.rowData?.birth_place },
    { label: "Civil Status", value: props.rowData?.civil_status },
    { label: "Religion", value: props.rowData?.religion },
    { label: "Dialect", value: props.rowData?.dialect },

    {
        label: "Birth Registered (Local)",
        value: props.rowData?.birth_registered_with_local,
    },
    {
        label: "Registered Where",
        value: props.rowData?.registered_with_local_where,
    },
    {
        label: "Reason (If not registered)",
        value: props.rowData?.registered_with_local_reason_why,
    },

    {
        label: "Address in PH",
        value:
            [
                props.rowData?.address_in_ph_house_no,
                props.rowData?.address_in_ph_street,
                props.rowData?.address_in_ph_brgy,
                props.rowData?.address_in_ph_city,
                props.rowData?.address_in_ph_province,
                props.rowData?.address_in_ph_region,
            ]
                .filter(Boolean)
                .join(", ") || "N/A",
    },
    { label: "Address in Malaysia", value: props.rowData?.address_in_malaysia },

    {
        label: "Education",
        value: getEducationOptions(props.rowData?.education_attainment),
    },
    { label: "Eligibility", value: props.rowData?.eligibility },
    {
        label: "Date Acquired",
        value: shortformatDate(props.rowData?.eligibility_date_acquired),
    },
    { label: "Skills", value: props.rowData?.skills },

    {
        label: "Estimated Income",
        value:
            [
                props.rowData?.estimated_income_foriegn
                    ? `${Number(
                          props.rowData?.estimated_income_foriegn
                      ).toLocaleString("en-US")} ${
                          props.rowData?.estimated_code_currency || ""
                      }`
                    : null,

                props.rowData?.estimated_income_local
                    ? `${Number(
                          props.rowData?.estimated_income_local
                      ).toLocaleString("en-US")} ${
                          props.rowData?.estimated_code || ""
                      }`
                    : null,
            ]
                .filter(Boolean)
                .join(" | ") || "---",
    },
]);
// 2. Map all categories from all combined records
const categoryHistory = computed(() => {
    if (!props.rowData?.all_category_cases) return [];
    return props.rowData.all_category_cases.map((item) => ({
        category: item.client_category?.category || "N/A",
        assessment: item.client_assessment?.assessment || "No details",
        services: item.client_services?.service_name || "N/A",
        date: shortformatDate(item.created_at),
    }));
});
</script>

<template>
    <Drawer
        v-model:visible="drawerVisibility"
        position="right"
        :style="{ width: '60%' }"
    >
        <template #header>
            <div class="w-full">
                <h1 class="text-[24px] font-bold text-black mb-1">
                    Case Number:
                    {{ rowData?.display_case_no }}
                </h1>
                <Divider />
            </div>
        </template>

        <div v-if="rowData" class="space-y-8 pb-10">
            <div class="space-y-2">
                <div
                    v-for="item in personalInfo"
                    :key="item.label"
                    class="flex items-start font-medium"
                >
                    <span class="text-black w-1/3 min-w-[150px]">
                        {{ item.label }}
                    </span>

                    <span class="text-gray-700">
                        {{ item.value || "---" }}
                    </span>
                </div>
            </div>

            <div class="h-[1px] bg-gray-200 w-full mb-5"></div>

            <div class="space-y-2 mb-10">
                <div
                    v-for="item in demographicFields"
                    :key="item.label"
                    class="flex items-start font-medium"
                >
                    <span class="text-black w-1/3 min-w-[150px]">
                        {{ item.label }}
                    </span>

                    <span class="text-gray-700 flex-1">
                        {{ item.value || "---" }}
                    </span>
                </div>
            </div>

            <Divider />

            <section>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold">All General Intakes</h3>
                    <Tag :value="rowData?.display_case_no" severity="info" />
                </div>
                <DataTable
                    :value="categoryHistory"
                    class="p-datatable-sm shadow-sm"
                    stripedRows
                >
                    <Column field="category" header="Category">
                        <template #body="{ data }">
                            <Tag
                                :value="data.category"
                                severity="secondary"
                                rounded
                            />
                        </template>
                    </Column>
                    <Column field="assessment" header="Length of Stay" />
                    <Column field="date" header="Date Encoded" />
                </DataTable>
            </section>
        </div>
    </Drawer>
</template>
