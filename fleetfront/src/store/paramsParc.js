import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";
export const useParamsParcStore = defineStore('paramsParcStore', {
    state: () =>{
        return {
            errors :{}
        }
    },
    actions: {
        // ---------- MARQUES ----------
        async getMarques(params = {},toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/marques', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load marques', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createMarques(formData, toast) {
            try {
                 const res = await axiosInstance.post('/marques', formData, {
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

        async updateMarques(marqueId, formData, toast) {
            try {
                formData.append('_method', 'PUT'); // <-- trick Laravel into treating it as PUT

                const res = await axiosInstance.post(`/marques/${marqueId}`, formData, {
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

        async getMarqueById(marqueId, toast) {
            try {
                const res = await axiosInstance.get(`/marques/${marqueId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load marque', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteMarque(marqueId, toast) {
            try {
                const res = await axiosInstance.delete(`/marques/${marqueId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete marque', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- MODELES ----------
        async getModeles(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/modeles', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load modeles', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async getModelesName(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/modeles-names', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load modeles', life: 3000 });
                this.handleAuthorizationError(err,toast);
            }
        },

        async createModeles(formData, toast) {
            try {
                const res = await axiosInstance.post('/modeles', formData, {
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

        async updateModeles(modeleId, formData, toast) {
            try {
                const res = await axiosInstance.put(`/modeles/${modeleId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getModeleById(modeleId, toast) {
            try {
                const res = await axiosInstance.get(`/modeles/${modeleId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load modelee', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteModele(modeleId, toast) {
            try {
                const res = await axiosInstance.delete(`/modeles/${modeleId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete modele', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- GAMMES ----------
        async getGammes(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get('/gammes', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    params: queryParams
                });

                return res.data;
            } catch (err) {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load gammes', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createGammes(formData, toast) {
            try {
                const res = await axiosInstance.post('/gammes', formData, {
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

        async updateGammes(gammeId, formData, toast) {
            try {
                const res = await axiosInstance.put(`/gammes/${gammeId}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getGammeById(gammeId, toast) {
            try {
                const res = await axiosInstance.get(`/gammes/${gammeId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to load gamme', life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteGamme(gammeId, toast) {
            try {
                const res = await axiosInstance.delete(`/gammes/${gammeId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete gamme', life: 3000 });
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
