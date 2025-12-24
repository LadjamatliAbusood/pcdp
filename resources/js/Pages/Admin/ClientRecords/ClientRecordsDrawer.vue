<script setup>
import { defineProps, defineEmits, computed,ref } from 'vue';
import Drawer from 'primevue/drawer';
import Tag from 'primevue/tag';
import { useClientHelpers } from '@/Constant/useClientHelpers'; 
import Accordion from 'primevue/accordion';
import AccordionPanel from 'primevue/accordionpanel';
import AccordionHeader from 'primevue/accordionheader';
import AccordionContent from 'primevue/accordioncontent';
import Badge from 'primevue/badge';

import Image from 'primevue/image';

const { shortformatDate, getAge, getSexLabel,getEducationOptions,getDurationInMalaysia,ClientPlan } = useClientHelpers();
const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    client: {
        type: Object,
        default: null,
    },
});


const emit = defineEmits(['update:visible']);


const drawerVisibility = computed({
    get: () => props.visible,
    set: (value) => {
        emit('update:visible', value);
    }
});

const familyMembers = computed(() => {
    const clientCaseno = props.client?.client_caseno?.[0];
    const categoryCase = clientCaseno?.category_case?.[0];
    return categoryCase?.client_family_members || []; 
});


const familyMemberCount = computed(() => {
    return familyMembers.value.length;
});

const clientAssessment = computed(() => {
   
    const clientCaseno = props.client?.client_caseno?.[0];
    const categoryCase = clientCaseno?.category_case?.[0];
   
    
    return categoryCase?.client_assessment || []; 
});
const clientServices = computed(() => {
   
    const clientCaseno = props.client?.client_caseno?.[0];
    const categoryCase = clientCaseno?.category_case?.[0];
    return categoryCase?.client_services || []; 
});

</script>

<template>
    <Drawer v-model:visible="drawerVisibility" position="right" :style="{ width: '50%' }">
        <template #header>


            <div v-if="props.client.client_caseno && props.client.client_caseno.length > 0">
                <div v-for="item in props.client.client_caseno" :key="item.id">
                    <p class="font-bold text-base">Case No: {{ item.case_no }}</p>
                </div>
            </div>
        </template>

        <div class="card">
            <Accordion value="0" expandIcon="pi pi-plus" collapseIcon="pi pi-minus">
              <AccordionPanel value="0">
    <AccordionHeader>
        <span class="flex items-center gap-2 w-full">
            <span class="font-bold whitespace-nowrap">Identifying Information</span>
        </span>
    </AccordionHeader>
    <AccordionContent>
        <div class="flex flex-col gap-4">

            <div class="
            bg-white p-4 rounded-lg shadow-md 
            ">
                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Client Identity</h5>
                <div class="grid grid-cols-4 gap-x-4 gap-y-2 text-sm">
                    
                    <div class="col-span-4">
                        <p class="text-xs font-bold text-gray-700">Client Name</p>
                        <p class="font-medium text-primary-600">
                            {{ props.client.lastname }}, {{ props.client.firstname }} 
                            {{ props.client.middlename }} {{ props.client.extensionname }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-700">Gender</p>
                        <p class="font-medium text-primary-600">{{ getSexLabel(props.client.sex) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Age</p>
                        <p class="font-medium text-primary-600">{{ getAge(props.client.birthdate) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Birthdate</p>
                        <p class="font-medium text-primary-600">{{ shortformatDate(props.client.birthdate) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Civil Status</p>
                        <p class="font-medium text-primary-600">{{ props.client.civil_status}}</p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Birthplace</p>
                        <p class="font-medium text-primary-600">{{ props.client.birth_place }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Religion</p>
                        <p class="font-medium text-primary-600">{{ props.client.religion}}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Dialect(s)</p>
                        <p class="font-medium text-primary-600">{{ props.client.dialect}}</p>
                    </div>

                </div>
            </div>

            <div class=" bg-white p-4 rounded-lg shadow-md ">
                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Birth Registration</h5>
                <div class="grid grid-cols-4 gap-x-4 gap-y-2 text-sm">
                    
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Registered with local Registrar?</p>
                        <p class="font-medium text-primary-600">{{ props.client.birth_registered_with_local }}</p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">
                            {{ props.client.birth_registered_with_local === 'Yes' ? 'Where Registered' : 'Reason Why Not Registered' }}
                        </p>
                        <p class="font-medium text-primary-600">
                            {{ props.client.birth_registered_with_local === 'Yes'
                                ? props.client.registered_with_local_where
                                : props.client.registered_with_local_reason_why || 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class=" bg-white p-4 rounded-lg shadow-md ">
                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Address Information</h5>
                <div class="flex flex-col gap-3 text-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-700">Address in the Philippines</p>
                        <p class="font-medium text-primary-600">
                            {{ props.client.address_in_ph_house_no}} {{ props.client.address_in_ph_street}} 
                            {{ props.client.address_in_ph_brgy}}, {{ props.client.address_in_ph_city}}, 
                            {{ props.client.address_in_ph_province}}, {{ props.client.address_in_ph_region}}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Address in Malaysia</p>
                        <p class="font-medium text-primary-600">{{ props.client.address_in_malaysia || 'N/A'}}</p>
                    </div>
                </div>
            </div>

            <div class=" bg-white p-4 rounded-lg shadow-md ">
                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Education & Professional Details</h5>
                <div class="grid grid-cols-4 gap-x-4 gap-y-2 text-sm">
                    
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Highest Educational Attainment</p>
                        <p class="font-medium text-primary-600">{{ getEducationOptions(props.client.education_attainment)}}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Skills</p>
                        <p class="font-medium text-primary-600">{{ props.client.skills}}</p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-700">Eligibility</p>
                        <p class="font-medium text-primary-600">{{ props.client.eligibility || 'N/A'}}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Date Acquired</p>
                        <p class="font-medium text-primary-600">{{ shortformatDate(props.client.eligibility_date_acquired) || 'N/A'}}</p>
                    </div>
                    
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Estimated Monthly Income 
                            {{ props.client.estimated_code}}
                        </p>
                        <p class="font-medium text-primary-600">
                            {{ Number(props.client.estimated_income_foriegn).toLocaleString('en-US') || '0' }}
                            {{ props.client.estimated_code_currency}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AccordionContent>
</AccordionPanel>


                <AccordionPanel value="1">
                    <AccordionHeader>
                        <span class="flex items-center gap-2 w-full">
                            <span class="font-bold whitespace-nowrap">Family Composition</span>
                            <Badge :value="familyMemberCount" class="ml-auto mr-2" />
                        </span>
                    </AccordionHeader>
                <AccordionContent>
    <div v-if="familyMemberCount > 0" class="flex flex-col gap-2">
        <div 
            v-for="fam in familyMembers" 
            :key="fam.id" 
            class="bg-white p-4 rounded-lg shadow-md flex flex-col md:flex-row justify-between"
        >
            <div >
                <h5 class="text-md font-semibold text-primary-700 mb-2">
                    {{ fam.lastname }}, {{ fam.firstname }} {{ fam.middlename }}.
                    {{ fam.extensionname }}
                </h5>
                
                <div class="grid grid-cols-4 gap-2 text-sm">
                    <div class="col-span-1">
                        <p class="text-xs font-bold text-gray-700">Relationship</p>
                        <p class="font-medium text-primary-600">{{ fam.relationship }}</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-xs font-bold text-gray-700">Sex</p>
                        <p class="font-medium text-primary-600">{{ getSexLabel(fam.sex) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Education</p>
                        <p class="font-medium text-primary-600">{{ getEducationOptions(fam.education_attainment) }}</p>
                    </div>

                    <div class="col-span-1">
                        <p class="text-xs font-bold text-gray-700">Birthdate</p>
                        <p class="font-medium text-primary-600">{{ shortformatDate(fam.birthdate) }}</p>
                    </div>

                    <div class="col-span-3">
                        <p class="text-xs font-bold text-gray-700">Occupation / Skills</p>
                        <p class="font-medium text-primary-600">{{ fam.skills_and_occupation}}</p>
                    </div>
                    
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Estimated Monthly Income {{ fam.estimated_code }}</p>
                        <p class="font-medium text-primary-600">
                            {{ Number(fam.estimated_income_foriegn).toLocaleString('en-US') || '0' }}
                            {{ fam.estimated_code_currency }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-xs font-bold text-gray-700">Health Status</p>
                        <p class="font-medium text-primary-600">{{ fam.health_status }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-start justify-center">
                <Image 
                    v-if="fam.fam_img"
                    :src="`/storage/${fam.fam_img}`" 
                    class="w-24 h-24 md:w-32 md:h-32 "
                    alt="Family Member"
                    preview 
                />
                <div v-else class="w-24 h-24 md:w-32 md:h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                    <i class="pi pi-user text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    <p v-else class="m-0 text-gray-500">
        No family members recorded for this client.
    </p>
</AccordionContent>
                </AccordionPanel>
               <AccordionPanel value="2">
                    <AccordionHeader>
                        <span class="flex items-center gap-2 w-full">
                            <span class="font-bold whitespace-nowrap">Assessment</span>
                        </span>
                    </AccordionHeader>
                    <AccordionContent>
                        
                        <div v-if="clientAssessment" class="flex flex-col gap-4">

                            <div class=" bg-white p-4 rounded-lg shadow-md ">
                                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Migration & Employment</h5>
                                <div class="grid grid-cols-4 gap-x-4 gap-y-2 text-sm">
                                    
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Type of Client</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.typeofclient || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">ID Presented</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.id_presented || 'N/A' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs font-bold text-gray-700">Duration of Stay in Malaysia</p>
                                        <p class="font-medium text-primary-600">
                                            {{ clientAssessment.length_stay_in_malaysia }} {{ getDurationInMalaysia(clientAssessment.length_stay_in_malaysia_options) }}
                                        </p>
                                    </div>

                                    <div class="col-span-2">
                                        <p class="text-xs font-bold text-gray-700">How Client Went to Malaysia</p>
                                        <div v-if="clientAssessment.client_went_malaysia === 'With valid papers' ">
                                             <p class="font-medium text-primary-600">{{ clientAssessment.client_went_malaysia}} | {{ clientAssessment.valid_paper_type }}</p>
                                        </div>
                                            <div v-else>
                                                  <p class="font-medium text-primary-600">{{ clientAssessment.client_went_malaysia || 'N/A' }}</p>
                                            </div>
                                       
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <p class="text-xs font-bold text-gray-700">Employed</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.client_employeed || 'N/A' }}</p>
                                    </div>
                                    
                                    <template v-if="clientAssessment.client_employeed === 'Yes'">
                                        <div>
                                            <p class="text-xs font-bold text-gray-700">Nature of Work</p>
                                            <p class="font-medium text-primary-600">{{ clientAssessment.nature_of_work || 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-700">Position Title</p>
                                            <p class="font-medium text-primary-600">{{ clientAssessment.position_title || 'N/A' }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-xs font-bold text-gray-700">Name and Address of Employer</p>
                                            <p class="font-medium text-primary-600">{{ clientAssessment.name_and_address_of_employee || 'N/A' }}</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div class=" bg-white p-4 rounded-lg shadow-md ">
                                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Travel & Repatriation</h5>
                                <div class="grid grid-cols-4 gap-x-4 gap-y-2 text-sm">
                                    
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Repatriated</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.client_repatriated || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Passport/IC No</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.passport_ic_no || 'N/A' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs font-bold text-gray-700">Travel Document No.</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.travel_document_no || 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Issued By</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.issued_by || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Date Issued</p>
                                        <p class="font-medium text-primary-600">{{ shortformatDate(clientAssessment.date_issued) || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class=" bg-white p-4 rounded-lg shadow-md ">
                                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Problem and Action Plan</h5>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm">
                                  <div>
                                <p class="text-xs font-bold text-gray-700">Client Problem</p>
                                <p class="font-medium text-primary-600 whitespace-pre-wrap">{{ clientAssessment.client_problem || 'N/A' }}</p>
                            </div>

                            <div >
                                <p class="text-xs font-bold text-gray-700">Client Plan</p>
                                <p class="font-medium text-primary-600 whitespace-pre-wrap">{{ ClientPlan(clientAssessment.client_plan) || 'N/A' }}</p>
                            </div>

                            <div class="col-span-2">
                            <div  v-if="clientAssessment.client_plan !== 'remain_in_the_ph'">
                                <div >
                                    <p class="text-xs font-bold text-gray-700">Reason </p>
                                    <p class="font-medium text-primary-600 whitespace-pre-wrap">{{ clientAssessment.client_reason || 'N/A' }}</p>
                                </div>
                            </div>

                            <div  v-if="clientAssessment.client_plan === 'remain_in_the_ph'">
                                <div >
                                    <p class="text-xs font-bold text-gray-700">Employment Request</p>
                                    <p class="font-medium text-primary-600">{{ clientAssessment.client_employment || 'N/A' }}</p>
                                </div>
                            </div>
                            </div>
                          
                                      
                                </div>
                            </div>

                            <div class=" bg-white p-4 rounded-lg shadow-md ">
                                <h5 class="text-md font-bold text-primary-700 mb-3 border-b pb-2">Contact Person </h5>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm">
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">Full Name</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.contact_person_fullname || 'N/A' }}</p>
                                    </div>
                                     <div>
                                        <p class="text-xs font-bold text-gray-700">Phone Number</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.contact_person_phonenumber || 'N/A' }}</p>
                                    </div>
                                    <div >
                                        <p class="text-xs font-bold text-gray-700">Relationship</p>
                                        <p class="font-medium text-primary-600">{{ clientAssessment.contact_person_relationship || 'N/A' }}</p>
                                    </div>
                                   
                                </div>
                            </div>
                            
                        </div>
                        <p v-else class="m-0 text-gray-500">
                             No assessment record found for this client/case.
                        </p>
                        
                    </AccordionContent>
                </AccordionPanel>


                <AccordionPanel value="3">
    <AccordionHeader>
        <span class="flex items-center gap-2 w-full">
            <span class="font-bold whitespace-nowrap">Recommendation Services and Assistance</span>
        </span>
    </AccordionHeader>
    <AccordionContent>
        
        <div v-if="clientServices" class="bg-white p-4 rounded-lg shadow-md">
            
            <div class="flex gap-6">

                <div class="flex-1 min-w-0 pr-3">
                    <h5 class="text-md font-bold text-primary-700 mb-3 pb-2">Interventions Needed</h5>
                    
                    <div >
                        
                       <div>
                    <ul class="list-disc ml-5 text-sm">
                        <li v-for="(intervention, index) in clientServices.interventions_needed" :key="index" class="font-medium text-primary-600">
                            {{ intervention }}
                        </li>
                    </ul>
                </div> 

                    </div> 
                   
                </div>

                <div class="w-1/3">
                    <h5 class="text-md font-bold text-primary-700 mb-3 pb-2">Referred To</h5>
                    
                    <p class="font-medium text-primary-600 whitespace-pre-wrap text-sm">
                        {{ clientServices.referred_to || 'N/A' }}
                    </p>
                </div>
            
            </div>
            
        </div>

        <p v-else class="m-0 text-gray-500">
            No services or assistance record found for this case.
        </p>
        
    </AccordionContent>
</AccordionPanel>

           
            </Accordion>
        </div>

    

    </Drawer>
</template>