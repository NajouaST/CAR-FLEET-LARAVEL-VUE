import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";

export const useAuthStore = defineStore('authStore', {
    state: () =>{
        return {
            user : null,
            permissions : null,
            errors :{}
        }
    },
    actions: {
        async getUser() {
            if (localStorage.getItem('token')) {
                try {
                    const res = await axiosInstance.get('/user', {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });
                    this.user = res.data.user;
                    this.permissions = res.data.permissions;
                } catch (error) {
                    console.log(error)
                    this.user = null;
                    this.permissions = null;
                    localStorage.removeItem('token');
                    router.push("/login");
                }
            }
        },

        async logout(toast) {
            if (localStorage.getItem('token')) {
                try {
                    await axiosInstance.post('/logout', {}, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    toast.add({ severity: 'success', summary: 'Logging out', life: 3000 });
                    this.user = null;
                    this.errors = {};
                    localStorage.removeItem('token');
                    this.router.push({ name: "login" });
                } catch (error) {
                    // Optionally handle errors here (e.g., display error toast)
                }
            }
        },

        async authenticate(apiRoute, formData, toast) {
            try {
                // console.log('d5al lel authenticate (this is a temporary workaround)');
                // await axiosInstance.get('http://localhost:8000/sanctum/csrf-cookie');
                const res = await axiosInstance.post(`/${apiRoute}`, formData);
                
                if (res.data.errors) {
                    this.errors = res.data.errors;
                } else {
                    toast.add({ severity: 'success', summary: 'logged in', life: 3000 });
                    localStorage.setItem('token', res.data.token);
                    this.user = res.data.user;
                    this.errors = {};
                    this.router.push({ name: "dashboard" });
                }
            } catch (err) {
                console.log("Error authenticating:", err)
                if (err.response?.data?.errors) {
                    this.errors = err.response.data.errors;
                }
            }
        },

        async changePassword(formData, toast) {
            try {
                await axiosInstance.post('/change-password', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast.add({
                    severity: 'success',
                    summary: 'Password changed successfully',
                    life: 3000
                });

                this.errors = {};
            } catch (err) {
                if (err.response?.data?.errors) {
                    this.errors = err.response.data.errors;
                } else if (err.response?.data?.message) {
                    toast.add({
                        severity: 'error',
                        summary: err.response.data.message,
                        life: 3000
                    });
                }
            }
        },

        async logoutOthers( toast) {
            try {
                await axiosInstance.post('/logout-others',
                    {},
                    {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast.add({
                    severity: 'success',
                    summary: 'Logout id done',
                    life: 3000
                });

                this.errors = {};
            } catch (err) {
                if (err.response?.data?.errors) {
                    this.errors = err.response.data.errors;
                } else if (err.response?.data?.message) {
                    toast.add({
                        severity: 'error',
                        summary: err.response.data.message,
                        life: 3000
                    });
                }
            }
        },

        async forgotPassword(formData, toast) {
            try {
                const res = await axiosInstance.post(`forgot-password`, formData);

                if (res.data.errors) {
                    this.errors = res.data.errors;
                } else {
                    toast.add({ severity: 'success', summary: 'Check your Email', life: 3000 });
                }
            } catch (err) {
                if (err.response?.data?.errors) {
                    this.errors = err.response.data.errors;
                }
            }
        },

        async resetPassword(formData, toast) {
            try {
                const res = await axiosInstance.post(`/reset-password`, formData);

                toast.add({
                    severity: 'success',
                    summary: res.data.status || 'Password reset successfully',
                    life: 3000
                });

                this.errors = {};
                return true;
            } catch (err) {
                if (err.response?.data?.errors) {
                    this.errors = err.response.data.errors;
                }
                return false;
            }
        },
    },
})
