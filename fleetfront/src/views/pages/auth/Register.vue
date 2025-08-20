<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { Form } from '@primevue/forms';
import { useToast } from 'primevue/usetoast';
import { ref } from "vue";
import { useAuthStore } from "@/store/auth";
import { storeToRefs } from "pinia";

const toast = useToast();
const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();
const initialValues = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const resolver = ({ values }) => {
    const errors = {
        name: [] ,
        email: [] ,
        password: [] ,
        password_confirmation: [] ,
    };

    if (!values.name) {
        errors.name.push({ type: 'required', message: 'name is required.' });
    }

    if (values.name?.length < 3) {
        errors.name.push({ type: 'minimum', message: 'name must be at least 3 characters long.' });
    }

    if (!values.email) {
        errors.email.push({ type: 'required', message: 'email is required.' });
    }

    if (!values.password) {
        errors.password.push({ type: 'required', message: 'password is required.' });
    }

    if (!values.password_confirmation) {
        errors.password_confirmation.push({ type: 'required', message: 'password confirmation is required.' });
    }

    return {
        values,
        errors
    };
};

const onFormSubmit = async ({ valid , values }) => {
    if (valid) {
        await authenticate('register', values, toast);
    }
}

</script>

<template>
    <FloatingConfigurator />
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div class="w-full bg-surface-200 dark:bg-surface-800 py-20 px-8 sm:px-20" style="border-radius: 53px">
                <h1>LOGIN</h1>
                <Form :initialValues :resolver @submit="onFormSubmit" :validateOnBlur="true">

                    <label for="name" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">name</label>
                    <InputText name="name" type="text" placeholder="name" class="w-full md:w-[30rem] mb-2"/>
                    <Message v-if="errors?.name" severity="error" size="small" variant="simple">{{errors.name[0]}}</Message>
                    <div class="block mb-2"></div>

                    <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">email</label>
                    <InputText name="email" type="email" placeholder="email" class="w-full md:w-[30rem] mb-2"/>
                    <Message v-if="errors?.email" severity="error" size="small" variant="simple">{{errors.email[0]}}</Message>
                    <div class="block mb-2"></div>

                    <label for="password" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">password</label>
                    <Password id="password" name="password" placeholder="Password" :toggleMask="true" class="w-full md:w-[30rem] mb-2" fluid :feedback="false"></Password>
                    <Message v-if="errors?.password" severity="error" size="small" variant="simple">{{errors.password[0]}}</Message>
                    <div class="block mb-2"></div>

                    <label for="password_confirmation" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">password confirmation</label>
                    <Password id="password_confirmation" name="password_confirmation" placeholder="password_confirmation" :toggleMask="true" class="mb-2" fluid :feedback="false"></Password>
                    <div class="block mb-8"></div>

                    <Button type="submit" label="Register" class="w-full"  ></Button>
                </Form>
            </div>
        </div>
    </div>
</template>

