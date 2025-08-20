<script setup>
import { ref, onMounted, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useRoleStore } from '@/store/role';
import { useRoute, useRouter } from 'vue-router';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

const toast = useToast();
const router = useRouter();
const route = useRoute();
const roleStore = useRoleStore();
const isLoading = ref(true);

const roleId = route.params.id;

const formData = ref({
    name: '',
    permissions: []
});

const permissions = ref([]);

onMounted(async () => {
    isLoading.value = true;
    const [permRes, role] = await Promise.all([
        roleStore.getPermissionsNameList(),
        roleStore.getRoleById(roleId, toast),
    ]);
    permissions.value = permRes?.data ?? [];

    if (role) {
        formData.value.name = role.name;
        formData.value.permissions = role.permissions?.map(p => p.name) ?? [];
    }
    isLoading.value = false;
});

/**
 * Group permissions by entity (before the first space in the permission name)
 */
const groupedPermissions = computed(() => {
    const grouped = {};

    for (const perm of permissions.value) {
        const [entity] = perm.name.split(' ');
        if (!grouped[entity]) grouped[entity] = [];
        grouped[entity].push(perm);
    }

    return grouped;
});

const onSubmit = async () => {
    const res = await roleStore.updateRoles(roleId, formData.value, toast);
    if (res) {
        router.push({ name: 'roles' });
    }
};

const toggleEntityPermissions = (entity) => {
    const perms = groupedPermissions.value[entity].map(p => p.name);
    const current = formData.value.permissions;

    const allSelected = perms.every(p => current.includes(p));

    if (allSelected) {
        formData.value.permissions = current.filter(p => !perms.includes(p));
    } else {
        formData.value.permissions = Array.from(new Set([...current, ...perms]));
    }
};

const isEntityFullySelected = (entity) => {
    const perms = groupedPermissions.value[entity].map(p => p.name);
    return perms.every(p => formData.value.permissions.includes(p));
};
</script>

<template>
    <div v-if="isLoading" class="card text-center my-10">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p>Loading...</p>
    </div>
    <div v-else>
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12">
                <div class="card">
                    <h2 class="text-xl font-semibold mb-4">Edit Role</h2>
                    <Form @submit="onSubmit" :model="formData">
                        <div class="mb-3">
                            <h5 for="name">Name</h5>
                            <InputText id="name" v-model="formData.name" class="w-full" />
                        </div>

                        <div class="mb-3">
                            <h5>Permissions</h5>
                            <div
                                v-for="(perms, entity) in groupedPermissions"
                                :key="entity"
                                class="mb-4 border p-3 rounded"
                            >
                                <div class="flex justify-between items-center mb-2">
                                    <h6 class="font-bold capitalize">{{ entity }}</h6>
                                    <Button
                                        size="small"
                                        :label="isEntityFullySelected(entity) ? 'Uncheck All' : 'Check All'"
                                        severity="secondary"
                                        outlined
                                        @click.prevent="toggleEntityPermissions(entity)"
                                    />
                                </div>

                                <div class="flex flex-wrap gap-3 grid grid-cols-4">
                                    <div
                                        v-for="permission in perms"
                                        :key="permission.name"
                                        class="flex items-center gap-2"
                                    >
                                        <Checkbox
                                            v-model="formData.permissions"
                                            :inputId="permission.name"
                                            :value="permission.name"
                                        />
                                        <label :for="permission.name">{{ permission.name }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <div class="flex flex-col grow basis-0 gap-2">
                                <Button
                                    label="Update"
                                    type="submit"
                                />
                            </div>
                            <div class="flex flex-col grow basis-0 gap-2">
                                <Button
                                    severity="secondary"
                                    label="Back"
                                    @click="router.push({ name: 'roles' })"
                                />
                            </div>
                        </div>

                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
