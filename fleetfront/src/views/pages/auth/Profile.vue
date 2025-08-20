<script setup>
import { ref, onMounted } from 'vue';
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import { useAuthStore } from "@/store/auth";
import { useToast } from "primevue/usetoast";
import { useUserStore } from "@/store/user";
import { useConfirm } from "primevue";

const isLoading = ref(true);
const authStore = useAuthStore();
const toast = useToast();
const confirm = useConfirm();

const formDataProfile = ref({
    name: authStore.user.name,
    email: authStore.user.email,
});
const isSubmitting = ref(false); // <-- new

const formDataPassword = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
});

onMounted(async () => {
    isLoading.value = true;
   // const res = await userStore.getRolesNameList();
  //  roles.value = res?.data ?? [];
    isLoading.value = false;
});

const onSubmitProfile = async () => {
    isSubmitting.value = true; // disable button
    await useUserStore().updateUsers(authStore.user.id, formDataProfile.value ,toast);
    isSubmitting.value = false; // disable button
};

const onSubmitPassword = async () => {
    isSubmitting.value = true; // disable button
    await authStore.changePassword(formDataPassword.value, toast);
    isSubmitting.value = false; // disable button
};
const loading = ref(false);

const onSubmitLogout = async () => {
    confirm.require({
        message: 'Are you sure you want to logout other devices?',
        header: 'Logout Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-success',
        rejectClass: 'p-button-danger',
        accept: async () => {
            loading.value = true;
            try {
                await authStore.logoutOthers(toast);
            } finally {
                loading.value = false;
            }
        },
        reject: () => {
            // Optional: handle rejection
        }
    });
};
</script>

<template>
    <ConfirmDialog />

    <div v-if="isLoading" class="card text-center my-10">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p>Loading...</p>
    </div>
    <div v-else>
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12">
                <div class="card grid items-center gap-2">
                    <h2 class="text-xl font-semibold mb-4">Profile</h2>
                    <Accordion value="0">
                        <AccordionPanel value="0">
                            <AccordionHeader>Edit Profile</AccordionHeader>
                            <AccordionContent>
                                <Form @submit="onSubmitProfile" :model="formDataProfile">
                                    <div class="mb-3">
                                        <Label class="font-bold mt-8 mb-2 block" for="name">Name</Label>
                                        <InputText id="name" v-model="formDataProfile.name" class="w-full" />
                                    </div>

                                    <div class="mb-3">
                                        <Label class="font-bold mt-8 mb-2 block" for="email">Email</Label>
                                        <InputText id="email" tupe="email" v-model="formDataProfile.email" class="w-full" />
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <div class="flex flex-col grow basis-0 gap-2">
                                            <Button
                                                label="Update"
                                                type="submit"
                                                :disabled="isSubmitting"
                                            />
                                        </div>
                                    </div>
                                </Form>
                            </AccordionContent>
                        </AccordionPanel>
                        <AccordionPanel value="1">
                            <AccordionHeader>Reset Password</AccordionHeader>
                            <AccordionContent>
                                <Form @submit="onSubmitPassword" :model="formDataPassword">
                                    <div class="mb-3">
                                        <Label class="font-bold mt-8 mb-2 block" for="current_password">Current Password</Label>
                                        <Password id="current_password"  v-model="formDataPassword.current_password" placeholder="Current Password" :toggleMask="true" class="w-full" fluid :feedback="false"></Password>
                                    </div>
                                    <div class="mb-3">
                                        <Label class="font-bold mt-8 mb-2 block" for="password">New Password</Label>
                                        <Password id="password" v-model="formDataPassword.password" placeholder="New Password" :toggleMask="true" class="w-full" fluid :feedback="false"></Password>
                                    </div>
                                    <div class="mb-3">
                                        <Label class="font-bold mt-8 mb-2 block" for="password_confirmation">Password confirmation</Label>
                                        <Password id="password_confirmation" v-model="formDataPassword.password_confirmation" placeholder="Password Confirmation" :toggleMask="true" class="w-full" fluid :feedback="false"></Password>
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <div class="flex flex-col grow basis-0 gap-2">
                                            <Button
                                                label="Update"
                                                type="submit"
                                                :disabled="isSubmitting"
                                            />
                                        </div>
                                    </div>
                                </Form>

                            </AccordionContent>
                        </AccordionPanel>
                        <AccordionPanel value="2">
                            <AccordionHeader>Logout from other devices</AccordionHeader>
                            <AccordionContent>
                                <div class="flex flex-col grow basis-0 gap-2">
                                    <Button
                                        label="Logout from other devices"
                                        @click="onSubmitLogout()"
                                    />
                                </div>
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                </div>
            </div>
        </div>
    </div>
</template>
