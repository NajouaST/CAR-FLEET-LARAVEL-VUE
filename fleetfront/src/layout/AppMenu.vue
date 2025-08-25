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
            },

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
                icon: 'pi pi-fw pi-id-card',
                permission: "roles access" ,
                to: '/admin/role'
            },
        ]
    },
    {
        label: 'Parc',
        items: [
            {
                label: 'Vehicule',
                icon: 'pi pi-fw pi-car',
                permission: "vehicules access",
                to: '/parc/vehicule'
            },
            {
                label: 'Affectations véhicules',
                icon: 'pi pi-fw pi-share-alt',
                permission: "vehicules access",
                to: '/parc/affectations-vehicules'
            },
            {
                label: 'Params',
                icon: 'pi pi-fw pi-cog',
                permission: "parcparams access",
                items: [
                    {
                        label: 'Marque',
                        icon: 'pi pi-fw pi-tag',
                        to: '/parc/params/marque'
                    },
                    {
                        label: 'Modele',
                        icon: 'pi pi-fw pi-clone',
                        to: '/parc/params/modele'
                    },
                    {
                        label: 'Gamme',
                        icon: 'pi pi-fw pi-sitemap',
                        to: '/parc/params/gamme'
                    },
                ]
            },
        ]
    },
    {
        label: 'RH',
        items: [
            {
                label: 'Fiche personnel',
                icon: 'pi pi-fw pi-address-book',
                to: '/params/fiche-personnel'
            },
            {
                label: 'Params',
                icon: 'pi pi-fw pi-cog',
                permission: "rhparams access",
                items: [
                    {
                        label: 'Societe',
                        icon: 'pi pi-fw pi-building',
                        to: '/parc/params/marque'
                    },
                    {
                        label: 'Direction',
                        icon: 'pi pi-fw pi-sitemap',
                        to: '/parc/params/modele'
                    },
                    {
                        label: 'Fonction',
                        icon: 'pi pi-fw pi-briefcase',
                        to: '/parc/params/modele'
                    },
                    {
                        label: 'Departement',
                        icon: 'pi pi-fw pi-th-large',
                        to: '/parc/vehicule'
                    },
                    {
                        label: 'Division',
                        icon: 'pi pi-fw pi-th-large',
                        to: '/parc/vehicule'
                    },
                    {
                        label: 'Centre de cout',
                        icon: 'pi pi-fw pi-wallet',
                        to: '/parc/vehicule'
                    },
                ]
            },
        ]
    },
    {
        label: 'Parametres Genereaux',
        permission: "params access",
        items: [
            {
                label: 'Type Compteur',
                icon: 'pi pi-fw pi-asterisk',
                to: '/params/type-compteur'
            },
            {
                label: 'Type Carburant',
                icon: 'pi pi-fw pi-asterisk',
                to: '/params/type-carburant'
            },
            {
                label: 'Famille vehicule',
                icon: 'pi pi-fw pi-th-large',
                to: '/params/famille-vehicule'
            },
            {
                label: 'Fournisseur',
                icon: 'pi pi-fw pi-truck',
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
