<template>
  <nav class="p-2" :class="componentClass">
    <ul>
      <li 
        v-for="(item, index) in model" 
        :key="index"
        class="group transition-all duration-200"
      >
        <Link 
          :href="item.to" 
         :class="[
  'flex items-center cursor-pointer gap-2 rounded-[5px] px-1 py-1 text-white font-base hover:bg-white/15',
  { 'bg-white/15': isItemActive(item.to, item.match) }
]"

        >
          <i v-if="item.icon" :class="[item.icon, 'text-base flex-shrink-0']"></i>
          
          <span class="flex-grow">{{ item.label }}</span>
        </Link>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3'; 

const props = defineProps({
  model: {
    type: Array,
    required: true,
    default: () => [],

  },
  class: {
    type: [String, Object, Array],
    default: '',
  }
});

const componentClass = computed(() => props.class);


const page = usePage();

const isItemActive = (to, match = []) => {
  const currentUrl = page.url;

  // exact match
  if (currentUrl === to) return true;

  // parent / grouped routes
  if (Array.isArray(match) && match.length) {
    return match.some(path => currentUrl.startsWith(path));
  }

  return false;
};



</script>