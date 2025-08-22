import { defineStore } from "pinia";
import axiosInstance from "@/lib/axios";
import router from "@/router";

export const useParamsGenereauxStore = defineStore("paramsGenereauxStore", {
    state: () => ({
        errors: {}
    }),
    actions: {
        // ---------- TYPE COMPTEURS ----------
        async getTypeCompteurs(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/type-compteurs", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load compteurs", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createTypeCompteur(formData, toast) {
            try {
                const res = await axiosInstance.post("/type-compteurs", formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
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

        async updateTypeCompteur(id, formData, toast) {
            try {
                const res = await axiosInstance.put(`/type-compteurs/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getTypeCompteurById(id, toast) {
            try {
                const res = await axiosInstance.get(`/type-compteurs/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load compteur", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteTypeCompteur(id, toast) {
            try {
                const res = await axiosInstance.delete(`/type-compteurs/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete compteur", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- TYPE CARBURANTS ----------
        async getTypeCarburants(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/type-carburants", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load carburants", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createTypeCarburant(formData, toast) {
            try {
                const res = await axiosInstance.post("/type-carburants", formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
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

        async updateTypeCarburant(id, formData, toast) {
            try {
                const res = await axiosInstance.put(`/type-carburants/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getTypeCarburantById(id, toast) {
            try {
                const res = await axiosInstance.get(`/type-carburants/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load carburant", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteTypeCarburant(id, toast) {
            try {
                const res = await axiosInstance.delete(`/type-carburants/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete carburant", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- TYPE CARBURANTS ----------
        async getFamilleVehicules(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/famille-vehicules", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load familles vehicule", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getFamilleVehiculesName(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/famille-vehicules-names", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load fournisseurs", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createFamilleVehicule(formData, toast) {
            try {
                const res = await axiosInstance.post("/famille-vehicules", formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
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

        async updateFamilleVehicule(id, formData, toast) {
            try {
                const res = await axiosInstance.put(`/famille-vehicules/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getFamilleVehiculeById(id, toast) {
            try {
                const res = await axiosInstance.get(`/famille-vehicules/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load famille vehicule", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteFamilleVehicule(id, toast) {
            try {
                const res = await axiosInstance.delete(`/famille-vehicules/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete famille vehicule", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- TYPE CARBURANTS ----------
        async getFournisseurs(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/fournisseurs", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load fournisseurs", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getFournisseurName(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);

                const res = await axiosInstance.get("/fournisseurs-names", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                    params: queryParams,
                });

                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load fournisseurs", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createFournisseur(formData, toast) {
            try {
                const res = await axiosInstance.post("/fournisseurs", formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
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

        async updateFournisseur(id, formData, toast) {
            try {
                const res = await axiosInstance.put(`/fournisseurs/${id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                this.errors = {};
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async getFournisseurById(id, toast) {
            try {
                const res = await axiosInstance.get(`/fournisseurs/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load fournisseur", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteFournisseur(id, toast) {
            try {
                const res = await axiosInstance.delete(`/fournisseurs/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                    },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete fournisseur", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- UTILITIES ----------
        handleAuthorizationError(error, toast) {
            const message = error?.response?.data?.message;

            if (message === "Unauthenticated." || message === "Unauthorized.") {
                localStorage.removeItem("token");
                this.resetAllData();
                toast?.add({ severity: "warn", summary: "Session expired", life: 3000 });
                router.push({ name: "login" });
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
    },
});
