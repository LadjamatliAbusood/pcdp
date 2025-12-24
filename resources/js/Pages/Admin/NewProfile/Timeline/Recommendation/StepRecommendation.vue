<script setup>
import { defineProps, defineEmits } from 'vue';
import CheckboxComponent from '@/Components/CheckboxComponent.vue';
import RadioBtn from '@/Components/RadioBtn.vue';
import RadioWithInput from '@/Components/RadioWithInput.vue';
const props = defineProps({
    form: Object,
    prevStep: Function,
});

const emit = defineEmits(['nextStep']);

const triggerNextStep = () => {
    emit('nextStep');
};
const triggerPrevStep = () => {
    if (props.prevStep) {
        props.prevStep();
    }
};

const Interventionoptions = [
    { label: 'Food', value: 'Food' },
 
    { label: 'Livelihood', value: 'Livelihood' },
    { label: 'Skills Training', value: 'Skills Training' },
    { label: 'Temporary Shelter', value: 'Temporary Shelter' },
    { label: 'Documentation Services', value: 'Documentation Services' },
    { label: 'Foreign/Local Employement', value: 'Foreign/Local Employementr' },
    { label: 'Capital Assistance', value: 'Capital Assistance' },
    { label: 'Medical Assistance', value: 'Medical Assistance' },
   
    
 { label: 'Others', value: 'others' },
];


const Referedoptions = [
    { label: 'DSWD', value: 'DSWD' },
    { label: 'DOLE', value: 'DOLE' },
    { label: 'TESDA', value: 'TESDA' },
    { label: 'DFA', value: 'DFA' },
    { label: 'BID', value: 'BID' },
    { label: 'NSO', value: 'NSO' },
    { label: 'PDEA', value: 'PDEA' },
    { label: 'OWWA', value: 'OWWA' },
    { label: 'PHILHEALTH', value: 'PHILHEALTH' },
    { label: 'OMA', value: 'OMA' },
    { label: 'NBI', value: 'NBI' },
    { label: 'DOH', value: 'DOH' },
    { label: 'Others', value: 'Others' }
];
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">Recomended Services and Assistance</h2>
            <p class="text-gray-600 text-sm">Suggested support based on the individual's needs.</p>
        </div>

        <form @submit.prevent>
            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Interventions needed</label>
                <div class="col-span-2">
                    <CheckboxComponent
                        v-model="form.interventions_needed" 
                        :options="Interventionoptions"
                        :message="form.errors.interventions_needed" 
                        :name="'interventions'" 
                        class="col-span-2 w-full" 
                    />
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 items-start py-2">
                <label class="font-medium text-gray-700">Referred to</label>
                <div class="col-span-2">
                    <RadioWithInput
                        v-model="form.referred_to"
                        :options="Referedoptions"
                        :message="form.errors.referred_to"
                        :name="'referred'"
                        othersValue="Others"
                        class="col-span-2 w-full"
                    />


                    
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <button
                    class="mt-2 font-medium text-sm px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-150 ease-in-out"
                    @click.prevent="triggerPrevStep">
                    Go Back
                </button>

                <button :class="[
                    'mt-2 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-colors duration-150 ease-in-out flex items-center',
                    form.processing
                        ? 'bg-gray-400 cursor-not-allowed'
                        : 'bg-blue-900 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300']"
                    :disabled="form.processing" @click.prevent="triggerNextStep">
                    <span>Proceed to Assessment</span>
                    <i class="pi pi-arrow-right mx-2"></i>
                </button>
            </div>
        </form>
    </div>
</template>