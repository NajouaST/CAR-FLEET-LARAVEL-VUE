import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";
export const useUserStore = defineStore('userStore', {
    state: () =>{
        return {
            errors :{}
        }
    },
    actions: {
        // ---------- USERS ----------
        async getUsers(params = {},toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get('/users', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load users', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createUsers(formData, toast) {
            try {
                const res = await axiosInstance.post('/users', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                toast?.add({ severity: 'success', summary: 'User created successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.errors = err.response.data.errors;
                this.handleAuthorizationError(err);
            }
        },

        async updateUsers(userId, formData, toast) {
            try {
                const res = await axiosInstance.put(`/users/${userId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'User updated successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.errors = err.response.data.errors;
                this.handleAuthorizationError(err);
            }
        },

        async restoreUsers(userId, formData, toast) {
            try {
                const res = await axiosInstance.post(`/users/restore/${userId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'User restored successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err);
            }
        },

        async getUserById(userId, toast) {
            try {
                const res = await axiosInstance.get(`/users/${userId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load user', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getRolesNameList() {
            try {
                const res = await axiosInstance.get('/roles-names', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err);
            }
        },

        async deleteUser(userId, toast) {
            try {
                const res = await axiosInstance.delete(`/users/${userId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete user', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async permaDeleteUser(userId, toast) {
            try {
                const res = await axiosInstance.delete(`/users/force/${userId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete user', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- UTILITIES ----------

        /**
         * Handles unauthorized responses globally.
         */
        handleAuthorizationError(error,toast) {
            const message = error?.response?.data?.message;
            console.log(error.response?.data?.message)

            if (message === 'No Access') {
                this.resetAllData();
                router.push({ name: 'accessDenied' });
            }

            if (message === 'Unauthenticated.' || message === 'Unauthorized.') {
                localStorage.removeItem('token');
                this.resetAllData();
                toast.add({ severity: 'warn', summary: 'Session expired', life: 3000 });
                router.push({ name: 'login' });
            }
        },

        resetAllData() {
            this.errors = {};
        },

        buildUserApiQuery(params) {
            const query = {};

            for (const [field, filter] of Object.entries(params.filters || {})) {
                const value = filter.value;
                const mode = filter.matchMode;

                if (value !== null && value !== undefined && value !== "") {
                    query[`${field}__${mode}`] = value;
                }
            }

            if (params.first !== undefined) query.first = params.first;
            if (params.rows !== undefined) query.rows = params.rows;
            if (params.sortField) query.sortField = params.sortField;
            if (params.sortOrder !== undefined) query.sortOrder = params.sortOrder;

            return query;
        },
    }
});
