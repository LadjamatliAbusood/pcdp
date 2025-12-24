<script setup>
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { reactive, ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import useNotify from '@/Message/Notify'
import Message from 'primevue/message';
const props = defineProps({ 
  clientId: { type: Number, required: true },
  categoryCaseId: { type: Number, required: true }
 })

const wsUrl = 'ws://localhost:5050'
const socket = ref(null)
const isConnected = ref(false)
const scannerServiceOffline = ref(false)
const activeFinger = ref(null)
const scansCount = ref(0)
const differentFinger = ref(false)
const notify = useNotify();
// Fingers list
const fingers = [
  'left_thumb',
  'left_index',
  'left_middle',
  'left_ring',
  'left_pinky',
  'right_thumb',
  'right_index',
  'right_middle',
  'right_ring',
  'right_pinky'
]

// form state
const form = reactive(Object.fromEntries(fingers.map(f => [f, { template: '', image: '' }])))
const skipped = reactive(Object.fromEntries(fingers.map(f => [f, false])))
const remarks = reactive(Object.fromEntries(fingers.map(f => [f, ''])))

// reactive preview map (initialize keys so Vue tracks them)
const currentImagePreview = reactive(Object.fromEntries(fingers.map(f => [f, null])))

function progressClass(finger, slotIndex) {
    const isActive = activeFinger.value === finger
    const isCollected = scansCount.value >= slotIndex
    
    // 1. If enrollment is complete, always show success color (Green)
    if (form[finger].template) {
        return 'bg-green-500'
    }

    if (isActive && isCollected) {
        
  
        if (differentFinger.value) {
            return 'bg-red-500'
        }

        return 'bg-green-500'
    }

    return 'bg-gray-300'
}

onMounted(connectWebSocket)
onBeforeUnmount(() => { if (socket.value) socket.value.close() })

// ------------------------------------------------------------------
// 🚀 CORE FUNCTION: connectWebSocket
// ------------------------------------------------------------------
function connectWebSocket() {
  socket.value = new WebSocket(wsUrl)

  socket.value.onopen = () => {
    isConnected.value = true
    scannerServiceOffline.value = false
    const firstFinger = fingers.find(f => !form[f].template && !skipped[f])
    if (firstFinger) nextTick(() => startScan(firstFinger))
  }

  socket.value.onmessage = (event) => {
    try {
      const data = JSON.parse(event.data)


      if (data.log && data.log.includes('Scanner service is currently offline.')) {
          scannerServiceOffline.value = true
          console.error(data.log)
          // Optionally, you might want to stop the current scan if it was active
          if (activeFinger.value) {
              activeFinger.value = null
              scansCount.value = 0
          }
          return // Stop processing this message further
      }

      if (data.scan_count !== undefined || data.merged_template || data.action === 'reset_done') {
          scannerServiceOffline.value = false
      }

      // Live scan update: always set preview for the finger that the scan came from
      if (data.scan_count !== undefined && data.finger_key) {
        const fk = data.finger_key
        scansCount.value = (activeFinger.value === fk) ? Number(data.scan_count) || scansCount.value : scansCount.value
        differentFinger.value = (activeFinger.value === fk) ? !!data.different_finger : differentFinger.value

        if (data.current_image) {
          let img = data.current_image.replace(/\s/g,'')
          if (!img.startsWith('data:image')) img = 'data:image/png;base64,' + img
          currentImagePreview[fk] = img
        }
      }

      // Enrollment finished for a finger (merged template + image)
      if (data.merged_template && data.finger_key) {
        const fk = data.finger_key

        // store template
        form[fk].template = data.merged_template || ''

        // store final merged image (for the box)
        if (data.merged_image) {
          let img = data.merged_image.replace(/\s/g,'')
          if (!img.startsWith('data:image')) img = 'data:image/png;base64,' + img
          form[fk].image = img
        }

        // clear live preview for that finger
        currentImagePreview[fk] = null

        // reset ui counters if we were scanning this finger
        if (activeFinger.value === fk) {
          activeFinger.value = null
          scansCount.value = 0
          differentFinger.value = false
        }

        // start next finger automatically
        goToNextFinger()
      }

      // Reset confirmed from server
      if (data.action === 'reset_done') {
        scansCount.value = 0
        differentFinger.value = false
        activeFinger.value = null
        // clear all live previews
        fingers.forEach(f => currentImagePreview[f] = null)
        goToNextFinger()
      }
      
      // 🚀 LOGIC TO HANDLE LARAVEL RESPONSE (302 Success or Error)
      if (data.action === 'store_result') {
          const status = data.status
          let message = ''

          // Treat 302 as success because the database commit was confirmed
          if (status === 201 || status === 200 || status === 302) {
              message = 'Fingerprint data saved successfully.'
          
              
              // 1. Show an immediate alert for feedback (Fixes the missing success message)
              // console.log(message) 
              notify.success(message)

              // 2. Try to redirect to the next page
              try {
            
                  router.visit(
                          
                      route('client-records.index', { id: props.clientId }), 
                      { data: { success: message }, preserveScroll: true }
                  )

              } catch (e) {
                 
                  console.error('Inertia Route Error:', e)
              }
              
          } else {
              // Handle Errors (4xx, 5xx)
              
              message = ` Submission failed! Status ${status}. Check console for details.`
             
              try {
                  const responseJson = JSON.parse(data.response_body);
                  message += ' ' + (responseJson.message || 'Server error occurred.');
              } catch (e) {
                  message += ` Review Laravel API logs for status ${status}.`
              }
              
              // Stay on current page and display error
              
              notify.error(message);
          }
      }

      if (data.log) console.log('Java Log:', data.log)
    } catch(err) {
      console.error('WS parse error', err)
    }
  }

  socket.value.onclose = () => { isConnected.value = false; setTimeout(connectWebSocket, 2000) }
  socket.value.onerror = (err) => { console.error('WebSocket error', err); isConnected.value = false }
}
// ------------------------------------------------------------------

function startScan(finger) {
  if (!isConnected.value) return
  if (form[finger].template || skipped[finger]) {
    goToNextFinger()
    return
  }

  if (activeFinger.value) resetEnrollment()

  activeFinger.value = finger
  scansCount.value = 0
  differentFinger.value = false
  currentImagePreview[finger] = null

  if (socket.value && socket.value.readyState === WebSocket.OPEN) {
    socket.value.send(JSON.stringify({ action: 'start_scan', finger_key: finger }))
  }
}

function resetEnrollment() {
  if (socket.value && socket.value.readyState === WebSocket.OPEN) {
    socket.value.send(JSON.stringify({ action: 'reset' }))
  }
  activeFinger.value = null
  scansCount.value = 0
  differentFinger.value = false
  fingers.forEach(f => currentImagePreview[f] = null)
}

function goToNextFinger() {
  const nextFinger = fingers.find(f => !form[f].template && !skipped[f])
  if (nextFinger) {
    setTimeout(() => startScan(nextFinger), 250)
  } else {
    activeFinger.value = null
    scansCount.value = 0
  }
}

function skipAndNext(finger) {
  skipped[finger] = true
  form[finger].template = ''
  form[finger].image = ''
  remarks[finger] = ''
  currentImagePreview[finger] = null

  if (activeFinger.value === finger) {
    activeFinger.value = null
    resetEnrollment()
    setTimeout(() => {
      if (!activeFinger.value) goToNextFinger()
    }, 800)
  } else {
    const nextFinger = fingers.find(f => !form[f].template && !skipped[f])
    if (nextFinger && !activeFinger.value) startScan(nextFinger)
  }
}

function submitAllFingers() {
  if (!isConnected.value) return
  if (activeFinger.value) return 
  console.log("Finish or reset current scan first.")

  const generalRemarks = []
  const payload = {}
  fingers.forEach(f => {
    payload[f] = skipped[f] ? null : (form[f].template || '')
    if (skipped[f] && remarks[f]) generalRemarks.push(`${f}: ${remarks[f]}`)
  })

  const finalPayload = {
    action: 'batch_store',
    client_category_case_id: props.categoryCaseId,
    remark: generalRemarks.length > 0 ? generalRemarks.join(' | ') : null,
    ...payload
  }

  if (socket.value && socket.value.readyState === WebSocket.OPEN) {
    socket.value.send(JSON.stringify(finalPayload))
    console.log('Submission sent to Java service.')
  } else 
  console.log('Cannot submit: WS not open.')
}
</script>


<template>
    <Toast/>
  <div >
    <div class="card flex flex-wrap gap-4 justify-center">
     <Message v-if="!isConnected "  severity="error" variant="outlined">
                Cannot connect to Fingerprint Scanner Service. Please ensure Node.js Proxy and Java app are running.
            </Message>
    </div>

     <div  class="card flex flex-wrap gap-4 justify-center">
            <Message v-if="isConnected && scannerServiceOffline"  severity="error" variant="outlined">
                Scanner service is currently offline.
            </Message>
        
        </div>


    <form  @submit.prevent="submitAllFingers" class="space-y-6">


<div class="relative w-full flex items-center">

  <!-- CENTER TEXT (absolute centered) -->
  <div 
    v-if="differentFinger" 
    class="absolute left-1/2 transform -translate-x-1/2 text-red-500 font-bold text-center"
  >
    Different finger detected! Please lift and place the same finger again.
  </div>

  <!-- RIGHT BUTTON -->
  <div class="ml-auto" v-if="activeFinger">
    <Button type="button" @click="resetEnrollment" class="p-button-danger p-button-sm">
      Reset Current Scan
    </Button>
  </div>

</div>


  <!-- Different Finger Warning -->
  
  <div >
        <div  class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div v-for="finger in fingers" :key="finger" class="flex justify-center">
          <div class="w-48 p-0 shadow-none space-y-2">
            <div
              class="aspect-square rounded-sm border overflow-hidden cursor-pointer"
              :class="{
                ' border-green-500 ': form[finger].template && !skipped[finger],
                ' border-yellow-500 ': skipped[finger],
                ' border-sky-500 ': activeFinger === finger,
                ' border-gray-300': !form[finger].template && !skipped[finger] && activeFinger !== finger,
              }"
              @click="startScan(finger)"
            >
              <div class="flex flex-col h-full justify-center items-center text-center p-0 relative">
                <img
                  v-if="currentImagePreview[finger] && activeFinger === finger"
                  :src="currentImagePreview[finger]"
                  alt="Live Scan Preview"
                  class="w-full h-full object-contain opacity-90"
                />

                <img
                  v-else-if="form[finger].image && !skipped[finger]"
                  :src="form[finger].image"
                  alt="Captured Fingerprint"
                  class="w-full h-full object-contain"
                />

                <span v-else-if="skipped[finger]" class="absolute text-yellow-700 font-bold">No Finger</span>

                <span v-else-if="activeFinger === finger" class="absolute text-blue-600 font-bold">
                       {{ finger.replace('_', ' ') }}</span>
                    
                  

                <span v-else class="absolute text-gray-600 font-bold">No Finger Scan</span>
              </div>
            </div>

          <div class="flex h-3 rounded overflow-hidden w-full border border-gray-300">
        <div 
            v-for="i in 3" 
            :key="i"
            class="flex-1 h-full transition-all duration-300 ease-in-out"
            :class="progressClass(finger, i)"
        ></div>
        </div>


          

            <!-- <div
              v-if="form[finger].template"
              class="text-xs text-gray-700 bg-white p-1 border rounded-md"
              style="height: 50px; overflow-y: auto; word-break: break-all;"
            >
              <p class="font-mono">{{ form[finger].template }}</p>
            </div> -->

            <div class="flex justify-between items-center pt-2">
          
              <Button
                v-if="!form[finger].template && !skipped[finger] && activeFinger === finger"
                type="button"
                class="p-button-warning p-button-sm"
                @click.stop="skipAndNext(finger)"
              >
                Skip
              </Button>
            </div>

            <div v-if="skipped[finger]">
              <InputText class="w-full" placeholder="Enter remarks..." v-model="remarks[finger]" />
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-center pt-8">
        <Button type="submit" class="p-button-success p-button-lg" :disabled="!isConnected || activeFinger">
          Submit All Fingerprints
        </Button>
      </div>

  </div>

  

      
    </form>
  </div>
</template>

