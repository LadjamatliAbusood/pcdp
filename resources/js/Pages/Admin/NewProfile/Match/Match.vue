<script setup>
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column' 
import { router } from '@inertiajs/vue3' 
import { reactive, ref, onMounted, onBeforeUnmount, nextTick } from 'vue' 
import Header from '../Layout/Header.vue'
import Message from 'primevue/message';
import useNotify from '@/Message/Notify';

const notify = useNotify();
const wsUrl = 'ws://localhost:5050'
const socket = ref(null)
const isConnected = ref(false)

const statusMessage = ref('')
const activeFinger = ref('match_temp') 
const scansCount = ref(0) 
const verificationMessage = ref('') 
const isMatched = ref(false)
const matchedUserData = ref(null) 
const isMatching = ref(false) 
const showResultComponent = ref(false) 
const scannerServiceOffline = ref(false)

const hasDraft = ref(false)
const FORM_STORAGE_KEY = 'unfinishedClientForm'
defineOptions({
    layout: Header
});

const currentImagePreview = reactive({ 
    'match_temp': null 
})

function connectWebSocket() {
    socket.value = new WebSocket(wsUrl)
    
    socket.value.onopen = () => {
        isConnected.value = true
        scannerServiceOffline.value = false
        statusMessage.value = ''
        nextTick(() => startMatch()) 
    }

    socket.value.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data)
            if (data.log && data.log.includes('Scanner service is currently offline.')) {
                scannerServiceOffline.value = true
                isMatching.value = false
                currentImagePreview['match_temp'] = null;
                scansCount.value = 0;
                console.error(data.log)
                return 
            }
            // Reset offline flag if we get a normal message
            if (data.action === 'match_scan_update' || data.action === 'match_result' || data.action === 'reset_done') {
                scannerServiceOffline.value = false
            }

            if (data.action === 'reset_done' || data.action === 'match_scan_update') {
                verificationMessage.value = ''
                isMatched.value = false
                matchedUserData.value = null
                showResultComponent.value = false 
            }

            if (data.action === 'match_scan_update') {
                // ... (scan update logic remains the same)
                if (data.current_image) {
                    currentImagePreview['match_temp'] = 'data:image/png;base64,' + data.current_image
                }
                if (data.scan_count) {
                    scansCount.value = data.scan_count
                }
                
                if (data.log && data.scan_count < 3) {
                    statusMessage.value = `${data.log}`
                } else if (isMatching.value) {
                    statusMessage.value = ''
                }
            }

            if (data.action === 'match_result') {
                isMatching.value = false
                currentImagePreview['match_temp'] = null;

                isMatched.value = data.match_found
                matchedUserData.value = data.match_data 
           
                if (isMatched.value) {
               
                    const dataString = JSON.stringify(matchedUserData.value)
                    
                    try {
                        const targetUrl = route('result', { data: dataString })
                        router.visit(targetUrl, {
                            // Ensure the request is not preserved if this causes issues
                            preserveState: false,
                            preserveScroll: false,
                        })
                    } catch (e) {
                        router.visit(`/result?data=${encodeURIComponent(dataString)}`)
                    }
                    
                    return
                }

                // If NO match is found, continue displaying the error locally
                let message = ' NO MATCH FOUND: Client not registered or mismatch.'
                
                verificationMessage.value = message; 
                statusMessage.value = message; 
                showResultComponent.value = false; 
            }
            
            if (data.action === 'merge_failure') {
                isMatching.value = false;
                verificationMessage.value = '❌ Match Failed: Could not merge the 3 scans. Please try again.';
                statusMessage.value = verificationMessage.value;
                showResultComponent.value = false; 
            }

            if (data.log) {
                if (data.log.includes('Merging successful')) {
                    statusMessage.value = `Processing: ${data.log}`;
                }
            }

        } catch(e){ 
            console.error('Error parsing WS message', e) 
        }
    }

    socket.value.onclose = () => { 
        isConnected.value = false; 
        scannerServiceOffline.value = false;
        statusMessage.value = 'Scanner disconnected. Reconnecting...';
        setTimeout(connectWebSocket, 2000);
    }
    socket.value.onerror = err => { 
        console.error(err); 
        isConnected.value = false;
        scannerServiceOffline.value = false;
        statusMessage.value = ' Connection Error. Check Node Proxy and Java service.';
    }
}
onMounted(connectWebSocket)
onBeforeUnmount(() => { if(socket.value) socket.value.close() })


function startMatch() {

    verificationMessage.value = '';
    isMatched.value = false;
    matchedUserData.value = null;
    scansCount.value = 0;
    showResultComponent.value = false; 
    
    activeFinger.value = 'match_temp';
    isMatching.value = true;
    scannerServiceOffline.value = false;

    if (socket.value && socket.value.readyState === WebSocket.OPEN) {
        socket.value.send(JSON.stringify({ action: 'start_match' })) 
        console.log(`3-Tap Matching sequence started. Awaiting first scan.`)
       
    } else {
        // statusMessage.value = '❌ Scanner service is not connected. Cannot start match.'
    }
}

function resetEnrollment() { 
    // ... (resetEnrollment logic remains the same)
    if (socket.value && socket.value.readyState === WebSocket.OPEN) {
        socket.value.send(JSON.stringify({ action: 'reset' })) 
    }
    scansCount.value = 0
    verificationMessage.value = ''
    isMatched.value = false
    matchedUserData.value = null
    isMatching.value = false;
    showResultComponent.value = false; 
    
    nextTick(() => startMatch()) 
    statusMessage.value = ''
}

function handleFillFromScratch() {
  router.visit(route('new-client'))
}


onMounted(() => {
  hasDraft.value = !!localStorage.getItem(FORM_STORAGE_KEY)
})

function clearDraft() {
  localStorage.removeItem(FORM_STORAGE_KEY)
  hasDraft.value = false

  notify.success('Draft cleared')
}

function continueDraft() {
  const draft = localStorage.getItem(FORM_STORAGE_KEY)
  if (!draft) return
  const encoded = encodeURIComponent(draft)
  router.visit(route('new-client', { prefill: encoded }))
}
</script>
<template>
  <div class="mt-6">
    <Toast/>    
    <div class="text-center mb-6">
      <h2 class="text-gray-600 font-bold">
        Scan the client's fingerprint three times to verify whether they exist in the database.
      </h2>
     
      <div class="card flex flex-wrap gap-4 justify-center">
        <Message v-if="!isConnected"  severity="error" variant="outlined">
             Cannot connect to Fingerprint Scanner Service. Please ensure Node.js Proxy and Java app are running.
        </Message>
       
    </div>
      <div  class="card flex flex-wrap gap-4 justify-center">
            <Message v-if="isConnected && scannerServiceOffline"  severity="error" variant="outlined">
                Scanner service is currently offline.
            </Message>
        
        </div>
      
      <span 
      
       class="text-lg font-medium text-red-500" 
       >
          {{ statusMessage }}
      </span>
    </div>

    <div class="w-64 mx-auto">
      <div class="p-0 shadow-none">
        <div
          class="aspect-square rounded-sm border overflow-hidden cursor-pointer"
          :class="{
            'border-red-500': verificationMessage && !isMatched, 
            'border-sky-500': isMatching && !verificationMessage, 
            'border-gray-300': !isMatching && !verificationMessage && !isMatched
          }"
          @click="isMatching ? null : startMatch()" 
        >
          <div class="flex flex-col h-full justify-center items-center text-center relative">
            <img
              v-if="currentImagePreview['match_temp']"
              :src="currentImagePreview['match_temp']"
              alt="Live Scan Preview"
              class="w-full h-full object-contain opacity-90"
            />
            
          
          </div>
        </div>

       <div class="w-64 mx-auto mt-4">
          <div class="text-center mb-1 h-5">
              <span v-if="verificationMessage && !isMatched" class="text-sm font-semibold text-red-600">
                  Verification Failed (Try again) 
              </span>
              <span v-else-if="isMatching">
                  <span v-if="scansCount < 3" class="text-sm font-semibold text-gray-500">
                      {{ scansCount === 1 ? 'Scan again please' : (scansCount === 2 ? 'Almost there, scan again.' : 'Awaiting first scan...') }}
                  </span>
                  <span v-else class="text-sm font-semibold text-sky-600">
                      Processing scans...
                  </span>
              </span>
              <span v-else class="text-sm font-semibold text-gray-500">
             
      

                   <Button @click="handleFillFromScratch"
        severity="secondary" variant="outlined">
          <i class="pi pi-file" ></i>
        <span class="ml-2 block text-md font-medium">Fill up from scratch</span>
      </Button>
              </span>
          </div>

          <div v-if="isMatching" class="flex h-2 rounded overflow-hidden">
              <div 
                  :style="{ width: (scansCount / 3) * 100 + '%' }" 
                  :class="{
                      'bg-red-500': verificationMessage && !isMatched, 
                      'bg-sky-500': isMatching && scansCount < 3 
                  }"
                  class="h-full transition-all duration-300 ease-in-out">
              </div>
              
              <div 
                  :style="{ width: (1 - scansCount / 3) * 100 + '%' }" 
                  class="h-full bg-gray-300 transition-all duration-300 ease-in-out">
              </div>

              
          </div>
       </div>
      </div>
    </div>
    
    <div v-if="verificationMessage && !isMatched" class="w-full max-w-lg mx-auto mt-4 p-4 rounded-lg text-lg bg-red-100 text-red-700 border border-red-500">
        <p class="font-bold">{{ verificationMessage }}</p>
    </div>

  

    <div  class="flex justify-center pt-6">
           <div v-if="hasDraft" class="space-x-4">
      <Button @click="clearDraft"
        severity="danger"  variant="outlined" >
    
           <i class="pi pi-times-circle" ></i>
        <span class="ml-2 block text-md font-medium">Clear Draft</span>
      </Button>

      <Button @click="continueDraft"
        severity="info" variant="outlined" >
          <i class="pi pi-user-edit" ></i>
        <span class="ml-2 block text-md font-medium">Continue with Draft</span>
      </Button>
    </div>
    <div v-else>
  <Button severity="info" variant="outlined"  @click="resetEnrollment">
        <i class="pi pi-refresh" ></i>
        <span class="ml-2 block text-md font-medium">Reset & Restart Scan</span>
      </Button>
    </div>
    
    </div>
  </div>
</template>