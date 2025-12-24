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

    family_members: [],

    client_category_id: null,
    typeofclient: null,
    id_presented: null,
    length_stay_in_malaysia: null,
    length_stay_in_malaysia_options: null,
    additional_length_option_if_with_years: null, // weeks or months
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
    if (Object.values(newForm).some(v => v !== null && v !== '' && !(Array.isArray(v) && v.length === 0))) {
        localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify({ ...newForm, attempt: attempt.value }));
    }
}, { deep: true });

onMounted(() => {
    // Restore unfinished form first (always prioritized)
    const savedForm = localStorage.getItem(FORM_STORAGE_KEY);
    if (savedForm) {
        const parsed = JSON.parse(savedForm);
        attempt.value = parsed.attempt || 1;

        Object.keys(parsed).forEach(key => {
            if (key in form) form[key] = parsed[key];
        });

        notify.info('Restored unfinished form');
        return;
    }

    // READ PREFILL FROM URL (from Result.vue)
    const urlParams = new URLSearchParams(window.location.search);
    const prefill = urlParams.get('prefill');
    if (!prefill) return;

    try {
        // Decode the flat data object
        const decoded = JSON.parse(decodeURIComponent(prefill));

        // =================================================================
        // *** CRITICAL FIX: Direct Assignment from FLAT Data ***
        // =================================================================

        // The data is already flattened, so we iterate over the decoded object.
        Object.keys(decoded).forEach(key => {
            if (key in form && decoded[key] !== null) {
                // Ensure date strings are converted to Date objects for date pickers
                if (['birthdate', 'eligibility_date_acquired', 'date_issued'].includes(key) && typeof decoded[key] === 'string') {
                    form[key] = new Date(decoded[key]);
                } else if (key === 'education_attainment') {
                    form[key] = Number(decoded[key]);
                } else {
                    form[key] = decoded[key];
                }
            }
        });

        attempt.value += 1;

        notify.info('Previous client data loaded and ready to edit');
    } catch (e) {
        console.error('Prefill decode failed', e);
        notify.error('Failed to load previous client data.');
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