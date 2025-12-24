<script setup>
import { defineProps, defineEmits, ref, onMounted, reactive } from 'vue';
import AddFamilyMemberModal from './components/AddFamilyMemberModal.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import TextInputField from '@/Components/TextInputField.vue';

const props = defineProps({
    form: Object,
    prevStep: Function,
});
const videoRefs = reactive({});
const canvasRefs = reactive({});
const cameraStreams = reactive({});

const emit = defineEmits(['nextStep']);
const addFamilyMemberModal = ref(null);
const form = props.form;


const editingMemberIndex = ref(null);


onMounted(() => {
    if (!form.family_members || !Array.isArray(form.family_members)) {
        form.family_members = [];
    }
});


const triggerNextStep = () => {
    emit('nextStep');
};

const triggerPrevStep = () => {
    if (props.prevStep) {
        props.prevStep();
    }
};


const openModal = (memberToEdit = null, index = null) => {
    editingMemberIndex.value = index;
    const dataToPass = memberToEdit ? { ...memberToEdit } : {};
    addFamilyMemberModal.value.openModal(dataToPass, index);
}


const handleMemberSaved = (savedMember, index) => {
    if (index !== null && index >= 0 && index < form.family_members.length) {

        form.family_members[index] = savedMember;
    } else {

        form.family_members.push(savedMember);
    }

    editingMemberIndex.value = null;

};


const editMember = (member, index) => {
    openModal(member, index);
};


const removeMember = (index) => {
    // if (confirm('Are you sure you want to remove this family member?')) {
    form.family_members.splice(index, 1);
    // }
};



const getEducationLabel = (value) => {
    const options = [
        { label: 'College', value: 1 },
        { label: 'Senior High', value: 2 },
        { label: 'Junior High', value: 3 },
        { label: 'Elementary', value: 4 },
        { label: 'None', value: 5 },
    ];
    return options.find(opt => opt.value === value)?.label || 'N/A';
};

const getSexLabel = (value) => {
    const options = [
        { label: 'Male', value: 1 },
        { label: 'Female', value: 2 },
    ];
    return options.find(opt => opt.value === value)?.label || 'N/A';
};

const calculateAge = (birthdate) => {
    if (!birthdate) return 'N/A';
    const birthDate = new Date(birthdate);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDifference = today.getMonth() - birthDate.getMonth();
    if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
};

const startCamera = async (index) => {
    form.family_members[index].showCamera = true;

    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    cameraStreams[index] = stream;

    if (videoRefs[index]) {
        videoRefs[index].srcObject = stream;
    }
};
const stopCamera = (index) => {
    form.family_members[index].showCamera = false;

    if (cameraStreams[index]) {
        cameraStreams[index].getTracks().forEach(t => t.stop());
        cameraStreams[index] = null;
    }
};
const capturePhoto = (index) => {
    const video = videoRefs[index];
    const canvas = canvasRefs[index];

    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob((blob) => {
        const file = new File([blob], `member_${index}.jpg`, { type: "image/jpeg" });
        const previewUrl = URL.createObjectURL(blob);

        form.family_members[index].fam_img = file;
        form.family_members[index].fam_img_preview = previewUrl;
        form.family_members[index].showCamera = false;

        stopCamera(index);
    }, "image/jpeg", 0.95);
};



</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">Family Composition</h2>
            <p class="text-gray-600 text-sm">Details about the deported person's immediate family.</p>
        </div>

        <form @submit.prevent>
            <div class="flex space-x-4 mb-6 p-2">
                <div class="w-full">
                    <button
                        class="w-full items-center justify-center font-medium px-5 py-2.5 text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-200 flex transition duration-150 ease-in-out"
                        @click="openModal()">
                        <span class="text-center">Add Family Member</span>
                        <i class="pi pi-user-plus px-2"></i>
                    </button>
                </div>
            </div>


            <div v-if="form.family_members && form.family_members.length > 0">


                <div class="space-y-4">

                    <Card class="mt-2 shadow-lg" header="User Details" v-for="(member, index) in form.family_members"
                        :key="index">
                        <template #content>
                         <div class="flex gap-4 items-start">
    <!-- Left: User Details -->
    <div class="flex-1 space-y-2">
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Full Name:</label>
            <div class="col-span-2">
                {{ member.firstname }}, {{ member.lastname }} {{ member.middlename }}{{ member.extensionname }}
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Age:</label>
            <div class="col-span-2">{{ calculateAge(member.birthdate) }}</div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Sex:</label>
            <div class="col-span-2">{{ getSexLabel(member.sex) }}</div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Civil Status:</label>
            <div class="col-span-2">{{ member.civil_status }}</div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Relationship:</label>
            <div class="col-span-2">{{ member.relationship }}</div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Education:</label>
            <div class="col-span-2">{{ getEducationLabel(member.education_attainment) }}</div>
        </div>
        <div class="grid grid-cols-3 gap-3 items-start py-1">
            <label class="font-medium text-gray-700">Skills/Occupation:</label>
            <div class="col-span-2">{{ member.skills_and_occupation }}</div>
        </div>
    </div>

    <!-- Right: Photo / Camera -->
    <div class="flex flex-col items-center gap-2">
        <!-- Show preview if captured -->
        <img v-if="member.fam_img_preview && !member.showCamera"
            :src="member.fam_img_preview"
            class="w-32 h-32 object-cover rounded border" />

        <!-- Camera View -->
        <div v-if="member.showCamera" class="space-y-2">
            <video :ref="el => videoRefs[index] = el" autoplay playsinline
                class="w-48 h-36 bg-black rounded"></video>

            <canvas :ref="el => canvasRefs[index] = el" width="400" height="300"
                class="hidden"></canvas>

            <div class="flex gap-2">
                <button @click="capturePhoto(index)"
                    class="px-3 py-1 bg-blue-700 text-white rounded text-sm">
                    Capture
                </button>

                <button @click="stopCamera(index)"
                    class="px-3 py-1 bg-gray-500 text-white rounded text-sm">
                    Close
                </button>
            </div>
        </div>

        <!-- Show open camera button -->
        <button v-if="!member.showCamera"
            @click="startCamera(index)"
            class="px-3 py-1 bg-blue-700 text-white rounded text-sm">
            Take Photo
        </button>

        <!-- Validation message -->
        <p v-if="!member.fam_img && !member.showCamera" class="text-red-600 text-sm mt-1">
            Photo is required
        </p>
    </div>
</div>

                        </template>

                        <template #footer>
                            <div class="flex justify-end gap-2">
                                <Button icon="pi pi-trash" label="Remove" severity="danger" text rounded
                                    @click="removeMember(index)" class="text-xs" />
                                <Button icon="pi pi-pencil" label="Edit" severity="info" text rounded
                                    @click="editMember(member, index)" class="text-xs" />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <div v-else class="text-center p-6 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
                No family members added yet. Click "Add Family Member" to begin.
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

    <AddFamilyMemberModal ref="addFamilyMemberModal" @member-saved="handleMemberSaved" />
</template>