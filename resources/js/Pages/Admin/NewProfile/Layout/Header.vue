<script setup>
import { Link } from '@inertiajs/vue3';
import Logo from '@/assets/dswd_bp_logo.svg';
import { router } from '@inertiajs/vue3';
import useConfirm from '../../../Message/Confirm.js'
const { confirmAlert } = useConfirm();
defineOptions({ layout: null });
defineProps({
    homeRoute: {
        type: String,
        default: 'home'
    }
});



</script>

<template>
    <section class="flex-1 flex flex-col h-screen">

        <!-- HEADER -->
        <header class="h-14 shadow bg-blue-900 flex justify-between items-center px-4">
            <div class="font-medium cursor-pointer flex justify-between items-center w-full">

                <!-- Left: Logo -->
                <div class="flex items-center">
                    <img :src="Logo" class="h-10" />
                </div>

                <!-- Right: X / Close -->
                <div class="flex items-center">
                    <Link
                        :href="route(homeRoute)"
                   
                        class="flex items-center cursor-pointer gap-2 rounded-[5px] px-2 py-2 text-white font-medium hover:bg-white/15"
                    >
                        <i class="pi pi-times"></i>
                    </Link>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT goes here -->
        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>

    </section>
</template>
