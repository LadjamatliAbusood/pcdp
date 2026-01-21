<script setup>
import { ref, watch, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import Dropdown from 'primevue/dropdown';
import { pickBy } from "lodash";

const props = defineProps({
    paginator: Object,
    filters: Object, // <-- NEW: pass filters like { search: searchQueryGeneral }
});

const cleanLabel = (label) => {
    if (label.includes('Previous')) return '';
    if (label.includes('Next')) return '';
    return label;
};

// Options for rows per page
const perPageOptions = [
    { label: '10', value: 10 },
    { label: '20', value: 20 },
    { label: '40', value: 40 },
    { label: '50', value: 50 },
];

// Local reactive variable for Dropdown
const selectedPerPage = ref(props.paginator.per_page);

watch(() => props.paginator.per_page, (newVal) => {
    selectedPerPage.value = newVal;
});

// Handle per-page change
const handlePerPageChange = (event) => {
    const newPerPage = event.value;

    router.get(window.location.pathname,
        pickBy({
            page: 1,
            per_page: newPerPage,
            ...props.filters, // <-- keep search & other filters
        }),
        { preserveState: true, replace: true }
    );
};

// --- CHUNKED PAGES LOGIC ---
const chunkSize = 5; // Number of pages per chunk

const currentChunk = computed(() => {
    const currentPage = props.paginator.current_page;
    return Math.floor((currentPage - 1) / chunkSize);
});

const chunkedPages = computed(() => {
    const totalPages = props.paginator.last_page;
    const start = currentChunk.value * chunkSize + 1;
    const end = Math.min(start + chunkSize - 1, totalPages);

    const pages = [];
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

// --- helper for building link with filters ---
const buildPageLink = (page) => {
    // 1. Merge current filters with page and per_page
    const params = pickBy({
        page,
        per_page: props.paginator.per_page,
        ...props.filters,
    });

    
    const searchParams = new URLSearchParams();
    
    Object.keys(params).forEach(key => {
        if (Array.isArray(params[key])) {
            params[key].forEach(value => {
                searchParams.append(`${key}[]`, value); 
            });
        } else {
            searchParams.set(key, params[key]);
        }
    });

    return `${props.paginator.path}?${searchParams.toString()}`;
};
</script>

<template>
    <div v-if="paginator?.links" class="flex items-center justify-between px-2 py-3 border-t border-gray-100">
        
        <!-- Per Page Selector -->
        <div class="flex items-center gap-2">
            <Dropdown
                v-model="selectedPerPage"
                :options="perPageOptions"
                optionLabel="label"
                optionValue="value"
                class="w-24 text-sm"
                @change="handlePerPageChange"
            />
            <span class="text-sm text-gray-500">per page</span>
        </div>

        <!-- Pagination Links -->
        <div class="flex items-center gap-1">

            <!-- First & Prev -->
            <Link v-if="paginator.current_page > 1" :href="buildPageLink(1)" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-full transition-all">
                <i class="pi pi-angle-double-left text-xs font-bold"></i>
            </Link>
            <span v-else class="w-8 h-8 flex items-center justify-center text-gray-300">
                <i class="pi pi-angle-double-left text-xs font-bold"></i>
            </span>

            <Link v-if="paginator.prev_page_url" :href="buildPageLink(paginator.current_page - 1)" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-full transition-all">
                <i class="pi pi-angle-left text-xs font-bold"></i>
            </Link>
            <span v-else class="w-8 h-8 flex items-center justify-center text-gray-300">
                <i class="pi pi-angle-left text-xs font-bold"></i>
            </span>

            <!-- Page Numbers (Chunked) -->
            <template v-for="page in chunkedPages" :key="page">
                <Link
                    :href="buildPageLink(page)"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium transition-colors"
                    :class="page === paginator.current_page 
                        ? 'bg-blue-800 text-white font-bold' 
                        : 'text-gray-600 hover:bg-gray-100'"
                >
                    {{ page }}
                </Link>
            </template>

            <!-- Next & Last -->
            <Link v-if="paginator.next_page_url" :href="buildPageLink(paginator.current_page + 1)" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-full transition-all">
                <i class="pi pi-angle-right text-xs font-bold"></i>
            </Link>
            <span v-else class="w-8 h-8 flex items-center justify-center text-gray-300">
                <i class="pi pi-angle-right text-xs font-bold"></i>
            </span>

            <Link v-if="paginator.current_page < paginator.last_page" :href="buildPageLink(paginator.last_page)" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:bg-gray-100 rounded-full transition-all">
                <i class="pi pi-angle-double-right text-xs font-bold"></i>
            </Link>
            <span v-else class="w-8 h-8 flex items-center justify-center text-gray-300">
                <i class="pi pi-angle-double-right text-xs font-bold"></i>
            </span>
        </div>
    </div>
</template>
