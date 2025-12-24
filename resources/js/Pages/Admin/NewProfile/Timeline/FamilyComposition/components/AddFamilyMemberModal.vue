<script setup>
import { ref } from "vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import { useForm } from "@inertiajs/vue3";
// Assuming these are locally defined components
import TextInputField from "@/Components/TextInputField.vue";
import SelectBtnComponent from '@/Components/SelectBtnComponent.vue';
import BirthDatePicker from '@/Components/BirthDatePicker.vue'
import SelectwithOthers from "@/Components/SelectwithOthers.vue";
import SelectComponent from "@/Components/SelectComponent.vue";
import RadioBtn from '@/Components/RadioBtn.vue';
import ChipCom from "@/Components/ChipCom.vue";
import CurrencyConvert from "@/Components/CurrencyConvert.vue";
import useNotify from "@/Message/Notify";
import {genderOptions,relationshipOptions,CivilStatusOptions,EducationOptions} from '@/Constant/Choices.js'

const emit = defineEmits(['member-saved']);
const notify = useNotify();
const isVisible = ref(false);


const currentEditingIndex = ref(null);

const memberForm = useForm({
    nickname: null,
    firstname: null,
    middlename: null,
    lastname: null,
    extensionname: null,
    sex: null,
    birthdate: null,
    civil_status: null,
    relationship: null,
    education_attainment: null,
    skills_and_occupation: [],
    estimated_income_foriegn: null,
    estimated_code_currency: 'MYR',
    estimated_income_local: null,
    estimated_code: 'PHP',
    health_status: null,
    fam_img:null,
});


const validateForm = () => {
    let isValid = true;
    const requiredFields = {
        'firstname': 'First Name',
        'lastname': 'Last Name',
        'sex': 'Sex',
        'birthdate': 'Birthdate',
        'civil_status': 'Civil Status',
        'relationship': 'Relationship',
        'education_attainment': 'Educational Attainment',
        'health_status': 'Health Status',
    };
    
    // Clear previous errors first
    memberForm.clearErrors();

    // Check primary required fields
    Object.entries(requiredFields).forEach(([field, label]) => {
        if (!memberForm[field]) {
            memberForm.errors[field] = `The ${label} field is required.`;
          
            isValid = false;
        }
    });

    // Check for array fields (skills_and_occupation)
    if (!memberForm.skills_and_occupation || memberForm.skills_and_occupation.length === 0) {
        memberForm.errors.skills_and_occupation = 'The Skills/Occupation field is required.';
        isValid = false;
    }

    return isValid;
};



const openModal = (initialData = null, index = null) => {
    isVisible.value = true;
    memberForm.reset(); // Always reset form initially to clear previous state

    if (initialData) {
        // EDIT MODE: Populate the form with existing data
        memberForm.defaults(initialData).reset(); // Set defaults and then reset to those defaults
        currentEditingIndex.value = index;
    } else {
        // ADD MODE: Reset the editing index
        currentEditingIndex.value = null;
    }

    memberForm.clearErrors(); // Clear errors
};

// Function to close the modal
const closeModal = () => {
    isVisible.value = false;
    memberForm.reset();
    currentEditingIndex.value = null; // Important: reset index on close
};

// *** MODIFIED Submission logic ***
const saveMember = () => {
    // 1. Run client-side validation
    if (!validateForm()) {
        notify.error('Some fields are required.')

        console.warn('Modal validation failed. Cannot save member.');
        return;
    }
    
    // 2. If valid, emit the member data AND the editing index (which might be null)
    emit('member-saved', { ...memberForm.data() }, currentEditingIndex.value);

    // 3. Close the modal
    closeModal();
};

// Expose openModal for parent to trigger
defineExpose({ openModal });
</script>

<template>
    <div class="card flex justify-center">
         <Toast/>
        <Dialog v-model:visible="isVisible" modal :style="{ width: '50vw' }" @update:visible="closeModal">
            <template #header>
               
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ currentEditingIndex !== null ? 'Edit Family Member' : 'Add Family Member' }}
                    </h2>
                    <p class="text-gray-600 text-sm">Make sure to include only the person who is related to the client.
                    </p>
                </div>
            </template>

            <form @submit.prevent="saveMember" class="space-y-6">

                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Nickname</label>
                    <div class="col-span-2 w-full">
                        <TextInputField v-model="memberForm.nickname" name="Nickname" placeholder="(If applicable)"
                            :message="memberForm.errors.nickname" />
                          
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Person's Name</label>
                    <div class="col-span-2 space-y-3">
                        <TextInputField v-model="memberForm.firstname" name="First Name" placeholder="First Name"
                            :message="memberForm.errors.firstname" />
                        <TextInputField v-model="memberForm.middlename" name="Middle Name"
                            placeholder="Middle Name (If applicable)" :message="memberForm.errors.middlename" />
                        <TextInputField v-model="memberForm.lastname" name="Last Name" placeholder="Last Name"
                            :message="memberForm.errors.lastname" />
                        <TextInputField v-model="memberForm.extensionname" name="Extension Name"
                            placeholder="Extension Name (If applicable)" :message="memberForm.errors.extensionname" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Sex</label>
                    <div class="col-span-2 space-y-3">
                        <SelectBtnComponent v-model="memberForm.sex" :options="genderOptions"
                            :message="memberForm.errors.sex" class=" w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Birthdate</label>
                    <div class="col-span-2 space-y-3">
                        <BirthDatePicker v-model="memberForm.birthdate" :message="memberForm.errors.birthdate"
                            class="col-span-2 w-full" />

                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Civil Status</label>
                    <div class="col-span-2 space-y-3">
                        <SelectwithOthers v-model="memberForm.civil_status" :options="CivilStatusOptions"
                            :message="memberForm.errors.civil_status" class="col-span-2 w-full" />

                    </div>
                </div>


                <div class="grid grid-cols-3 gap-3 items-start py-2">
                    <label class="font-medium text-gray-700">Relationship</label>
                    <div class="col-span-2">
                        <SelectComponent placeholder="Select here.." v-model="memberForm.relationship"
                            :options="relationshipOptions" :message="memberForm.errors.relationship"
                            class="col-span-2 w-full" />
                    </div>
                </div>



                <div class="grid grid-cols-3 gap-3 items-start py-2">
                    <label class="font-medium text-gray-700">Educational Attaintment</label>
                    <div class="col-span-2">
                        <RadioBtn v-model="memberForm.education_attainment" :options="EducationOptions" name="education"
                            :message="memberForm.errors.education_attainment" />
                    </div>
                </div>


                <div class="grid grid-cols-3 gap-3 items-start py-2">
                    <label class="font-medium text-gray-700">Skills/Occupation</label>
                    <div class="col-span-2">
                        <TextInputField v-model="memberForm.skills_and_occupation"
                            :message="memberForm.errors.skills_and_occupation"  />
                    </div>
                </div>


                <div class="grid grid-cols-3 gap-2 items-start py-2">
                    <div class="flex flex-col ">
                        <label class="font-medium text-gray-700 pt-3">Estimated Monthly Income</label>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <TextInputField type="number" v-model="memberForm.estimated_income_foriegn"
                            :message="memberForm.errors.estimated_income_foriegn" 
                            class="w-full" />

                        <TextInputField type="number" v-model="memberForm.estimated_income_local"
                            :message="memberForm.errors.estimated_income_local" 
                            class="w-full" />
                    </div>

                    <div class="flex flex-col space-y-3">
                        <!-- <CurrencyConvert v-model="memberForm.estimated_code_currency" option-label="code"
                            option-value="code" />

                        <CurrencyConvert 
                        v-model="memberForm.estimated_code" 
                        option-label="code"
                         option-value="code"
                            class="mt-2" /> -->

                               
                    <CurrencyConvert disabled
                     v-model="memberForm.estimated_code_currency" 
                     :amount="memberForm.estimated_income_foriegn"
                        to-currency="PHP" 
                        @converted="val => memberForm.estimated_income_local = val" />

                    <CurrencyConvert disabled
                    class="mt-2"
                    v-model="memberForm.estimated_code"  
                    :amount="memberForm.estimated_income_foriegn" />
                
                    </div>


                </div>


                <div class="grid grid-cols-3 gap-4 items-start">
                    <label class="font-medium text-gray-700 pt-3">Health Status</label>
                    <div class="col-span-2 w-full">
                        <TextInputField v-model="memberForm.health_status" :message="memberForm.errors.health_status" />
                    </div>
                </div>


            </form>

            <template #footer>
                <div class="flex justify-between gap-4 pt-6">
                    <!-- <Button label="Cancel" text severity="secondary" @click="closeModal" type="button" class="w-32" /> -->
                    <button
                    class="mt-2 font-medium text-sm px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-150 ease-in-out"
                    @click="closeModal">
                    Close
                </button>
                    <button :class="[
                        'mt-2 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-colors duration-150 ease-in-out flex items-center',
                        ' bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300'
                    ]" @click="saveMember" :disabled="memberForm.processing">
                        <span>{{ currentEditingIndex !== null ? 'Save Changes' : 'Add to the list' }}</span>

                    </button>
                </div>
            </template>
        </Dialog>
    </div>
</template>