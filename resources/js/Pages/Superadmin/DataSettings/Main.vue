<template>
  <div class="card">
    <Menubar :model="adminMenu">
      <template #item="{ item }">
        <a
          v-ripple
          class="flex items-center gap-1.5 px-3 py-2 cursor-pointer transition-all duration-200 no-underline group relative"
          :class="{
            'border-b-2 border-blue-800 text-blue-800': item.key === activeKey,
            'text-gray-600 hover:border-b-2 hover:border-blue-600 hover:text-blue-600': item.key !== activeKey
          }"
          @click="item.command && item.command()"
        >
          <span :class="[item.icon, 'text-xs group-hover:text-blue-600']" />
          <span class="text-xs font-semibold uppercase tracking-tight">
            {{ item.label }}
          </span>
        </a>
      </template>
    </Menubar>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Menubar from 'primevue/menubar';
import { route } from 'ziggy-js';


const page = usePage();

// Menu items with unique keys
const adminMenu = ref([
  { label: "Client ID Presented", icon: "pi pi-id-card", key: "idpresented", command: () => goTo('client-idpresented.index') },
  { label: "Client Types", icon: "pi pi-users", key: "client-category", command: () => goTo('client-category.index') },
  // { label: "Client Types", icon: "pi pi-user", key: "client-type", command: () => goTo('client-type.index') },
]);

// Active key for current page
const activeKey = ref('');


function goTo(routeName) {
  const tabKey = adminMenu.value.find(i => i.command && routeName.includes(i.key))?.key;
  if (tabKey) activeKey.value = tabKey;
  router.get(route(routeName), {}, { preserveState: true, replace: true });
}


onMounted(() => {
  const path = window.location.pathname;
  const activeTab = adminMenu.value.find(tab => path.includes(tab.key));
  if (activeTab) activeKey.value = activeTab.key;
});
</script>
