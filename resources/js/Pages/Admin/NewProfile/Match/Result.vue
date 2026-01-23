<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Button from 'primevue/button'
import { router } from '@inertiajs/vue3'
import Header from '../Layout/Header.vue'

defineOptions({
  layout: Header
});


const clientData = ref([])
const matchedUserData = ref(null)

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const dataString = urlParams.get('data');

  if (dataString) {
    try {
      const data = JSON.parse(decodeURIComponent(dataString));
      matchedUserData.value = data;

      // Safely retrieve case_no from either root or nested structure for display
      const caseNo = data.case_no || data.client_caseno?.case_no || 'N/A';


      // Data for display in Result.vue
      clientData.value = [
        { label: 'Case No.', value: caseNo },
        { label: 'Client ID', value: data.id || 'N/A' },
        { label: 'First Name', value: data.firstname },
        { label: 'Middle Name', value: data.middlename },
        { label: 'Last Name', value: data.lastname },
        { label: 'Birthday', value: getDate(data.birthdate) },
      ];
    } catch (e) {
      console.error('Failed to parse client data from URL query:', e);
    }
  }
});



function handleScanAgain() {
  router.visit(route('new-profile'))
}

function handleFillFromScratch() {
  localStorage.removeItem('unfinishedClientForm');
  localStorage.removeItem('unfinishedClientFormPhotos');
  router.visit(route('new-client'))
}


const getDate = (date) =>
  new Date(date).toLocaleDateString("en-us", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

function handleContinue() {
  if (!matchedUserData.value) return;

  const data = matchedUserData.value;

  const clientCaseno = Array.isArray(data.client_caseno) ? data.client_caseno[0] : data.client_caseno;

  // 1. Prepare the data object
  const dataToSend = {
    ...data,
    client_id: data.id,
    case_no: data.case_no || clientCaseno?.case_no,
    
    // 2. CLEAR THE PHOTOS
    // This ensures the user MUST capture new photos in Main.vue
    photo_front: null,
    photo_left: null,
    photo_right: null,
  };

  // 3. Clean up the object structure
  delete dataToSend.client_caseno;
  delete dataToSend.id; 

  // 4. Send to the form
  const encodedData = encodeURIComponent(JSON.stringify(dataToSend));
  router.visit(route('new-client', { prefill: encodedData }));
}
</script>

<template>
  <div class="mt-8 flex justify-center">
    <div class="w-full max-w-2xl ">

      <div class="pb-3 mb-4 text-center">
        <h3 class="text-2xl font-bold text-gray-700">
          Is this client's information displayed here?
        </h3>
      </div>

      <div class="mb-6 flex justify-center">
        <div class="w-full max-w-md">
          <div v-for="item in clientData" :key="item.label" class="grid grid-cols-2 gap-4 p-2">
            <div class="text-base font-medium text-gray-600">
              {{ item.label }}:
            </div>
            <div class="text-base text-gray-800 font-medium">
              {{ item.value }}
            </div>
          </div>
        </div>
      </div>


      <div class="pt-4">
        <div class="flex justify-between">

        

            <Button  @click="handleFillFromScratch"
        severity="secondary" variant="outlined">
          <i class="pi pi-file" ></i>
        <span class="ml-2 block text-md font-medium">Fill up from scratch</span>
      </Button>


               <Button  @click="handleScanAgain"
        severity="secondary" variant="outlined">
          <i class="pi pi-qrcode" ></i>
        <span class="ml-2 block text-md font-medium">Scan again</span>
      </Button>

          <Button label="Continue with this data" icon="pi pi-id-card" @click="handleContinue" />

          
        </div>

      </div>
    </div>
  </div>
</template>