<script setup>
import { computed, onMounted, watch, ref } from 'vue'; 
import ChipCom from '@/Components/ChipCom.vue';
import TextInputField from '@/Components/TextInputField.vue';
import BirthDatePicker from '@/Components/BirthDatePicker.vue'
import SelectBtnComponent from '@/Components/SelectBtnComponent.vue';
import SelectwithOthers from '@/Components/SelectwithOthers.vue';
import RegionPicker from './components/RegionPicker.vue';
import RadioBtn from '@/Components/RadioBtn.vue';
import CurrencyConvert from '@/Components/CurrencyConvert.vue';
import DatePickerComponent from '@/Components/DatePickerComponent.vue';
import 'primeicons/primeicons.css'
import axios from 'axios';
import SelectComponent from '@/Components/SelectComponent.vue'
import { genderOptions, BirthRegisterOptions, CivilStatusOptions, EducationOptions } from '@/Constant/Choices.js'
import CaptureDialog from './CaptureDialog.vue';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Image from 'primevue/image';

const FORM_PHOTO_KEY = 'unfinishedClientFormPhotos';

const props = defineProps({
    form: Object,
    prevStep: Function,
});

const showCapture = ref(false);
const photoPreviews = ref({
    photo_front: null,
    photo_left: null,
    photo_right: null
});

const emit = defineEmits(['nextStep']);
const form = props.form;

/* =========================
   WATCH FORM FILES TO CREATE PREVIEWS
========================= */
const dataURLtoFile = (dataurl, filename) => {
    if (!dataurl || typeof dataurl !== 'string') return null;
    let arr = dataurl.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]),
        n = bstr.length,
        u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
}

/* =========================
   LOCAL STORAGE HELPERS
========================= */
watch(
    () => [form.photo_front, form.photo_left, form.photo_right],
    (newVals) => {
        const keys = ['photo_front', 'photo_left', 'photo_right'];
        newVals.forEach((val, index) => {
            const key = keys[index];
            
            // If it's a new file from the camera
            if (val instanceof File) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreviews.value[key] = e.target.result;
                    persistPhotos(); 
                };
                reader.readAsDataURL(val);
            } else if (!val) {
                photoPreviews.value[key] = null;
                persistPhotos();
            }
        });
    },
    { deep: true }
);

const persistPhotos = () => {
    const payload = {};
    ['photo_front', 'photo_left', 'photo_right'].forEach(key => {
        if (photoPreviews.value[key]) payload[key] = photoPreviews.value[key];
    });
    if (Object.keys(payload).length) {
        localStorage.setItem(FORM_PHOTO_KEY, JSON.stringify(payload));
    } else {
        localStorage.removeItem(FORM_PHOTO_KEY);
    }
};

const restorePhotos = () => {
    const saved = localStorage.getItem(FORM_PHOTO_KEY);
    if (!saved) return;

    try {
        const parsed = JSON.parse(saved);
        ['photo_front', 'photo_left', 'photo_right'].forEach(key => {
            if (parsed[key]) {
                // 1. Set the visual preview
                photoPreviews.value[key] = parsed[key];
                // 2. CONVERT TO FILE: This satisfies Laravel's validation
                form[key] = dataURLtoFile(parsed[key], `${key}.jpg`);
            }
        });
    } catch (e) {
        console.error('Failed to restore photos', e);
    }
};

onMounted(() => {
    restorePhotos();
});
/* =========================
   OTHER EXISTING CODE
========================= */
const selectedAddress = computed({
    get() {
        return {
            region: form.address_in_ph_region,
            province: form.address_in_ph_province,
            city: form.address_in_ph_city,
            barangay: form.address_in_ph_brgy,
        };
    },
    set(newVal) {
        form.address_in_ph_region = newVal.region;
        form.address_in_ph_province = newVal.province;
        form.address_in_ph_city = newVal.city;
        form.address_in_ph_brgy = newVal.barangay;
    },
});

const addressErrors = computed(() => ({
    region: form.errors.address_in_ph_region,
    province: form.errors.address_in_ph_province,
    city: form.errors.address_in_ph_city,
    barangay: form.errors.address_in_ph_brgy,
}));

const triggerNextStep = () => {
    emit('nextStep');
};

watch(
    () => form.eligibility,
    (newValue) => {
        if (!newValue) form.eligibility_date_acquired = null;
    }
);
</script>


<template>
    <div class="space-y-6 ">

        <!-- Header -->
        <div>
            <h2 class="text-xl font-semibold">Identifying Information</h2>
            <p class="text-gray-600 text-sm">Basic personal details of the deported individual.</p>
        </div>

        <!-- The form tag's submit handler can be removed or kept as prevent default, 
             but the main action now happens on button click -->
        <form @submit.prevent>


            <!-- Nickname -->
            <!-- <div class="grid grid-cols-3 gap-4 items-start py-2">
    <label class="font-medium text-gray-700">Case no</label>
    <TextInputField :readonly="true" v-model="form.case_no" class="col-span-2 w-full" />
</div> -->


            <!-- Nickname -->
            <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Nickname</label>
                <TextInputField v-model="form.nickname" placeholder="(If applicable)" class="col-span-2 w-full" />
            </div>

            <!-- Client's Name -->
            <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Client’s Name <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2 space-y-3">
                    <TextInputField v-model="form.firstname" placeholder="First Name" class="w-full"
                        :message="form.errors.firstname" />

                    <TextInputField v-model="form.middlename" placeholder="Middle Name (If applicable)" class="w-full"
                        :message="form.errors.middlename" />

                    <TextInputField v-model="form.lastname" placeholder="Last Name" :message="form.errors.lastname"
                        class="w-full" />

                    <TextInputField v-model="form.extensionname" placeholder="Extension Name (If applicable)"
                        class="w-full" :message="form.errors.extensionname" />
                </div>
            </div>


            <!-- Sex -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Sex <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <SelectBtnComponent v-model="form.sex" :options="genderOptions" :message="form.errors.sex"
                        class="col-span-2 w-full" />
                </div>
            </div>

            <!-- Birthdate -->
            <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Birthdate <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <BirthDatePicker v-model="form.birthdate" v-model:age="form.age" :message="form.errors.birthdate"
                        class="col-span-2 w-full" />

                </div>
            </div>

            <!-- Birth Place -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Birth Place <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <TextInputField v-model="form.birth_place" :message="form.errors.birth_place" />


                </div>
            </div>



            <!-- Birth Registered with Local Register? -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Birth Registered with Local Registrar? <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <SelectBtnComponent v-model="form.birth_registered_with_local" :options="BirthRegisterOptions"
                        :message="form.errors.birth_registered_with_local" class="col-span-2 w-full" />




                    <div v-if="form.birth_registered_with_local === 'Yes'">
                        <TextInputField v-model="form.registered_with_local_where"
                            :message="form.errors.registered_with_local_where" placeholder="Where"
                            class="col-span-2 w-full" />
                    </div>
                    <div v-if="form.birth_registered_with_local === 'No'">
                        <TextInputField v-model="form.registered_with_local_reason_why"
                            :message="form.errors.registered_with_local_reason_why" placeholder="Reason Why?"
                            class="col-span-2 w-full" />

                    </div>
                </div>
            </div>


            <!-- Civil Status -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Civil Status <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <SelectwithOthers name="Civil Status" v-model="form.civil_status" :options="CivilStatusOptions"
                        :message="form.errors.civil_status" class="col-span-2 w-full" />
                </div>
            </div>


            <!-- Religion -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Religion <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <TextInputField v-model="form.religion" :message="form.errors.religion" />
                </div>
            </div>


            <!-- Dealect -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Dialect/s <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <TextInputField v-model="form.dialect" :message="form.errors.dialect" />
                </div>
            </div>


            <!-- Address in the Philippines -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Address in the Philippines <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <RegionPicker v-model="selectedAddress" :errors="addressErrors" />


                    <div v-if="selectedAddress.barangay">
                        <TextInputField v-model="form.address_in_ph_street" placeholder="Street (if applicable)"
                            class="col-span-2 w-full mt-4" />

                        <TextInputField v-model="form.address_in_ph_house_no"
                            placeholder="House/Lot/Building No. (if applicable)" class="col-span-2 w-full mt-4" />
                    </div>
                </div>
            </div>

            <!-- Address in Malaysia -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Address in Malaysia </label>
                <div class="col-span-2">
                    <TextInputField v-model="form.address_in_malaysia" :message="form.errors.address_in_malaysia" />
                </div>
            </div>


            <!-- Education Attaintment -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Highest Educational Attaintment <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <RadioBtn v-model="form.education_attainment" :options="EducationOptions" name="education"
                        :message="form.errors.education_attainment" />
                </div>
            </div>

            <!-- Eligibility -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Eligibility </label>
                <div class="col-span-2">
                    <TextInputField v-model="form.eligibility" :message="form.errors.eligibility" />
                    <div v-if="form.eligibility">
                        <div class="col-span-2">
                            <DatePickerComponent v-model="form.eligibility_date_acquired"
                                :message="form.errors.eligibility_date_acquired" class="col-span-2 w-full" />


                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Skills <span class="text-sm font-semibold text-red-500">*</span></label>
                <div class="col-span-2">
                    <TextInputField v-model="form.skills" :message="form.errors.skills" />
                </div>
            </div>

            <!-- Income -->
            <div class="grid grid-cols-3 gap-6 items-start py-2">
                <!-- First Row (Foreign Income) -->
                <div class="flex flex-col space-y-3">
                    <label class="font-medium text-gray-700">Estimated Monthly Income <span class="text-sm font-semibold text-red-500">*</span></label>

                </div>


                <!-- First Row (Foreign Income) -->
                <div class="flex flex-col space-y-3">

                    <TextInputField type="number" v-model="form.estimated_income_foriegn"
                        :message="form.errors.estimated_income_foriegn" />

                    <TextInputField type="number" v-model="form.estimated_income_local"
                        :message="form.errors.estimated_income_local" />

                </div>

                <div class="flex flex-col space-y-3">
                    <CurrencyConvert disabled
                     v-model="form.estimated_code_currency" :amount="form.estimated_income_foriegn"
                        to-currency="PHP" @converted="val => form.estimated_income_local = val" />

                    <CurrencyConvert disabled
                    class="mt-2"
                     v-model="form.estimated_code" :amount="form.estimated_income_foriegn" />
                </div>


            </div>



               <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Capture Image<span class="text-sm font-semibold text-red-500">*</span></label>
       <div class="col-span-2">
    <div v-if="photoPreviews.photo_front || photoPreviews.photo_left || photoPreviews.photo_right" 
         class="flex flex-wrap gap-4 mb-6 p-4 ">
        
        <div v-for="(url, key) in photoPreviews" :key="key">
            <div v-if="url" class="flex flex-col items-center gap-2">
                <Image :src="url" width="140" preview class="rounded shadow-sm overflow-hidden border" />
                <span class="text-[10px] uppercase font-bold text-slate-500">
                    {{ key.split('_')[1] }} View
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <Button 
            type="button"
            :label="photoPreviews.photo_front ? 'Retake Photos' : 'Open Camera'" 
            :icon="photoPreviews.photo_front ? 'pi pi-refresh' : 'pi pi-camera'"
            :severity="photoPreviews.photo_front ? 'secondary' : 'primary'"
            @click="showCapture = true" 
        />
        
        <span v-if="!photoPreviews.photo_front" class="text-sm text-red-500 font-medium">
            * Front photo is required
        </span>
    </div>

    <CaptureDialog 
        v-model:visible="showCapture" 
        :form="form" 
    />
</div>
            </div>


   

         







            <!-- Proceed Button -->
            <div class="flex justify-end gap-4 pt-10 pb-4">
                <div class="col-span-2">

                    <button @click.prevent="triggerNextStep" type="button" :class="[
                        'mt-2 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-colors ',
                        form.processing
                            ? 'bg-gray-400 cursor-not-allowed'
                            : 'bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300',
                        ]" :disabled="form.processing">
                        Proceed to Family Composition
                        <i class="pi pi-arrow-right mx-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>