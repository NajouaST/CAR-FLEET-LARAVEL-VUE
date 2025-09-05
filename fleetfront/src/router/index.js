import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';
import { useAuthStore } from "@/store/auth";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            redirect: { name: 'dashboard' },
            meta: { authenticated: true },  // Requires authentication
            children: [
                {
                    path: 'dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue'),
                },
                {
                    path: 'profile',
                    name: 'profile',
                    component: () => import('@/views/pages/auth/Profile.vue'),
                },
                {
                    path: 'admin',
                    children: [
                        {
                            path: 'user',
                            meta: { permission: "users access" },
                            children: [
                                {
                                    path: '',
                                    name: 'users',
                                    meta: { permission: "users view" },
                                    component: () => import('@/views/pages/admin/users/ListUser.vue'),
                                },
                                {
                                    path: 'create',
                                    name: 'createUser',
                                    meta: { permission: "users create" },
                                    component: () => import('@/views/pages/admin/users/CreateUser.vue'),
                                },
                                {
                                    path: 'edit/:id',
                                    name: 'editUser',
                                    meta: { permission: "users edit" },
                                    component: () => import('@/views/pages/admin/users/EditUser.vue'),
                                },
                                {
                                    path: 'show/:id',
                                    name: 'showUser',
                                    meta: { permission: "users view" },
                                    component: () => import('@/views/pages/admin/users/ShowUser.vue'),
                                },
                            ]
                        },
                        {
                            path: 'role',
                            meta: { permission: "roles access" },
                            children: [
                                {
                                    path: '',
                                    name: 'roles',
                                    meta: { permission: "roles view" },
                                    component: () => import('@/views/pages/admin/roles/ListRole.vue'),
                                },
                                {
                                    path: 'create',
                                    name: 'createRole',
                                    meta: { permission: "roles create" },
                                    component: () => import('@/views/pages/admin/roles/CreateRole.vue'),
                                },
                                {
                                    path: 'edit/:id',
                                    name: 'editRole',
                                    meta: { permission: "roles edit" },
                                    component: () => import('@/views/pages/admin/roles/EditRole.vue'),
                                },
                                {
                                    path: 'show/:id',
                                    name: 'showRole',
                                    meta: { permission: "roles view" },
                                    component: () => import('@/views/pages/admin/roles/ShowRole.vue'),
                                },
                            ]
                        },
                    ]
                },
                {
                    path: 'parc',
                    children: [
                        {
                            path: 'affectations-vehicules',
                            meta: { permission: "params access" },
                            component: () => import('@/views/pages/parc/affectations-vehicules/ViewAffectationsVehicules.vue'),
                        },
                        {
                            path: 'params',
                            children: [
                                {
                                    path: 'marque',
                                    meta: { permission: "params access" },
                                    component: () => import('@/views/pages/parc/params/ViewMarque.vue'),
                                },
                                {
                                    path: 'modele',
                                    meta: { permission: "params access" },
                                    component: () => import('@/views/pages/parc/params/ViewModele.vue'),
                                },
                                {
                                    path: 'gamme',
                                    meta: { permission: "params access" },
                                    component: () => import('@/views/pages/parc/params/ViewGamme.vue'),
                                },
                            ]
                        },
                        {
                            path: 'vehicule',
                            meta: { permission: "vehicules access" },
                            children: [
                                {
                                    path: '',
                                    name: 'vehicules',
                                    meta: { permission: "vehicules view" },
                                    component: () => import('@/views/pages/parc/vehicule/ViewVehicule.vue'),
                                },
                                {
                                    path: 'create',
                                    name: 'createVehicule',
                                    meta: { permission: "vehicules create" },
                                    component: () => import('@/views/pages/parc/vehicule/CreateVehicule.vue'),
                                },
                                {
                                    path: 'edit/:id',
                                    name: 'editVehicule',
                                    meta: { permission: "vehicules edit" },
                                    component: () => import('@/views/pages/parc/vehicule/EditVehicule.vue'),
                                },
                                {
                                    path: 'show/:id',
                                    name: 'showVehicule',
                                    meta: { permission: "vehicules view" },
                                    component: () => import('@/views/pages/parc/vehicule/ShowVehicule.vue'),
                                },
                            ]
                        },
                    ]
                },
                {
                    path: 'params',
                    children: [
                        
                        {
                            path: 'type-carburant',
                            meta: { permission: "params access" },
                            component: () => import('@/views/pages/params/ViewTypeCarburant.vue'),
                        },
                        {
                            path: 'type-compteur',
                            meta: { permission: "params access" },
                            component: () => import('@/views/pages/params/ViewTypeCompteur.vue'),
                        },
                        {
                            path: 'famille-vehicule',
                            meta: { permission: "params access" },
                            component: () => import('@/views/pages/params/ViewFamilleVehicule.vue'),
                        },
                        {
                            path: 'fournisseur',
                            meta: { permission: "params access" },
                            component: () => import('@/views/pages/params/ViewFournissuer.vue'),
                        },
                    ]
                },
                {
                    path: 'rh',
                    children: [
                        {
                            path: 'fiche-personnel',
                            meta: { permission: "personnels access" },
                            children: [
                                {
                                    path: '',
                                    name: 'personnels',
                                    meta: { permission: "personnels view" },
                                    component: () => import('@/views/pages/rh/fiche-personnel/ViewFichePersonnel.vue'),
                                },
                                {
                                    path: 'create',
                                    name: 'createPersonnel',
                                    meta: { permission: "personnels create" },
                                    component: () => import('@/views/pages/rh/fiche-personnel/CreateFichePersonnel.vue'),
                                },
                                {
                                    path: 'edit/:id',
                                    name: 'editPersonnel',
                                    meta: { permission: "personnels edit" },
                                    component: () => import('@/views/pages/rh/fiche-personnel/EditFichePersonnel.vue'),
                                },
                                {
                                    path: 'show/:id',
                                    name: 'showPersonnel',
                                    meta: { permission: "personnels view" },
                                    component: () => import('@/views/pages/rh/fiche-personnel/ShowFichePersonnel.vue'),
                                },
                            ]
                        },
                        {
                            path: 'params',
                            children: [
                                {
                                    path: 'societe',
                                    meta: { permission: "rhparams access" },
                                    component: () => import('@/views/pages/rh/params/ViewSociete.vue'),
                                },
                                {
                                    path: 'site',
                                    meta: { permission: "rhparams access" },
                                    component: () => import('@/views/pages/rh/params/ViewSite.vue'),
                                },
                                {
                                    path: 'departement',
                                    meta: { permission: "rhparams access" },
                                    component: () => import('@/views/pages/rh/params/ViewDepartement.vue'),
                                },
                                {
                                    path: 'direction',
                                    meta: { permission: "rhparams access" },
                                    component: () => import('@/views/pages/rh/params/ViewDirection.vue'),
                                },
                                {
                                    path: 'centre-de-couts',
                                    meta: { permission: "rhparams access" },
                                    component: () => import('@/views/pages/rh/params/ViewCentreDeCouts.vue'),
                                },
                            ]
                        },
                    ]
                },
            ]
        },
        {
            path: '/forgot-password',
            name: 'forgot-password',
            meta: { authenticated: false }, // No authentication required
            component: () => import('@/views/pages/auth/ForgotPassword.vue'),
        },
        {
            path: '/password-reset/:token',
            name: 'password-reset',
            meta: { authenticated: false }, // No authentication required
            component: () => import('@/views/pages/auth/PasswordReset.vue'),
        },
        {
            path: '/login',
            name: 'login',
            meta: { authenticated: false }, // No authentication required
            component: () => import('@/views/pages/auth/Login.vue'),
        },
        {
            path: '/register',
            name: 'register',
            meta: { authenticated: false }, // No authentication required
            component: () => import('@/views/pages/auth/Register.vue'),
        },
        {
            path: '/access',
            name: 'accessDenied',
            meta: { authenticated: true }, // Requires authentication
            component: () => import('@/views/pages/auth/Access.vue'),
        },
        {
            path: '/pages/notfound',
            name: 'notfound',
            meta: { authenticated: true }, // Requires authentication
            component: () => import('@/views/pages/NotFound.vue'),
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/pages/notfound',
        }
    ]
});

router.beforeEach(async (to) => {
    if(!useAuthStore().user){
        await useAuthStore().getUser()
    }

    if (localStorage.getItem('token') && to.meta.authenticated === false) {
        return { name: 'dashboard' };
    }

    if (!localStorage.getItem('token') && to.meta.authenticated === true) {
        return { name: 'login' };
    }

    if (to.meta.permission) {
        const permission = to.meta.permission;
        const perms = useAuthStore().permissions;

        const hasPermission =
            perms.includes(permission) ||
            perms.includes(`${permission} (own)`) ||
            perms.includes(`${permission} (all)`);

        if (!hasPermission) {
            return { name: 'accessDenied' };
        }
    }

    return true;
});

export default router;
