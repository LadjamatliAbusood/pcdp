<script setup>
import Panel from 'primevue/panel';
import Header from '../../Layout/Header.vue';
import finger from './fingers/finger.vue';
import { useClientHelpers } from '@/Constant/useClientHelpers';

const { getSexLabel, formatDate, getAge } = useClientHelpers();

defineOptions({
    layout: Header
});

defineProps({
    client: Object
});


</script>

<template>
    <div class="p-4 space-y-4">
        <!-- CLIENT INFORMATION -->
        <Panel header="Client Information">
            <div class="flex flex-wrap items-center gap-6">

                <!-- Name -->
                <div class="flex gap-1 items-center">
                    <span class="font-semibold">Name:</span>
                    <span>{{ client.firstname }} {{ client.middlename }} {{ client.lastname }} {{ client.extensionname ?? '' }}</span>
                </div>

                <!-- Case No -->
                <div class="flex gap-1 items-center">
    <span class="font-semibold">Case No:</span>
    <span>
        <!-- Check if there's at least one case -->
        {{ client.client_caseno.length > 0 ? client.client_caseno[0].case_no : 'N/A' }}
    </span>
</div>

               
                <!-- Sex -->
                <div class="flex gap-1 items-center">
                    <span class="font-semibold">Sex:</span>
                    <span>{{ getSexLabel(client.sex) }}</span>
                </div>

                <!-- Birthdate
                <div class="flex gap-1 items-center">
                    <span class="font-semibold">Birthdate:</span>
                    <span>{{ formatDate(client.birthdate) }}</span>
                </div> -->

                <!-- Age -->
                <div class="flex gap-1 items-center">
                    <span class="font-semibold">Age:</span>
                    <span>{{ getAge(client.birthdate) }}</span>
                </div>

            </div>
        </Panel>

        <!-- Fingerprint component -->
        <finger :client-id="client.id"
        :category-case-id="client.client_caseno.length > 0 ? client.client_caseno[0].id : null"/>
    </div>
</template>
