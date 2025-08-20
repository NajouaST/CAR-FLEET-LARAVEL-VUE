<script setup>
import { useLayout } from '@/layout/composables/layout';
import { useAuthStore } from "@/store/auth";
import { useToast } from "primevue/usetoast";
import { onMounted, ref } from "vue";
import router from "@/router";
import AppConfigurator from "@/layout/AppConfigurator.vue";
import AppNotif from "@/layout/AppNotif.vue";

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

const { logout } = useAuthStore();

const toast = useToast();
const logoutUser = async () => {
    await logout( toast);
};


const menu = ref();
// Menu items
const items = ref([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            router.push('/profile') // Change to your profile route
        }
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => {
            logoutUser()
        }
    }
]);

defineProps({
    user: Object,
});
const toggleProfile = (event) => {
    menu.value.toggle(event);
};
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <router-link :to="{name: 'dashboard'}" class="layout-topbar-logo">
                <span>Fleet</span>
            </router-link>
        </div>

        <div class="layout-topbar-actions  items-center">
            <div class="layout-config-menu">
                <div class="relative">
                    <button
                        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                        type="button"
                        class="layout-topbar-action layout-topbar-action-highlight"
                    >
                        <i class="pi pi-palette"></i>
                    </button>
                    <AppConfigurator />
                </div>

                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>

                <AppNotif></AppNotif>
            </div>

            <div class="layout-config-menu">
                <Button variant="text" label="Profile" icon="pi pi-user" @click="toggleProfile" class="flex flex-col gap-2">
                    <div class="flex items-center">
                        <span class="text-surface-900 dark:text-surface-0 font-bold text">{{user.name}}</span>
                    </div>
                    <div class="flex items-center flex-wrap gap-4">
                        <Tag v-for="role in user.roles" >{{role.name}}</Tag>
                    </div>
                </Button>
                <Menu ref="menu" :model="items" popup />
            </div>

        </div>
    </div>
</template>
