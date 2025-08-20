import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";
export const useAdminStore = defineStore('adminStore', {
    state: () =>{
        return {
            userslist : null,
            roleslist : null,
            permissionslist : null,
            permissionsNameslist : null,
            errors :{}
        }
    },
    actions: {

        // ---------- USERS ----------
        async getUsers(params = {}) {
            if (!this.checkTokenAndRedirect()) return;

            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/users', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });
                this.userslist = res.data;
            } catch (err) {
                this.goLogin(err)
                this.checkTokenAndRedirect();
            }
        },

        async createUsers(formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

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
                this.goLogin(err)
            }
        },

        async updateUsers(userId, formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

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
                this.goLogin(err)
            }
        },

        // ---------- ROLES ----------
        async getRoles(params = {}) {
            if (!this.checkTokenAndRedirect()) return;

            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/roles', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                this.roleslist = res.data;
            } catch (err) {
                this.goLogin(err)
                this.checkTokenAndRedirect();
            }
        },

        async createRoles(formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

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
                this.goLogin(err)
            }
        },

        async updateRoles(roleId, formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

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
                this.goLogin(err)
            }
        },

        // ---------- PERMISSIONS ----------
        async getPermissions(params = {}) {
            if (!this.checkTokenAndRedirect()) return;

            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/permissions', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                this.permissionslist = res.data;
            } catch (err) {
                this.goLogin(err)
                this.checkTokenAndRedirect();
            }
        },

        async getPermissionsNameList() {
            if (!this.checkTokenAndRedirect()) return;
            try {
                const res = await axiosInstance.get('/permissions-names', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                this.permissionsNameslist = res.data;
            } catch (err) {
                this.goLogin(err)
                this.checkTokenAndRedirect();
            }
        },

        async createPermissions(formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

            try {
                const res = await axiosInstance.post('/permissions', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'Permission created successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.goLogin(err)
            }
        },

        async updatePermissions(permissionId, formData, toast) {
            if (!this.checkTokenAndRedirect()) return;

            try {
                const res = await axiosInstance.put(`/permissions/${permissionId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                toast?.add({ severity: 'success', summary: 'Permission updated successfully', life: 3000 });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.goLogin(err)
            }
        },


        // ---------- Utility ----------
        checkTokenAndRedirect() {
            const token = localStorage.getItem('token');

            if (!token) {
                this.resetAllData();
                this.router?.push?.({ name: 'login' });
                return false;
            }

            return true;
        },

        resetAllData() {
            this.userslist = null;
            this.roleslist = null;
            this.permissionslist = null;
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

        goLogin(error) {
            if (error.response?.data?.message === 'Unauthenticated.') {
                localStorage.removeItem('token');
                router.push("/login");
            }
        },
    }
});
