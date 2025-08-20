<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue/usetoast';
import { ref } from "vue";
import { useAuthStore } from "@/store/auth";
import { storeToRefs } from "pinia";

const toast = useToast();
const { errors } = storeToRefs(useAuthStore());
const { forgotPassword } = useAuthStore();
const initialValues = ref({
    email: '',
});

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
    if (valid) {
        await forgotPassword( values, toast);
    }
}

</script>

<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw]">
        <div class="flex flex-col items-center justify-center">
            <div class="w-full bg-surface-200 dark:bg-surface-200 py-20 px-8 sm:px-20 rounded-2xl">
                <h1>Forgot Password</h1>
                <Form :initialValues :resolver @submit="onFormSubmit" :validateOnBlur="true">

                    <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">email</label>
                    <InputText name="email" type="email" placeholder="email" class="w-full md:w-[30rem] mb-2"/>
                    <Message v-if="errors?.email" severity="error" size="small" variant="simple">{{errors.email[0]}}</Message>
                    <div class="block mb-2"></div>

                    <Button type="submit" label="Send" class="w-full"  ></Button>
                </Form>

            </div>
        </div>
    </div>
</template>

