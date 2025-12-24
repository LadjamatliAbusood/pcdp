<!-- <script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    form: Object, 
    prevStep: Function,
    finalData: Object, 
});

  


const emit = defineEmits(['nextStep']); 


const triggerFinalSubmit = () => {
 
    emit('nextStep'); 
};



</script>

<template>
    <div class="space-y-8 p-4 rounded-lg shadow-inner">
        <div>
            <h2 class="text-xl font-semibold">Confirmation</h2>
            <p class="text-gray-600 text-sm">Make sure all data is correct before saving.</p>
        </div>

          <div>
 
    <div class="section">
      <h2>Identifying Information</h2>
      <div><strong>Nickname:</strong> {{ form.nickname || 'N/A' }}</div>
      <div><strong>First Name:</strong> {{ form.firstname }}</div>
      <div><strong>Middle Name:</strong> {{ form.middlename }}</div>
      <div><strong>Last Name:</strong> {{ form.lastname }}</div>
      <div><strong>Extension Name:</strong> {{ form.extensionname || 'N/A' }}</div>
      <div><strong>Sex:</strong> {{ form.sex === 1 ? 'Male' : form.sex === 2 ? 'Female' : 'N/A' }}</div>
      <div><strong>Birthdate:</strong> {{ formatDate(form.birthdate) }}</div>
      <div><strong>Birth Place:</strong> {{ form.birth_place }}</div>
      <div><strong>Birth Registered With Local:</strong> {{ form.birth_registered_with_local }}</div>
      <div><strong>Registered With Local Reason:</strong> {{ form.registered_with_local_reason_why || 'N/A' }}</div>
      <div><strong>Registered With Local Where:</strong> {{ form.registered_with_local_where }}</div>
      <div><strong>Civil Status:</strong> {{ form.civil_status }}</div>
      <div><strong>Religion:</strong> {{ form.religion }}</div>
      <div><strong>Dialect:</strong> {{ form.dialect.join(', ') }}</div>
    </div>


    <div class="section">
      <h2>Address Information</h2>
      <div><strong>Region:</strong> {{ form.address_in_ph_region }}</div>
      <div><strong>Province:</strong> {{ form.address_in_ph_province }}</div>
      <div><strong>City:</strong> {{ form.address_in_ph_city }}</div>
      <div><strong>Barangay:</strong> {{ form.address_in_ph_brgy }}</div>
      <div><strong>Street:</strong> {{ form.address_in_ph_street }}</div>
      <div><strong>House No:</strong> {{ form.address_in_ph_house_no }}</div>
      <div><strong>Address in Malaysia:</strong> {{ form.address_in_malaysia }}</div>
    </div>

    <div class="section">
      <h2>Employment and Travel Information</h2>
      <div><strong>Education Attainment:</strong> {{ form.education_attainment }}</div>
      <div><strong>Eligibility:</strong> {{ form.eligibility }}</div>
      <div><strong>Eligibility Date Acquired:</strong> {{ formatDate(form.eligibility_date_acquired) }}</div>
      <div><strong>Skills:</strong> {{ form.skills.join(', ') }}</div>
      <div><strong>Estimated Income (Foreign):</strong> {{ form.estimated_income_foriegn }} {{ form.estimated_code_currency }}</div>
      <div><strong>Estimated Income (Local):</strong> {{ form.estimated_income_local }} {{ form.estimated_code }}</div>
      <div><strong>Family Members:</strong> {{ form.family_members.length ? form.family_members.join(', ') : 'N/A' }}</div>
      <div><strong>Length of Stay in Malaysia:</strong> {{ form.length_stay_in_malaysia }}</div>
      <div><strong>Went to Malaysia:</strong> {{ form.client_went_malaysia }}</div>
      <div><strong>Employed:</strong> {{ form.client_employeed }}</div>
      <div><strong>Nature of Work:</strong> {{ form.nature_of_work }}</div>
      <div><strong>Position Title:</strong> {{ form.position_title }}</div>
      <div><strong>Name and Address of Employer:</strong> {{ form.name_and_address_of_employee }}</div>
      <div><strong>Client Repatriated:</strong> {{ form.client_repatriated }}</div>
      <div><strong>Travel Document No:</strong> {{ form.travel_document_no }}</div>
      <div><strong>Issued By:</strong> {{ form.issued_by }}</div>
      <div><strong>Date Issued:</strong> {{ formatDate(form.date_issued) }}</div>
      <div><strong>Passport/IC No:</strong> {{ form.passport_ic_no }}</div>
      <div><strong>Client Problem:</strong> {{ form.client_problem }}</div>
      <div><strong>Client Plan:</strong> {{ form.client_plan }}</div>
      <div><strong>Client Reason:</strong> {{ form.client_reason || 'N/A' }}</div>
      <div><strong>Client Employment:</strong> {{ form.client_employment }}</div>
    </div>

 
    <div class="section">
      <h2>Contact and Recommendations</h2>
      <div><strong>Contact Person Fullname:</strong> {{ form.contact_person_fullname }}</div>
      <div><strong>Contact Person Phone:</strong> {{ form.contact_person_phonenumber }}</div>
      <div><strong>Contact Person Relationship:</strong> {{ form.contact_person_relationship }}</div>
      <div><strong>Interventions Needed:</strong> {{ form.interventions_needed.join(', ') }}</div>
      <div><strong>Referred To:</strong> {{ form.referred_to.join(', ') }}</div>
    </div>
  </div>
        
       

        <div >
        
            
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <button @click="prevStep" class="px-4 py-2 text-sm text-gray-600 border rounded hover:bg-gray-100">
                    <i class="pi pi-arrow-left"></i> Previous Step
                </button>
                <button @click="triggerFinalSubmit" class="px-6 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    Final Save and Submit <i class="pi pi-save"></i>
                </button>
            </div>
        </div>
    </div>
</template> -->


<!-- =============================================================================================== -->


<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    form: Object, 
    prevStep: Function,
    finalData: Object,
    isProcessing: Boolean,
});

const emit = defineEmits(['nextStep']); // Don't forget to define the emit function

// This function will be called by the "Submit" button in your template.
const triggerFinalSubmit = () => {
    // This emits the @nextStep event, which Main.vue listens to and calls submitForm().
    emit('nextStep'); 
};
</script>

<template>
    <div class="space-y-8 p-4 rounded-lg shadow-inner">
        <div>
            <h2 class="text-xl font-semibold">Confirmation</h2>
            <p class="text-gray-600 text-sm">Make sure all data is correct before saving.</p>
        </div>
        
        <div v-if="finalData" class="mt-8">
            <h3 class="text-lg font-bold text-green-700 border-b pb-2 mb-4">
                ✅ Final JSON Data Dump (From Controller)
            </h3>
            <p class="text-gray-600 text-sm mb-2">
                This is the clean, validated data object returned by Laravel's `store` method.
            </p>
            <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-sm text-gray-800 border border-gray-300">
                {{ JSON.stringify(finalData, null, 2) }}
            </pre>
        </div>

        <div v-else>
            <h3 class="text-lg font-bold border-b pb-2 mb-4">
                Review Data Before Final Submission
            </h3>
             <pre class="bg-yellow-50 p-4 rounded-lg overflow-auto text-sm text-gray-800">
                {{ JSON.stringify(form.data(), null, 2) }}
            </pre>
            
        <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <button @click="prevStep" class="px-4 py-2 text-sm text-gray-600 border rounded hover:bg-gray-100">
                    <i class="pi pi-arrow-left"></i> Previous Step
                </button>
              <button @click="triggerFinalSubmit" 
        type="button"
        :disabled="isProcessing" class="px-6 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
    Final Save and Submit <i class="pi pi-save"></i>
</button>
            </div>
        </div>
    </div>
</template>