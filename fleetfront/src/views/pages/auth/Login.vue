<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue/usetoast';
import { ref } from "vue";
import { useAuthStore } from "@/store/auth";
import { storeToRefs } from "pinia";
import router from "@/router";

const toast = useToast();
const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();
const initialValues = ref({
    email: '',
    password: '',
});
const isSubmitting = ref(false); // <-- new

const resolver = ({ values }) => {
    const errors = {
        email: [] ,
        password: [] ,
    };

    if (!values.email) {
        errors.email.push({ type: 'required', message: 'email is required.' });
    }

    if (!values.password) {
        errors.password.push({ type: 'required', message: 'password is required.' });
    }

    return {
        values,
        errors
    };
};

const onFormSubmit = async ({ valid , values }) => {
    isSubmitting.value = true; // disable button
    if (valid) {
        await authenticate('login', values, toast);
    }
    isSubmitting.value = false; // disable button
}

</script>

<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div class="w-full bg-surface-200 dark:bg-surface-800 py-20 px-8 sm:px-20" style="border-radius: 53px">
                <h1>LOGIN</h1>
                <Form :initialValues :resolver @submit="onFormSubmit" :validateOnBlur="true">

                    <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">email</label>
                    <InputText name="email" type="email" placeholder="email" class="w-full md:w-[30rem] mb-2"/>
                    <Message v-if="errors?.email" severity="error" size="small" variant="simple">{{errors.email[0]}}</Message>
                    <div class="block mb-2"></div>

                    <label for="password" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">password</label>
                    <Password id="password" name="password" placeholder="Password" :toggleMask="true" class="w-full md:w-[30rem] mb-2" fluid :feedback="false"></Password>
                    <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                        <div class="flex items-center">
                        </div>
                        <Button label="forgot password" variant="link" @click="router.push({name : 'forgot-password'})"></Button>
                    </div>
                    <Message v-if="errors?.password" severity="error" size="small" variant="simple">{{errors.password[0]}}</Message>
                    <div class="block mb-8"></div>

                    <Button type="submit" label="Login" class="w-full" :disabled="isSubmitting" ></Button>
                </Form>

            </div>
        </div>
    </div>
</template>

