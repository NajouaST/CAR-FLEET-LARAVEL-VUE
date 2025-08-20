<script setup>
import { ref, onMounted, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useUserStore } from '@/store/user';
import { useRoute, useRouter } from 'vue-router';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import { storeToRefs } from "pinia";

const toast = useToast();
const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const { errors } = storeToRefs(useUserStore());
const isLoading = ref(true);
const isSubmitting = ref(false); // <-- new

const userId = route.params.id;

const formData = ref({
    name: '',
    email: '',
    is_active: '',
    roles: []
});

const roles = ref([]);

onMounted(async () => {
    userStore.errors = null
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
const onSubmit = async () => {
    isSubmitting.value = true; // disable button
    const res = await userStore.updateUsers(userId, formData.value, toast);
    if (res) {
        router.push({ name: 'users' });
    }
    isSubmitting.value = false; // disable button
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
                    <h2 class="text-xl font-semibold mb-4">Edit User</h2>
                    <Form @submit="onSubmit" :model="formData">

                        <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">name</label>
                        <InputText name="name" type="text" placeholder="name" v-model="formData.name" class="w-full mb-2"/>
                        <Message v-if="errors?.name" severity="error"  size="small" variant="simple">{{errors.name[0]}}</Message>
                        <div class="block mb-2"></div>

                        <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">email</label>
                        <InputText name="email" type="email" v-model="formData.email" placeholder="email" class="w-full mb-2"/>
                        <Message v-if="errors?.email" severity="error" size="small" variant="simple">{{errors.email[0]}}</Message>
                        <div class="block mb-2"></div>

                        <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Is active</label>
                        <ToggleButton v-model="formData.is_active" onLabel="Oui" offLabel="Non" class="min-w-20" />
                        <div class="block mb-2"></div>

                        <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Is active</label>
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
                                />
                                <label :for="role.name">{{ role.name }}</label>
                            </div>
                        </div>
                        <div class="block mb-8"></div>

                        <div class="flex flex-wrap gap-4">
                            <div class="flex flex-col grow basis-0 gap-2">
                                <Button
                                    label="Update"
                                    :disabled="isSubmitting"
                                    type="submit"
                                />
                            </div>
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
