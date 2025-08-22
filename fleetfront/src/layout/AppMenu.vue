<script setup>
import { ref } from "vue";

import AppMenuItem from './AppMenuItem.vue';

defineProps({
    permissions: Array
});

const model = ref([
    {
        label: 'Home',
        items: [
            {
                label: 'Dashboard',
                icon: 'pi pi-fw pi-home',
                to: '/dashboard' ,
            }
        ]
    },
    {
        label: 'Admin',
        items: [
            {
                label: 'Users',
                icon: 'pi pi-fw pi-users',
                permission: "users access",
                to: '/admin/user'
            },
            {
                label: 'Roles',
                icon: 'pi pi-fw pi-users',
                permission: "roles access" ,
                to: '/admin/role'
            },
        ]
    },
    {
        label: 'Parc',
        items: [
            {
                label: 'Params',
                icon: 'pi pi-fw pi-cog',
                permission: "params access",
                items: [
                    {
                        label: 'Marque',
                        icon: 'pi pi-fw pi-users',
                        to: '/parc/params/marque'
                    },
                    {
                        label: 'Modele',
                        icon: 'pi pi-fw pi-users',
                        to: '/parc/params/modele'
                    },
                    {
                        label: 'Gamme',
                        icon: 'pi pi-fw pi-users',
                        to: '/parc/params/gamme'
                    },
                ]
            },
            {
                label: 'Vehicule',
                icon: 'pi pi-fw pi-users',
                permission: "vehicules access",
                to: '/parc/vehicule'
            },
        ]
    },
    {
        label: 'Parametres Genereaux',
        permission: "params access",
        items: [
            {
                label: 'Type Compteur',
                icon: 'pi pi-fw pi-users',
                to: '/params/type-compteur'
            },
            {
                label: 'Type Carburant',
                icon: 'pi pi-fw pi-users',
                to: '/params/type-carburant'
            },
            {
                label: 'Famille vehicule',
                icon: 'pi pi-fw pi-users',
                to: '/params/famille-vehicule'
            },
            {
                label: 'Fournisseur',
                icon: 'pi pi-fw pi-users',
                to: '/params/fournisseur'
            },
        ]
    },
]);


</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator " :item="item" :index="i" :permissions="permissions"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
