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
        // ---------- PERSONNELS ----------
        async getPersonnels(params = {}, toast) {
			try {
				const queryParams = this.buildUserApiQuery(params);
				const res = await axiosInstance.get("/personnels", {
					headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
					params: queryParams,
				});
				return res.data;
			} catch (err) {
				toast?.add({ severity: "error", summary: "Error", detail: "Failed to load personnels", life: 3000 });
				this.handleAuthorizationError(err, toast);
			}
		},

		async createPersonnel(formData, toast) {
			try {
				const res = await axiosInstance.post("/personnels", formData, {
					headers: { Authorization: `Bearer ${localStorage.getItem("token")}`},
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

		async updatePersonnel(id, formData, toast) {
			try {
				const res = await axiosInstance.put(`/personnels/${id}`, formData, {
					headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
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

		async deletePersonnel(id, toast) {
			try {
				const res = await axiosInstance.delete(`/personnels/${id}`, {
					headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
				});
				return res.data;
			} catch (err) {
				toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete personnel", life: 3000 });
				this.handleAuthorizationError(err, toast);
			}
		},

        async getPersonnelById(id, toast) {

            try {
                const res = await axiosInstance.get(`/personnels/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load personnel", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- PERSONNEL CREATION DROPDOWN LISTS ----------
        
        async getSocietes(toast) {
            try {
                const res = await axiosInstance.get("/societes-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load societes", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getDirections(toast) {
            try {
                const res = await axiosInstance.get("/directions-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load directions", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getFonctions(toast) {
            try {
                const res = await axiosInstance.get("/fonctions-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load fonctions", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getRegions(toast) {
            try {
                const res = await axiosInstance.get("/regions-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load regions", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getZones(toast) {
            try {
                const res = await axiosInstance.get("/zones-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load zones", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getSites(toast) {
            try {
                const res = await axiosInstance.get("/sites-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sites", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getDepartements(toast) {
            try {
                const res = await axiosInstance.get("/departements-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load departements", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getGrades(toast) {
            try {
                const res = await axiosInstance.get("/grades-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load grades", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getDivisions(toast) {
            try {
                const res = await axiosInstance.get("/divisions-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load divisions", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getCentreCouts(toast) {
            try {
                const res = await axiosInstance.get("/centre-couts-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load centre couts", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getSuperviseurs(toast) {
            try {
                const res = await axiosInstance.get("/personnels-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load superviseurs", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getCarteCarburants(toast) {
            try {
                const res = await axiosInstance.get("/carte-carburants-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load carte carburants", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async getUsers(toast) {
            try {
                const res = await axiosInstance.get("/users-names", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load users", life: 3000 });
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

        // ---------- SOCIETES ----------

        async getSocietes(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get("/societes", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                    params: queryParams,
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sociétés", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createSociete(formData, toast) {
            try {
                const res = await axiosInstance.post("/societes", formData, {
                    headers: { 
                        Authorization: `Bearer ${localStorage.getItem("token")}`,
                        'Content-Type': 'multipart/form-data'
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

        async updateSociete(id, formData, toast) {
            try {

                formData.append('_method', 'PUT');
                let headers = { 
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    
                };

                
                const res = await axiosInstance.post(`/societes/${id}`, formData, { headers });
                
                console.log('Store: Update response from backend:', res.data);
                this.errors = {};
                return res.data;
            } catch (err) {
                console.error('Store: Update error:', err);
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors;
                }
                this.handleAuthorizationError(err, toast);
                throw err;
            }
        },

        async deleteSociete(id, toast) {
            try {
                const res = await axiosInstance.delete(`/societes/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete société", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- SITES ----------

        async getSites(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get("/sites", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                    params: queryParams,
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sites", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createSite(formData, toast) {
            try {
                const res = await axiosInstance.post("/sites", formData, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to create site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },
        
        async updateSite(id, data, toast) {
            try {
                console.log('Store: Update site:', data);
                const res = await axiosInstance.put(`/sites/${id}`, data, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteSite(id, toast) {
            try {
                const res = await axiosInstance.delete(`/sites/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- DEPARTEMENTS ----------

        async getDepartements(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get("/departements", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                    params: queryParams,
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sites", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createDepartement(formData, toast) {
            try {
                const res = await axiosInstance.post("/departements", formData, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to create site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },
        
        async updateDepartement(id, data, toast) {
            try {
                const res = await axiosInstance.put(`/departements/${id}`, data, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteDepartement(id, toast) {
            try {
                const res = await axiosInstance.delete(`/departements/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        // ---------- DIRECTION ----------

        async getDirections(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get("/directions", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                    params: queryParams,
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sites", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createDirection(formData, toast) {
            try {
                const res = await axiosInstance.post("/directions", formData, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to create site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },
        
        async updateDirection(id, data, toast) {
            try {
                const res = await axiosInstance.put(`/directions/${id}`, data, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteDirection(id, toast) {
            try {
                const res = await axiosInstance.delete(`/directions/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },
        // ---------- DIRECTION ----------

        async getCentreCouts(params = {}, toast) {
            try {
                const queryParams = this.buildUserApiQuery(params);
                const res = await axiosInstance.get("/centre-couts", {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                    params: queryParams,
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to load sites", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },

        async createCentreCout(formData, toast) {
            try {
                const res = await axiosInstance.post("/centre-couts", formData, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to create site", life: 3000 });
                this.handleAuthorizationError(err, toast);
            }
        },
        
        async updateCentreCout(id, data, toast) {
            try {
                const res = await axiosInstance.put(`/centre-couts/${id}`, data, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                this.handleAuthorizationError(err, toast);
            }
        },

        async deleteCentreCout(id, toast) {
            try {
                const res = await axiosInstance.delete(`/centre-couts/${id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
                });
                return res.data;
            } catch (err) {
                toast?.add({ severity: "error", summary: "Error", detail: "Failed to delete site", life: 3000 });
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
