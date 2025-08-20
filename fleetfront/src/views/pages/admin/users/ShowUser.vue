<script setup>
import { ref, onMounted, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useUserStore } from '@/store/user';
import { useRoute, useRouter } from 'vue-router';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

const toast = useToast();
const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const isLoading = ref(true);

const userId = route.params.id;

const formData = ref({
    name: '',
    email: '',
    is_active: '',
    roles: []
});

const roles = ref([]);

onMounted(async () => {
    isLoading.value = true;
    const [droles, user] = await Promise.all([
        userStore.getRolesNameList(),
        userStore.getUserById(userId, toast),
    ]);
    roles.value = droles?.data ?? [];

    if (user) {
        formData.value.name = user.name;
        formData.value.email = user.email;
        formData.value.is_active = user.is_active;
        formData.value.roles = user.roles?.map(p => p.name) ?? [];
    }
    isLoading.value = false;
});
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
                    <h2 class="text-xl font-semibold mb-4">Show User</h2>
                    <Form :model="formData">
                        <div class="mb-3">
                            <h5 for="name">Name</h5>
                            <InputText id="name" v-model="formData.name" class="w-full" readonly/>
                        </div>

                        <div class="mb-3">
                            <h5 for="email">Email</h5>
                            <InputText id="email" tupe="email" v-model="formData.email" class="w-full" readonly/>
                        </div>

                        <div class="mb-3">
                            <h5 for="is_active">Is active</h5>
                            <ToggleButton v-model="formData.is_active" onLabel="Oui" offLabel="Non" class="min-w-20" readonly/>
                        </div>

                        <div class="mb-3">
                            <h5 for="roles">Roles</h5>
                            <div class="flex flex-wrap gap-3 grid grid-cols-4">
                                <div
                                    v-for="role in roles"
                                    :key="role.name"
                                    class="flex items-center gap-2"
                                >
                                    <Checkbox
                                        v-model="formData.roles"
                                        :inputId="role.name"
                                        :value="role.name"
                                        readonly
                                    />
                                    <label :for="role.name">{{ role.name }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <div class="flex flex-col grow basis-0 gap-2">
                                <Button
                                    severity="secondary"
                                    label="Back"
                                    @click="router.push({ name: 'users' })"
                                />
                            </div>
                        </div>

                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
