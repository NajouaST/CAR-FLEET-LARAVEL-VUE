import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";
export const useParcStore = defineStore('parcStore', {
    state: () =>{
        return {
            errors :{}
        }
    },
    actions: {
        // ---------- VEHICULES ----------
        async getVehicules(params = {},toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/vehicules', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load vehicules', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createVehicule(formData, toast) {
            try {
                const res = await axiosInstance.post('/vehicules', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors;
                }
                this.handleAuthorizationError(err, toast);
                throw err;
            }
        },

        async updateVehicule(id, formData, toast) {
            try {
                formData.append('_method', 'PUT'); // <-- trick Laravel into treating it as PUT

                const res = await axiosInstance.post(`/vehicules/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getVehiculeById(id, toast) {
            try {
                const res = await axiosInstance.get(`/vehicules/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load vehicule', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteVehicule(marqueId, toast) {
            try {
                const res = await axiosInstance.delete(`/vehicules/${marqueId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete vehicule', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- DOC VEHICULES ----------
        async getDocVehicules(params = {},toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/document-vehicules', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load document vehicules', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createDocVehicule(formData, toast) {
            try {
                const res = await axiosInstance.post('/document-vehicules', formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors;
                }
                this.handleAuthorizationError(err, toast);
                throw err;
            }
        },

        async updateDocVehicule(id, formData, toast) {
            try {
                formData.append('_method', 'PUT'); // <-- trick Laravel into treating it as PUT

                const res = await axiosInstance.post(`/document-vehicules/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteDocVehicule(id, toast) {
            try {
                const res = await axiosInstance.delete(`/document-vehicules/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete document vehicule', life: 3000 });
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
