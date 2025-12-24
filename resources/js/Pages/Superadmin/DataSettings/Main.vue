<script setup>
import { ref } from 'vue';
import ClientCategory from '../ClientCategory/Category.vue';
import ClientIDpresnted from '../ClientCategory/IDPresented.vue'
import TypeofClient from '../ClientCategory/TypeofClient.vue';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

const activeIndex = ref(0);

const adminMenu = ref([
  { label: "Client", icon: "pi pi-users", component: ClientCategory },
   { label: "ID Presented", icon: "pi pi-id-card", component: ClientIDpresnted },
   { label: "Type of Client", icon: "pi pi-id-card", component: TypeofClient }
]);
</script>

<template>
  <div class="card">
    <Tabs v-model:value="activeIndex">

      <!-- TAB LABELS -->
      <TabList>
       <Tab
  v-for="(item, i) in adminMenu"
  :key="i"
  :value="i"

>
  <span
    class="flex items-center gap-2 text-sm text-gray-900
           hover:text-blue-800 transition-colors"
    :class="{
      'text-blue-800 font-semibold': activeIndex === i
    }"
  >
    <i :class="item.icon"></i>
    {{ item.label }}
  </span>
</Tab>

      </TabList>

      <!-- TAB CONTENT -->
      <TabPanels>
        <TabPanel
          v-for="(item, i) in adminMenu"
          :key="i"
          :value="i"
        >
          <component :is="item.component" />
        </TabPanel>
      </TabPanels>

    </Tabs>
  </div>
</template>
