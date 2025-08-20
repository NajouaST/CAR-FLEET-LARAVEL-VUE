import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";
export const useRoleStore = defineStore('roleStore', {
    state: () =>{
        return {
            errors :{}
        }
    },
    actions: {
        // ---------- ROLES ----------
        async getRoles(params = {},toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/roles', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load roles', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createRoles(formData, toast) {
            try {
                const res = await axiosInstance.post('/roles', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'Role created successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err);
            }
        },

        async updateRoles(roleId, formData, toast) {
            try {
                const res = await axiosInstance.put(`/roles/${roleId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'Role updated successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err);
            }
        },

        async getRoleById(roleId, toast) {
            try {
                const res = await axiosInstance.get(`/roles/${roleId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load role', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getPermissionsNameList() {
            try {
                const res = await axiosInstance.get('/permissions-names', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err);
            }
        },

        async deleteRole(roleId, toast) {
            try {
                const res = await axiosInstance.delete(`/roles/${roleId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete role', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- UTILITIES ----------

        /**
         * Handles unauthorized responses globally.
         */
        handleAuthorizationError(error,toast) {
            const message = error?.response?.data?.message;

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
