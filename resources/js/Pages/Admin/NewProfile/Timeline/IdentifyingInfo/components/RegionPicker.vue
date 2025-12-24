<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import Select from 'primevue/select'
import regions from '@/Data/regions.json'

// ---------------- PROPS ----------------
const props = defineProps({
    errors: {
        type: Object,
        default: () => ({
            region: null,
            province: null,
            city: null,
            barangay: null,
        })
    }
})

// ---------------- MODEL ----------------
const model = defineModel()

// ---------------- INITIALIZATION FLAG ----------------
const isInitializing = ref(true)

// ---------------- LOCAL STATE ----------------
const selectedRegion = ref(null)
const selectedProvince = ref(null)
const selectedCity = ref(null)
const selectedBarangay = ref(null)

// ---------------- OPTIONS ----------------
const regionOptions = Object.entries(regions).map(([code, data]) => ({
    code,
    region: data.region,
    province_list: data.province_list
}))

const provinceOptions = computed(() => {
    if (!selectedRegion.value) return []
    const region = regionOptions.find(r => r.code === selectedRegion.value)
    return region?.province_list
        ? Object.keys(region.province_list)
        : []
})

const cityOptions = computed(() => {
    if (!selectedRegion.value || !selectedProvince.value) return []
    const region = regionOptions.find(r => r.code === selectedRegion.value)
    return region?.province_list?.[selectedProvince.value]?.municipality_list
        ? Object.keys(region.province_list[selectedProvince.value].municipality_list)
        : []
})

const barangayOptions = computed(() => {
    if (!selectedRegion.value || !selectedProvince.value || !selectedCity.value) return []
    const region = regionOptions.find(r => r.code === selectedRegion.value)
    return region?.province_list?.[selectedProvince.value]
        ?.municipality_list?.[selectedCity.value]?.barangay_list || []
})

// ---------------- SYNC FROM PARENT (RESTORE / PREFILL) ----------------
watch(
    model,
    (newModel) => {
        if (!newModel?.region) return

        isInitializing.value = true

        selectedRegion.value = newModel.region
        selectedProvince.value = newModel.province
        selectedCity.value = newModel.city
        selectedBarangay.value = newModel.barangay

        nextTick(() => {
            isInitializing.value = false
        })
    },
    { immediate: true }
)

// ---------------- RESET LOGIC (USER CHANGES ONLY) ----------------
watch(selectedRegion, (newVal, oldVal) => {
    if (isInitializing.value) return
    if (newVal !== oldVal) {
        selectedProvince.value = null
        selectedCity.value = null
        selectedBarangay.value = null
    }
})

watch(selectedProvince, (newVal, oldVal) => {
    if (isInitializing.value) return
    if (newVal !== oldVal) {
        selectedCity.value = null
        selectedBarangay.value = null
    }
})

watch(selectedCity, (newVal, oldVal) => {
    if (isInitializing.value) return
    if (newVal !== oldVal) {
        selectedBarangay.value = null
    }
})

// ---------------- EMIT TO PARENT ----------------
watch(
    [selectedRegion, selectedProvince, selectedCity, selectedBarangay],
    () => {
        model.value = {
            region: selectedRegion.value,
            province: selectedProvince.value,
            city: selectedCity.value,
            barangay: selectedBarangay.value,
        }
    },
    { deep: true }
)
</script>

<template>
    <div class="space-y-4 text-md font-medium text-gray-700">

        <!-- REGION -->
        <div>
            <Select
                v-model="selectedRegion"
                :options="regionOptions"
                optionLabel="region"
                optionValue="code"
                placeholder="Region"
                class="w-full"
            />
            <small v-if="errors.region" class="text-red-500 font-semibold">
                {{ errors.region }}
            </small>
        </div>

        <!-- PROVINCE -->
        <div v-if="provinceOptions.length">
            <Select
                v-model="selectedProvince"
                :options="provinceOptions"
                placeholder="Province"
                class="w-full"
            />
            <small v-if="errors.province" class="text-red-500 font-semibold">
                {{ errors.province }}
            </small>
        </div>

        <!-- CITY -->
        <div v-if="cityOptions.length">
            <Select
                v-model="selectedCity"
                :options="cityOptions"
                placeholder="City / Municipality"
                class="w-full"
            />
            <small v-if="errors.city" class="text-red-500 font-semibold">
                {{ errors.city }}
            </small>
        </div>

        <!-- BARANGAY -->
        <div v-if="barangayOptions.length">
            <Select
                v-model="selectedBarangay"
                :options="barangayOptions"
                placeholder="Barangay"
                class="w-full"
            />
            <small v-if="errors.barangay" class="text-red-500 font-semibold">
                {{ errors.barangay }}
            </small>
        </div>

    </div>
</template>
