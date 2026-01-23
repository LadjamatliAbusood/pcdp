<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import TimelineStep from './Timeline/TimelineStep.vue';
import StepAssessment from './Timeline/Assessment/StepAssessment.vue';
import StepFamilyComposition from './Timeline/FamilyComposition/StepFamilyComposition.vue';
import StepIdentifyingInfo from './Timeline/IdentifyingInfo/StepIdentifyingInfo.vue';
import StepRecommendation from './Timeline/Recommendation/StepRecommendation.vue';
import Confirmation from './Timeline/Confirmation/Confirmation.vue';
import axios from 'axios';
import useNotify from '@/Message/Notify';
import useConfirm from '@/Message/Confirm.js';
import Header from './Layout/Header.vue';

defineOptions({ layout: Header });

const notify = useNotify();
const { confirmAlert } = useConfirm();

const attempt = ref(1);
const page = usePage();


// Form object
const form = useForm({
    client_id: null,
    case_no: null, 
    nickname: null,
    firstname: null,
    middlename: null,
    lastname: null,
    extensionname: null,
    sex: null,
    birthdate: null,
    age: null,
    birth_place: null,
    birth_registered_with_local: null,
    registered_with_local_reason_why: null,
    registered_with_local_where: null,
    civil_status: null,
    religion: null,
    dialect: null,
    address_in_ph_region: null,
    address_in_ph_province: null,
    address_in_ph_city: null,
    address_in_ph_brgy: null,
    address_in_ph_street: null,
    address_in_ph_house_no: null,
    address_in_malaysia: null,
    education_attainment: null,
    eligibility: '',
    eligibility_date_acquired: null,
    skills: null,
    estimated_income_foriegn: null,
    estimated_code_currency: 'MYR',
    estimated_income_local: null,
    estimated_code: 'PHP',

    photo_front: null,
    photo_left: null,
    photo_right: null,

    family_members: [],

    client_category_id: null,
    other_category:null,
    //typeofclient: null,
    id_presented: null,
    length_stay_in_malaysia: null,
    length_stay_in_malaysia_options: null,
    additional_length_option_if_with_years: null, 
    length_value_if_with_years: null,
    client_went_malaysia: null,
    valid_paper_type:null,
    client_employeed: null,
    nature_of_work: null,
    position_title: null,
    name_and_address_of_employee: null,
    client_repatriated: null,
    travel_document_no: null,
    issued_by: null,
    date_issued: null,
    passport_ic_no: null,
    client_problem: null,
    client_plan: null,
    client_reason: null,
    client_employment: null,
    contact_person_fullname: null,
    contact_person_phonenumber: null,
    contact_person_relationship: null,
    interventions_needed: [],
    referred_to: 'DSWD',
});

const addFamilyMember = (newMember) => {
    form.family_members.push(newMember);
};

// LocalStorage backup
const FORM_STORAGE_KEY = 'unfinishedClientForm';
watch(form, (newForm) => {
    // 1. Create a deep copy
    const formToSave = JSON.parse(JSON.stringify(newForm));
    
    // 2. Remove File objects from main photos (keep previews if they exist)
    delete formToSave.photo_front;
    delete formToSave.photo_left;
    delete formToSave.photo_right;

    // 3. Clean family members images
    if (formToSave.family_members) {
        formToSave.family_members = formToSave.family_members.map(member => {
            // Remove the actual File objects which can't be stringified
            delete member.fam_img_front;
            delete member.fam_img_left;
            delete member.fam_img_right;
            return member;
        });
    }

    if (Object.values(formToSave).some(v => v !== null && v !== '')) {
        localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify({ ...formToSave, attempt: attempt.value }));
    }
}, { deep: true });

const dataURLtoFile = (dataurl, filename) => {
    if (!dataurl || !dataurl.includes('base64')) return null;
    const arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) u8arr[n] = bstr.charCodeAt(n);
    return new File([u8arr], filename, { type: mime });
};
// Main.vue
onMounted(() => {
    // 1. Priority 1: Check LocalStorage (Draft)
    const savedForm = localStorage.getItem(FORM_STORAGE_KEY);
    if (savedForm) {
        const parsed = JSON.parse(savedForm);
        attempt.value = parsed.attempt || 1;

        // Restore standard fields
        Object.keys(parsed).forEach(key => {
            if (key in form) form[key] = parsed[key];
        });

        // Restore Family Photos from previews
        if (form.family_members) {
            form.family_members.forEach(member => {
                if (member.fam_img_front_preview) 
                    member.fam_img_front = dataURLtoFile(member.fam_img_front_preview, 'front.png');
                if (member.fam_img_left_preview) 
                    member.fam_img_left = dataURLtoFile(member.fam_img_left_preview, 'left.png');
                if (member.fam_img_right_preview) 
                    member.fam_img_right = dataURLtoFile(member.fam_img_right_preview, 'right.png');
            });
        }
        notify.info('Restored unfinished form');
        return; // Exit here if we found a draft
    }

    // 2. Priority 2: Check URL (Only for matching results, usually contains no images)
    const urlParams = new URLSearchParams(window.location.search);
    const prefill = urlParams.get('prefill');
    if (prefill) {
        try {
            const decoded = JSON.parse(decodeURIComponent(prefill));
            Object.keys(decoded).forEach(key => {
                if (key in form && decoded[key] !== null) {
                    // Date and Number formatting logic...
                    form[key] = decoded[key]; 
                }
            });
            notify.info('Previous client data loaded');
        } catch (e) {
            console.error('Prefill failed', e);
        }
    }
});


// Steps and components
const steps = [
    { label: 'Identifying Information' },
    { label: 'Family Composition' },
    { label: 'Assessment' },
    { label: 'Recommended Services and Assistance' },
    { label: 'Confirmation' },
];

const activeStep = ref(0);
const components = [
    
    StepIdentifyingInfo,
    StepFamilyComposition,
    StepAssessment,
    StepRecommendation,
    Confirmation
];

const currentComponent = computed(() => components[activeStep.value]);

// Navigation
const next = () => { if (activeStep.value < steps.length - 1) activeStep.value++; };
const prev = () => { if (activeStep.value > 0) activeStep.value--; };

// Submit form
const submitForm = (event) => {
    confirmAlert({
        target: event?.currentTarget,
        header: "Submit Form?",
        message: `Are you sure all information entered is correct`,
        acceptLabel: "Yes, Submit",
        rejectLabel: "Cancel",
        onAccept: () => {
            // Use 'post' method for submission
            form.post(route('deportees.store'), {
                onSuccess: (response) => {
                    localStorage.removeItem(FORM_STORAGE_KEY);
                    localStorage.removeItem('unfinishedClientFormPhotos')
                    const id = response.props.client.id;
                    notify.success(`Form submitted successfully`);
                    router.visit(route('client.show', id));
                },
                onError: (errors) => {
                    console.error("Final submission failed errors:", errors);
                    notify.error("Final submission failed. Please check the inputs.");
                }
            });
        },
        onReject: () => {
            notify.info("Submission cancelled.");
        }
    });
};


function handleNextStep() {
    if (activeStep.value === steps.length - 1) {
        submitForm();
        return;
    }

if (activeStep.value === 0) {
        if (!form.photo_front ) {
            notify.error('Required: Please capture client photos before proceeding.');
            return; // Stop here, don't even call the server
        }
    }
    form.post(route('validate-step', { step: activeStep.value }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            form.clearErrors();
            next();
        },
        onError: (errors) => {
            console.error("Step validation failed errors:", errors);
            notify.error('Please correct the required fields to proceed.');
        },
    });
}







</script>

<template>
    <section class="flex-1 flex flex-col">
        <Toast />
        <ConfirmPopup />

       <div class="flex flex-col md:flex-row gap-12 mt-4">

  <!-- STICKY TIMELINE -->
  <div class="w-full md:w-auto p-4 md:p-0">
    <div class="md:sticky md:top-6">
      <TimelineStep
        :steps="steps"
        :active="activeStep"
      />
    </div>
  </div>

  <!-- SCROLLABLE CONTENT -->
  <div class="w-full md:flex-1 border-t md:border-t-0 md:border-l px-4 md:px-12 pb-12">
    <component
      :is="currentComponent"
      :form="form"
      @nextStep="handleNextStep"
      :prevStep="prev"
      @add-member="addFamilyMember"
      :isProcessing="form.processing"
    />
  </div>

</div>

    </section>
</template>