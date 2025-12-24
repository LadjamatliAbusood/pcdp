<script setup>
import { defineProps, defineEmits, onMounted, ref, computed, watch } from 'vue';
import TextInputField from '@/Components/TextInputField.vue';
import RadioBtn from '@/Components/RadioBtn.vue';
import SelectBtnComponent from '@/Components/SelectBtnComponent.vue';
import TextAreaComponent from '@/Components/TextAreaComponent.vue';

import DatePickerComponent from '@/Components/DatePickerComponent.vue';
import SelectBtnClient from '@/Components/SelectBtnClient.vue';
import SelectComponent from '@/Components/SelectComponent.vue';
import RadioWithSelect from '@/Components/RadioWithSelect.vue';
import  Button  from 'primevue/button';

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

const LengthEmployeedOptions = [
    { label: 'Days', value: '1' },
    { label: 'Weeks', value: '2' },
    { label: 'Months', value: '3' },
     { label: 'Years', value: '4' },
];
const AdditionalOptions = [
    { label: 'Weeks', value: '2' },
    { label: 'Months', value: '3' },
];
const WenttoMalaysiaOptions = [
    { label: 'With valid papers', value: 'With valid papers' },
    { label: 'Without valid papers (backdoor)', value: 'Without valid papers (backdoor)' },
    { label: 'Born in Malaysia', value: 'Born in Malaysia' },
    { label: 'Victim of Traficking', value: 'Victim of Traficking' },
    { label: 'Victim of Illegal Rectruitment', value: 'Victim of Illegal Rectruitment' },
];

const showRepatriationFields = computed(()=>{
 const selected = props.form.client_went_malaysia;
  return selected === 'With valid papers' || 
           selected === 'Without valid papers (backdoor)' || 
           selected === 'Born in Malaysia';
})

const ValidPaperOptions =[
  {label: 'Used work permit', value: 'Used work permit'},
  { label: 'Used tourist visa', value: 'Used tourist visa' },
]

watch(() => props.form.client_went_malaysia, (newValue) => {
    // If the selection is NOT 'With valid papers', clear the sub-option
    if (newValue !== 'With valid papers') {
        props.form.valid_paper_type = null;
    }
});
watch(showRepatriationFields, (isVisible) => {
    // If fields are now hidden (isVisible is false)
    if (!isVisible) {
        // Access 'form' via 'props.form'
        props.form.client_repatriated = null;
        props.form.travel_document_no = null;
        props.form.issued_by = null;
        props.form.date_issued = null;
        props.form.passport_ic_no = null;
        props.form.valid_paper_type = null;
    }
}, { immediate: true });




const clientEmployeedOptions = [
    { label: 'Yes', value: 'Yes' },
    { label: 'No', value: 'No' },
];

const clientPlan = [
    { label: 'Return to Malaysia', value: 'return_to_malaysia' },
    { label: 'Remain in the Philippines', value: 'remain_in_the_ph' },
];

const EmploymentPH = [
    { label: 'Open Employment', value: 'Open Employment' },
    { label: 'Self Employment', value: 'Self Employment' },
];


const handleEmploymentChange = (newValue) => {
 
    if (newValue === 'No') {
        props.form.nature_of_work = null;
        props.form.position_title = null;
        props.form.name_and_address_of_employee = null;
    }
};

//categories
const categories = ref([]);
onMounted(async()=>{
    try{
        const res = await axios.get('/client-category/categories');
            categories.value = res.data.categories.map(cat => ({
  label: cat.category,
  value: cat.id,
}));
  } catch (error) {
    console.error(error);
  }
    
});



const idpresented = ref([]);
onMounted(async()=>{
    try{
        const res = await axios.get('/client-idpresented/idpresented');
            idpresented.value = res.data.idpresented.map(cat => ({
  label: cat.id_presented,
  value: cat.id_presented,
}));
  } catch (error) {
    console.error(error);
  }
    
});

// const idpresented = ref([]);
// onMounted(async () => {
//     const res = await axios.get('/client-idpresented/idpresented');
//     idpresented.value = res.data.idpresented.map(cat => ({
//         label: cat.id_presented,
//         value: cat.id,
//     }));
// });

const clienttype = ref([]);
onMounted(async()=>{
    try{
        const res = await axios.get('/client-type/clienttype');
            clienttype.value = res.data.clienttype.map(cat => ({
  label: cat.typeofclient,
  value: cat.typeofclient,
}));
  } catch (error) {
    console.error(error);
  }
    
});

</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-xl font-semibold">Assessment Details</h2>
      <p class="text-gray-600 text-sm">Document the client's needs and risks identified during assessment.</p>
    </div>

    <form @submit.prevent>

         <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Type of Client</label>
                <SelectComponent
                v-model="form.typeofclient"
                :options="clienttype"
                :message="form.errors.typeofclient"
                placeholder="Select ID Presented"
                class="col-span-2 w-full"/>
            </div>

           <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Client ID Presented</label>
                <SelectComponent
                v-model="form.id_presented"
                :options="idpresented"
                :message="form.errors.id_presented"
                placeholder="Select ID Presented"
                class="col-span-2 w-full"/>
            </div>


             <div class="grid grid-cols-3 gap-4 items-start py-2">
                <label class="font-medium text-gray-700">Client Category</label>
                <SelectComponent
                v-model="form.client_category_id"
                :options="categories"
                :message="form.errors.client_category_id"
                placeholder="Select Client Category"
                class="col-span-2 w-full"/>
            </div>

      <!-- Stay in malaysia -->
<div class="grid grid-cols-3 gap-4 items-start py-2">
  
  <!-- Label -->
  <label class="font-medium text-gray-700">
    Length of stay in Malaysia
  </label>

  <!-- Input + Select -->
  <div class="col-span-2 flex gap-4 w-full">
    <TextInputField 
      type="number"
      v-model="form.length_stay_in_malaysia" 
      :message="form.errors.length_stay_in_malaysia"
      placeholder="Length of stay"
      class="flex-1"
    />

    <SelectComponent
      v-model="form.length_stay_in_malaysia_options"
      :options="LengthEmployeedOptions"
        :message="form.errors.length_stay_in_malaysia_options"
      placeholder="Length unit"
      class="flex-1"
    />
  </div>

  <!-- CENTERED BUTTON under inputs -->
  <div
    v-if="form.length_stay_in_malaysia_options === '4' && !form.show_extra"
    class="col-span-2 col-start-2 flex justify-center"
  >
    <Button
      
      @click="form.show_extra = true"
      class="bg-blue-700 text-white border-blue-700"
    >
      + Add months or weeks
    </Button>
  </div>

  <!-- CENTERED EXTRA SELECT -->
  <div
    v-if="form.length_stay_in_malaysia_options === '4' && form.show_extra"
    class="col-span-2 col-start-2 flex justify-center gap-4 w-full "
  >

    <TextInputField 
      type="number"
     v-model="form.length_value_if_with_years"
      :message="form.errors.length_value_if_with_years"
      placeholder="Length of stay"
      class="flex-1"
    />
  
    <SelectComponent
      v-model="form.additional_length_option_if_with_years"
      :options="AdditionalOptions"
       :message="form.errors.additional_length_option_if_with_years"
      placeholder="Select months or weeks"
      class="flex-1"
    />
  </div>

</div>


      <!-- Client went to malaysia-->
   <div class="grid grid-cols-3 gap-3 items-start py-2">
    <label class="font-medium text-gray-700">How did the client go to Malaysia?</label>
    <div class="col-span-2">
        
        <RadioWithSelect 
            v-model="form.client_went_malaysia" 
            :options="WenttoMalaysiaOptions"
            :message="form.errors.client_went_malaysia"
            name="malaysia_entry"
        >
            <template #With_valid_papers>
                <div >
                    <SelectBtnClient 
                        v-model="form.valid_paper_type" 
                        :options="ValidPaperOptions"
                        :message="form.errors.valid_paper_type" 
                    />
                </div>
            </template>
        </RadioWithSelect>

    </div>
</div>

      <!-- Client Employeed  -->
      <div class="grid grid-cols-3 gap-3 items-center py-2">
        <label class="font-medium text-gray-700">Was the client employed?</label>
        <div class="col-span-2">

        <SelectBtnClient 
        v-model="form.client_employeed" 
        :options="clientEmployeedOptions"
            @update:modelValue="handleEmploymentChange" 
            :message="form.errors.client_employeed"
            class="col-span-2 w-full" />
      <div v-if="form.client_employeed === 'Yes'">
      <TextInputField 
      v-model="form.nature_of_work" 
      :message="form.errors.nature_of_work" 
      placeholder="Nature of work"
      class="col-span-2 w-full" />
        <TextInputField 
      v-model="form.position_title" 
      :message="form.errors.position_title" 
      placeholder="Position/Title"
      class="col-span-2 w-full" />
      <TextInputField 
      v-model="form.name_and_address_of_employee" 
      :message="form.errors.name_and_address_of_employee" 
      placeholder="Name and Address of employeer"
      class="col-span-2 w-full" />
  


          </div>
        </div>
      </div>



        <div  v-if="showRepatriationFields">
 <!-- Stay in client_repatriated -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Why was the client repatriated to the Philippines?</label>
        <TextInputField v-model="form.client_repatriated" :message="form.errors.client_repatriated"
          class="col-span-2 w-full" />
      </div>

      <!-- Travel Document No. -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Travel Document No.</label>
        <TextInputField  v-model="form.travel_document_no" :message="form.errors.travel_document_no"
          class="col-span-2 w-full" />
      </div>

      <!-- Issued By -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Issued By</label>
        <TextInputField v-model="form.issued_by" :message="form.errors.issued_by" class="col-span-2 w-full" />
      </div>


      <!-- Date Issued -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Date Issued</label>
        <div class="col-span-2">
          <DatePickerComponent v-model="form.date_issued" :message="form.errors.date_issued"
            class="col-span-2 w-full" />
        </div>
      </div>

      <!-- Passport/IC Number -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Passport/IC Number</label>
        <TextInputField v-model="form.passport_ic_no" :message="form.errors.passport_ic_no" class="col-span-2 w-full" />
      </div>

        </div>
     
      <!-- Client Problem -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Client Problem</label>
        <TextAreaComponent v-model="form.client_problem" :message="form.errors.client_problem"
          class="col-span-2 w-full h-18" />
      </div>


      <!-- remain_in_the_ph
      return_to_malaysia -->
        <!-- client_reason:null,
    client_employment:null, -->

      <!-- Client's Plan  -->
      <div class="grid mt-5 grid-cols-3 gap-3 items-start py-2">
        <label class="font-medium text-gray-700">Client's Plan</label>
        <div class="col-span-2">
          <SelectBtnComponent 
          v-model="form.client_plan" 
          :options="clientPlan" 
          :message="form.errors.client_plan"
            class="col-span-2 w-full" />


  

      <div v-if="form.client_plan === 'return_to_malaysia'">
      <TextInputField 
      v-model="form.client_reason" 
      :message="form.errors.client_reason" 
      placeholder="Reason"
      class="col-span-2 w-full" />
          </div>


           <div v-if="form.client_plan === 'remain_in_the_ph'">
      <SelectBtnComponent 
      v-model="form.client_employment" 
      :message="form.errors.client_employment" 
     :options="EmploymentPH" 
      class="col-span-2 w-full" />
          </div>
        </div>
      </div>


      <div>
        <h2 class="text-xl font-semibold my-4">Contact Person in the Philippines</h2>
      </div>



      <!-- Contact person Full Name -->
      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Fullname</label>
        <TextInputField v-model="form.contact_person_fullname" :message="form.errors.contact_person_fullname"
          class="col-span-2 w-full " />
      </div>

      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Phone Number</label>
        <TextInputField type="number" v-model="form.contact_person_phonenumber"
          :message="form.errors.contact_person_phonenumber" class="col-span-2 w-full " />
      </div>

      <div class="grid grid-cols-3 gap-4 items-start py-2">
        <label class="font-medium text-gray-700">Relationship</label>
        <TextInputField v-model="form.contact_person_relationship" :message="form.errors.contact_person_relationship"
          class="col-span-2 w-full" />
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
          <span>Proceed to Recommendation</span>
          <i class="pi pi-arrow-right mx-2"></i>
        </button>
      </div>
    </form>
  </div>
</template>