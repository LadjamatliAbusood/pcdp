<script setup>
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import Badge from 'primevue/badge'

const props = defineProps({
  visible: Boolean,
  memberForm: Object, // Receives the specific member's form
})

const emit = defineEmits(['update:visible'])

const videoPlayer = ref(null)
const canvas = ref(null)
const stream = ref(null)

const imageKeys = ['fam_img_front', 'fam_img_left', 'fam_img_right']

const currentStep = computed(() => {
  if (!props.memberForm.fam_img_front) return 'Front View'
  if (!props.memberForm.fam_img_left) return 'Left Profile'
  if (!props.memberForm.fam_img_right) return 'Right Profile'
  return 'All Photos Captured'
})

const allCaptured = computed(() => imageKeys.every(key => !!props.memberForm[key]))

const startCamera = async () => {
  try {
    stream.value = await navigator.mediaDevices.getUserMedia({ 
      video: { facingMode: 'user', width: 1280, height: 720 } 
    })
    videoPlayer.value.srcObject = stream.value
  } catch (err) { console.error('Camera error:', err) }
}

const stopCamera = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop())
    stream.value = null
  }
}

const takePhoto = () => {
  const video = videoPlayer.value
  const ctx = canvas.value.getContext('2d')
  canvas.value.width = video.videoWidth
  canvas.value.height = video.videoHeight
  ctx.drawImage(video, 0, 0)

  let targetKey = imageKeys.find(k => !props.memberForm[k])
  if (!targetKey) return

  const dataUrl = canvas.value.toDataURL('image/jpeg', 0.9)
  
  // Create File Object
  const bstr = atob(dataUrl.split(',')[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)
  while(n--) u8arr[n] = bstr.charCodeAt(n)
  const file = new File([u8arr], `${targetKey}.jpg`, { type: 'image/jpeg' })

  // Save to memberForm
  props.memberForm[targetKey] = file
  // Save a temporary preview URL for display
  props.memberForm[`${targetKey}_preview`] = dataUrl 
}

const clearPhoto = (key) => {
  props.memberForm[key] = null
  props.memberForm[`${key}_preview`] = null
}
</script>

<template>
  <Dialog 
    :visible="visible" 
    @update:visible="$emit('update:visible', $event)" 
    modal 
    header="Capture Member Photos" 
    :style="{ width: '95vw', maxWidth: '450px' }" 
    @show="startCamera" 
    @hide="stopCamera"
  >
    <div class="flex flex-col gap-4">
      <div class="relative overflow-hidden rounded-xl bg-slate-900 aspect-[4/3] flex items-center justify-center border shadow-inner">
        <video ref="videoPlayer" autoplay playsinline class="w-full h-full object-cover mirror"></video>
        <canvas ref="canvas" class="hidden"></canvas>
        
        <div class="absolute top-3 left-3">
          <Badge 
            :value="currentStep" 
            :severity="allCaptured ? 'success' : 'warning'" 
            class="shadow-md"
          />
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div v-for="key in imageKeys" :key="key" class="flex flex-col items-center gap-1">
          <div 
            class="relative group border-2 border-dashed rounded-lg h-24 w-full flex items-center justify-center bg-slate-50 overflow-hidden transition-all"
            :class="{
              'border-blue-400 bg-blue-50': !memberForm[key] && currentStep.toLowerCase().includes(key.split('_')[2])
            }"
          >
            <img v-if="memberForm[`${key}_preview`]" :src="memberForm[`${key}_preview`]" class="h-full w-full object-cover" />
            <i v-else class="pi pi-camera text-slate-300 text-2xl"></i>

            <div 
              v-if="memberForm[`${key}_preview`]" 
              class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity"
            >
              <Button 
                icon="pi pi-trash" 
                severity="danger" 
                rounded 
                text 
                @click="clearPhoto(key)" 
              />
            </div>
          </div>
          
          <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">
            {{ key.split('_')[2] }}
          </span>
        </div>
      </div>

      <div class="flex flex-col gap-2 pt-2">
        <Button 
          v-if="!allCaptured" 
          label="Capture Photo" 
          icon="pi pi-camera" 
          class="w-full py-3" 
          @click="takePhoto"
        />
        
        <Button 
          :label="allCaptured ? 'Complete' : 'Save & Exit'" 
          :icon="allCaptured ? 'pi pi-check-circle' : 'pi pi-save'" 
          :severity="allCaptured ? 'success' : 'secondary'" 
          class="w-full" 
          @click="$emit('update:visible', false)"
        />
      </div>
    </div>
  </Dialog>
</template>