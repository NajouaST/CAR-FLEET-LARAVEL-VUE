<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import { storeToRefs } from "pinia";

const toast = useToast();
const route = useRoute();
const router = useRouter();
const { errors } = storeToRefs(useAuthStore());
const { resetPassword } = useAuthStore();
const isSubmitting = ref(false); // <-- new

const formData = ref({
    token: '',
    email: '',
    password: '',
    password_confirmation: ''
});



onMounted(() => {
    // Get token & email from query params in reset link
    formData.value.token = route.params.token || '';
    formData.value.email = route.query.email || '';
});


const onFormSubmit = async () => {
    isSubmitting.value = true; // disable button
    const res = await resetPassword(formData.value, toast);
    if(res){
        router.push({ name: "login" });
    }
    isSubmitting.value = false; // disable button
};
</script>

<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div class="w-full bg-surface-200 dark:bg-surface-800 py-20 px-8 sm:px-20" style="border-radius: 53px">
                <h1 class="mb-6">Reset Password</h1>

                <Form :model="formData" @submit="onFormSubmit" :validateOnBlur="true">

                    <label for="password" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">password</label>
                    <Password id="password" v-model="formData.password" placeholder="Password" :toggleMask="true" class="w-full md:w-[30rem] mb-2" fluid :feedback="false"></Password>
                    <Message v-if="errors?.password" severity="error" size="small" variant="simple">{{errors.password[0]}}</Message>
                    <div class="block mb-2"></div>

                    <label for="password_confirmation" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">password confirmation</label>
                    <Password id="password_confirmation" v-model="formData.password_confirmation" placeholder="password_confirmation" :toggleMask="true" class="mb-2" fluid :feedback="false"></Password>
                    <div class="block mb-8"></div>

                    <Button type="submit" label="Reset Password" class="w-full mt-4" :disabled="isSubmitting" ></Button>
                </Form>
            </div>
        </div>
    </div>
</template>
