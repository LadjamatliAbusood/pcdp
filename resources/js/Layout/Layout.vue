<script setup>
import Menu from 'primevue/menu';
import 'primeicons/primeicons.css';
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import DSWD_Logo from "@/assets/dswd_bp_logo.svg";
import MenuComponent from './MenuComponent.vue';
import { Inertia } from '@inertiajs/inertia';

// Inertia page props
const page = usePage();
const user = computed(() => page.props.user);
const info = computed(() => page.props.info);

// Page title
const pageTitle = computed(() => page.props.title || 'Dashboard');



// Full name
const fullName = computed(() => {
  if (!info.value) return 'User';
  const first = info.value.first_name;
  const middle = info.value.middle_name ? info.value.middle_name.charAt(0) + '.' : '';
  const last = info.value.last_name;
  return `${first} ${middle} ${last}`;
});

// Role name
const roleName = computed(() => {
  if (!user.value) return '';
  switch(user.value.role) {
    case 1: return 'Superadmin';
    case 2: return 'Admin';
    case 3: return 'User';
    default: return 'Unknown';
  }
});

// Logout
const logout = () => {
  Inertia.post('/logout', {}, {
    onSuccess: () => {
     
    },
    onError: (errors) => {
      // console.error(errors);
      // alert('Logout failed.');
    }
  });
};

// Sidebar menus
const generalMenu = ref([
  { label: "Overview", 
  icon: "pi pi-home", 
   to: "/overview" },
  { label: "Client Records", 
  icon: "pi pi-id-card",  
  to: "/client-records" },
  { label: "Case Management",
   icon: "pi pi-briefcase",
    },
]);

const adminMenu = ref([
  { label: "Users", 
  icon: "pi pi-users", 
   },
  { label: "Roles", 
  icon: "pi pi-user-edit", 
},
  { label: "Settings", 
  icon: "pi pi-cog", 
  to: "/data-settings" },
]);

const systemMenu = ref([
  { label: "System Status", 
  icon: "pi pi-check-circle", 
  },
  { label: "Backups", 
  icon: "pi pi-database", 
  },
]);

// User menu
const userMenu = ref(null);
function toggleUserMenu(event) {
  userMenu.value.toggle(event);
}

const userMenuItems = [
  { label: 'My Account', 
  icon: 'pi pi-user',  },
  { label: 'Sign Out', 
  icon: 'pi pi-sign-out', 
  command: () => logout() }
];
</script>

<template>
  <div class="flex h-screen  ">
    <!-- SIDEBAR -->
    <aside class="w-62 bg-blue-900 text-white flex flex-col p-4">
      <div class="flex flex-col items-center gap-2 mb-3 w-fit">
        
     <img :src="DSWD_Logo"  alt="Logo" />





        <div class="font-bold text-sm ">
          PCDP Data Management System
        </div>
      </div>
      <div class="flex flex-col  bg-white/15 rounded-lg">
        <Link
        :href="route('new-profile')"
          class="flex items-center cursor-pointer gap-2 rounded-[5px] px-2 py-2 text-white font-base hover:bg-white/15 ">
        <i class="pi pi-plus-circle "></i> New Profile
        </Link>


      </div>

      <!-- GENERAL -->
      <div class="text-sm text-gray-300 ">General</div>
      <MenuComponent 
      :model="generalMenu" 
       />

      <!-- ADMIN -->
      <div class="text-sm text-gray-300 ">Admin</div>
      <MenuComponent :model="adminMenu" 
        />

      <!-- SYSTEM SETTINGS -->
      <div class="text-sm text-gray-300 ">System Settings</div>
      <MenuComponent 
      :model="systemMenu" 
     
      />


    </aside>

    <!-- MAIN CONTENT AREA -->
    <section class="flex-1 flex flex-col bg-gray-100">
      <!-- TOP BAR -->
      <header class="h-14 shadow bg-white flex justify-between items-center px-4">

        <!-- LEFT: Title -->
        <div class="font-semibold text-lg">
          {{ pageTitle }}
        </div>

        <!-- RIGHT: User Menu -->
        <div class="font-medium cursor-pointer"  @click="toggleUserMenu">
     {{ fullName }} ▾

        </div>
        <Menu ref="userMenu" :model="userMenuItems" popup />


      </header>


      <!-- PAGE CONTENT -->
      <div class="p-1 text-xl font-semibold">
        <slot />
         <Toast />

      </div>


    </section>
  </div>
</template>
