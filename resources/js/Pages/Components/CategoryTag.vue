<script setup>
import { computed } from 'vue';
import Tag from 'primevue/tag';

const props = defineProps({
    value: String
});

const defaultMapping = {
    'Deportee': { bg: '#F3FBD8', text: '#5FAF1E', border: '#9AD84B' },
    'Repatriates': { bg: '#e0f2fe', text: '#0369a1', border: '#4DA3FF' }, 
    'LSI Badjao': { bg: '#F3EFFF', text: '#6B4EFF', border: '#9B7BFF' }, 
    'Strandee': { bg: '#FFECEC', text: '#D93030', border: '#FF5A5A' }, 
    'Trafficking in Persons (TIP)': { bg: '#EEF6FF', text: '#2F7DE1', border: '#5DA9FF' }, 
    'Carry-over': { bg: '#E9F9EF', text: '#2F9E44', border: '#4FCB71' },
};

// Style for the "Others" prefix
const otherPrefixStyle = {
    backgroundColor: '#f1f5f9',
    color: '#475569',
    border: '1.5px solid #cbd5e1',
    padding: '2px 10px',
    borderRadius: '999px',
    fontWeight: '700',
    fontSize: '10px',
};

// Logic to determine if this is a custom category
const isCustom = computed(() => {
    return props.value && !defaultMapping[props.value] && props.value !== 'N/A';
});

const tagStyle = computed(() => {
    const category = props.value || 'N/A';
    
    // Default mapping color
    let color = defaultMapping[category];

    // If it's custom, give it a unique "Hue" (e.g., Orange/Amber)
    if (!color) {
        color = { bg: '#FFF9DB', text: '#E67E22', border: '#FAB005' };
    }

    return {
        backgroundColor: color.bg,
        color: color.text,
        border: `1.5px solid ${color.border}`,
        padding: '2px 14px',
        borderRadius: '999px',
        fontWeight: '600',
        fontSize: '12px',
        display: 'inline-block'
    };
});
</script>

<template>
    <div class="flex items-center gap-2">
        <Tag v-if="isCustom" :style="otherPrefixStyle">
            <span class="uppercase">Others</span>
        </Tag>

        <Tag :style="tagStyle">
            <span class="whitespace-nowrap">{{ value || 'N/A' }}</span>
        </Tag>
    </div>
</template>