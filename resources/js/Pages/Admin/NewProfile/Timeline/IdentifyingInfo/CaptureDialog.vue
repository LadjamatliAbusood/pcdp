<script setup>
import { ref, computed } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Badge from 'primevue/badge';

const props = defineProps({
    visible: Boolean,
    form: Object,
});

const emit = defineEmits(['update:visible']);

// Template Refs
const videoPlayer = ref(null);
const canvas = ref(null);
const stream = ref(null);

// Local Previews (since form[key] will now hold a File object)
const previews = ref({
    photo_front: null,
    photo_left: null,
    photo_right: null
});

const imageKeys = ['photo_front', 'photo_left', 'photo_right'];

// Logic state
const hasFront = computed(() => !!props.form.photo_front);
const allCaptured = computed(() => !!(props.form.photo_front && props.form.photo_left && props.form.photo_right));

const currentStep = computed(() => {
    if (!props.form.photo_front) return 'Front View';
    if (!props.form.photo_left) return 'Left Profile';
    if (!props.form.photo_right) return 'Right Profile';
    return 'All Photos Captured';
});

const startCamera = async () => {
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'user', width: 1280, height: 720 } 
        });
        videoPlayer.value.srcObject = stream.value;
    } catch (err) {
        console.error("Error accessing camera:", err);
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
};

const takePhoto = () => {
    const video = videoPlayer.value;
    const ctx = canvas.value.getContext('2d');
    
    canvas.value.width = video.videoWidth;
    canvas.value.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);

    // Identify which field to fill
    let targetKey = '';
    if (!props.form.photo_front) targetKey = 'photo_front';
    else if (!props.form.photo_left) targetKey = 'photo_left';
    else if (!props.form.photo_right) targetKey = 'photo_right';

    if (!targetKey) return;

    // Convert to Blob/File (Matches your family members logic)
    canvas.value.toBlob((blob) => {
        if (!blob) return;

        // 1. Create File object for form submission
        const file = new File([blob], `${targetKey}.jpg`, { type: "image/jpeg" });
        props.form[targetKey] = file;

        // 2. Create Preview URL for UI display
        if (previews.value[targetKey]) URL.revokeObjectURL(previews.value[targetKey]);
        previews.value[targetKey] = URL.createObjectURL(blob);

    }, "image/jpeg", 0.95);
};

const clearPhoto = (key) => {
    props.form[key] = null;
    if (previews.value[key]) {
        URL.revokeObjectURL(previews.value[key]);
        previews.value[key] = null;
    }
};

const saveAndClose = () => {
    emit('update:visible', false);
};
</script>

<template>
    <Dialog 
        :visible="visible" 
        @update:visible="$emit('update:visible', $event)"
        modal 
        header="Capture Identifying Photos" 
        :style="{ width: '95vw', maxWidth: '450px' }"
        @show="startCamera"
        @hide="stopCamera"
    >
        <div class="flex flex-col gap-4">
            <div class="relative overflow-hidden rounded-xl bg-slate-900 aspect-[4/3] flex items-center justify-center border shadow-inner">
                <video ref="videoPlayer" autoplay playsinline class="w-full h-full object-cover mirror"></video>
                <canvas ref="canvas" class="hidden"></canvas>
                
                <div class="absolute top-3 left-3">
                    <Badge :value="currentStep" :severity="allCaptured ? 'success' : 'warning'" class="shadow-md"></Badge>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div v-for="key in imageKeys" :key="key" class="flex flex-col items-center gap-1">
                    <div class="relative group border-2 border-dashed rounded-lg h-24 w-full flex items-center justify-center bg-slate-50 overflow-hidden transition-all"
                         :class="{'border-blue-400 bg-blue-50': !form[key] && currentStep.toLowerCase().includes(key.split('_')[1])}">
                        
                        <img v-if="previews[key]" :src="previews[key]" class="h-full w-full object-cover" />
                        <i v-else class="pi pi-camera text-slate-300 text-2xl"></i>

                        <div v-if="previews[key]" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                             <Button icon="pi pi-trash" severity="danger" rounded text @click="clearPhoto(key)" />
                        </div>
                    </div>
                    <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">
                        {{ key.split('_')[1] }}
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
                    v-if="hasFront"
                    :label="allCaptured ? 'Complete' : 'Save Captured & Exit'" 
                    :icon="allCaptured ? 'pi pi-check-circle' : 'pi pi-save'" 
                    :severity="allCaptured ? 'success' : 'secondary'"
                    :variant="allCaptured ? 'default' : 'outlined'"
                    class="w-full"
                    @click="saveAndClose" 
                />
            </div>
        </div>
    </Dialog>
</template>

