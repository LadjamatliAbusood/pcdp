<script setup>
import { computed } from 'vue';
import Tag from 'primevue/tag';

const props = defineProps({
    value: String
});

// 1. Map the exact colors from your image
const defaultMapping = {
    'Deportee': { bg: '#F3FBD8', text: '#5FAF1E', border: '#9AD84B' },
    'Repatriates': { bg: '#e0f2fe', text: '#0369a1', border: '#4DA3FF' }, 
    'LSI Badjao': { bg: '#F3EFFF', text: '#6B4EFF', border: '#9B7BFF' }, 
    'Strandee': { bg: '#FFECEC', text: '#D93030', border: '#FF5A5A' }, 
    'Trafficking in Persons (TIP)': { bg: '#EEF6FF', text: '#2F7DE1', border: '#5DA9FF' }, 
    'Carry-over': { bg: '#E9F9EF', text: '#2F9E44', border: '#4FCB71' },
};

// 2. Generator for new categories (Consistently Random)
const generateDynamicColor = (str) => {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    // Use HSL for a nice "pastel" look that matches the image style
    const h = Math.abs(hash) % 360; 
    return {
        bg: `hsl(${h}, 100%, 97%)`,
        text: `hsl(${h}, 70%, 40%)`,
        border: `hsl(${h}, 70%, 80%)`
    };
};

const tagStyle = computed(() => {
    const category = props.value || 'N/A';
    
    // Check if it exists in the image defaults
    const color = defaultMapping[category] || generateDynamicColor(category);

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
    <Tag :style="tagStyle">
        <span class="whitespace-nowrap">{{ value || 'N/A' }}</span>
    </Tag>
</template>