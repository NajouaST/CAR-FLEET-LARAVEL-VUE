<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useRouter, useRoute } from "vue-router";
import { useParcStore } from "@/store/parc";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";

const toast = useToast();
const router = useRouter();
const route = useRoute();

const parcStore = useParcStore();
const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const vehicule = ref({});
const modeles = ref([]);
const marques = ref([]);
const familles = ref([]);
const fournisseurs = ref([]);
const saving = ref(false);
const isLoading = ref(true);

// validation state
const errors = ref({});

// image previews
const carteGriseFrontPreview = ref(null);
const carteGriseBackPreview = ref(null);

// dropdown options
const options = {
    situation: [
        { label: "En exploitation", value: "en_exploitation" },
        { label: "En réparation", value: "en_reparation" },
        { label: "Accidentée", value: "accidentee" },
        { label: "Arrêtée", value: "arretee" },
        { label: "Litige", value: "litige" },
        { label: "Épave", value: "epave" },
        { label: "Vendue", value: "vendue" },
    ],
    statut: [
        { label: "Libre", value: "libre" },
        { label: "Affectée", value: "affectee" },
        { label: "Hors service", value: "hors_service" },
        { label: "Vendue", value: "vendue" },
    ],
    formuleAchat: [
        { label: "Fond propre", value: "fond_propre" },
        { label: "Leasing", value: "leasing" },
        { label: "LLD", value: "LLD" },
    ],
};

onMounted(async () => {
    isLoading.value = true;
    marques.value = (await paramsParcStore.getMarques({}, toast)).data;
    familles.value = (await paramsGenereauxStore.getFamilleVehiculesName({}, toast)).data;
    fournisseurs.value = (await paramsGenereauxStore.getFournisseurs({}, toast)).data;

    // Load vehicule
    const vehiculeId = route.params.id;
    const { data } = await parcStore.getVehiculeById(vehiculeId, toast);

    // Map API data to form model
    vehicule.value = {
        ...data,
        marque_id: data.marque?.id || null,
        modele_id: data.modele?.id || null,
        famille_vehicule_id: data.famille?.id || null,
        fournisseur_id: data.fournisseur?.id || null,
        dmc: data.dmc ? new Date(data.dmc) : null,
        date: data.date ? new Date(data.date) : null,
        date_garantie: data.date_garantie ? new Date(data.date_garantie) : null,
        date_cession: data.date_cession ? new Date(data.date_cession) : null,
    };

    // Set image previews
    carteGriseFrontPreview.value = data.carte_grise_front_url || null;
    carteGriseBackPreview.value = data.carte_grise_back_url || null;

    // Load modeles for the selected marque
    if (vehicule.value.marque_id) {
        modeles.value = (await paramsParcStore.getModeles(
            { filters: { marque_id: { value: vehicule.value.marque_id, matchMode: "equals" } } },
            toast
        )).data;
    }
    isLoading.value = false;
});

// Watch marque changes
watch(() => vehicule.value.marque_id, async (newMarqueId) => {
    if (newMarqueId) {
        modeles.value = (await paramsParcStore.getModeles(
            { filters: { marque_id: { value: newMarqueId, matchMode: "equals" } } },
            toast
        )).data;
    } else {
        modeles.value = [];
        vehicule.value.modele_id = null;
    }
},
    () => vehicule.value.situation, (newSituation) => {
    if (newSituation === "en_exploitation") {
        vehicule.value.statut = "libre"; // keep default libre for now
    } else if (newSituation === "vendue") {
        vehicule.value.statut = "vendue";
    } else {
        vehicule.value.statut = "hors_service";
    }
});

// Watch formule_achat to reset loyer if fond_propre
watch(() => vehicule.value.formule_achat, (newVal) => {
    if (newVal === "fond_propre") vehicule.value.loyer = null;
});

const selectedModele = computed(() => modeles.value.find(m => m.id === vehicule.value.modele_id) || null);

// File uploads
function handleFrontUpload(event) {
    const file = event.files[0];
    vehicule.value.carte_grise_front = file;
    carteGriseFrontPreview.value = URL.createObjectURL(file);
}

function handleBackUpload(event) {
    const file = event.files[0];
    vehicule.value.carte_grise_back = file;
    carteGriseBackPreview.value = URL.createObjectURL(file);
}

// Validation
function validateForm() {
    errors.value = {};
    if (!vehicule.value.immatriculation) errors.value.immatriculation = "Immatriculation requise";
    if (!vehicule.value.chassis) errors.value.chassis = "Châssis requis";
    if (!vehicule.value.dmc) errors.value.dmc = "Date de mise en circulation requise";
    if (!vehicule.value.carte_grise) errors.value.carte_grise = "Numéro de Carte Grise requis";
    if (!vehicule.value.famille_vehicule_id) errors.value.famille_vehicule_id = "Famille de véhicule requise";
    if (!vehicule.value.situation) errors.value.situation = "Situation requise";
    if (!vehicule.value.marque_id) errors.value.marque_id = "Marque requise";
    if (!vehicule.value.modele_id) errors.value.modele_id = "Modèle requis";
    if (!vehicule.value.formule_achat) errors.value.formule_achat = "Formule d’achat requise";
    if (!vehicule.value.date) errors.value.date = "Date d’acquisition requise";
    if (!vehicule.value.valeur) errors.value.valeur = "Valeur requise";
    if (!vehicule.value.statut) errors.value.statut = "Statut requis";

    return Object.keys(errors.value).length === 0;
}

// Update vehicule
async function updateVehicule() {
    if (!validateForm()) {
        toast.add({ severity: "error", summary: "Erreur", detail: "Veuillez remplir tous les champs obligatoires", life: 4000 });
        return;
    }

    saving.value = true;
    try {
        const formData = new FormData();
        Object.keys(vehicule.value).forEach((key) => {
            let val = vehicule.value[key];
            if (val instanceof Date) val = val.toISOString().split("T")[0];
            if (key !== "carte_grise_front" && key !== "carte_grise_back") formData.append(key, val ?? "");
        });
        if (vehicule.value.carte_grise_front) formData.append("carte_grise_front", vehicule.value.carte_grise_front);
        if (vehicule.value.carte_grise_back) formData.append("carte_grise_back", vehicule.value.carte_grise_back);

        await parcStore.updateVehicule(vehicule.value.id, formData, toast);
        toast.add({ severity: "success", summary: "Succès", detail: "Véhicule mis à jour avec succès", life: 3000 });
        router.push({ name: "vehicules" });
    } catch (err) {
        console.log(err);
    } finally {
        saving.value = false;
    }
}
</script>

<template>

    <div v-if="isLoading" class="card text-center my-10">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p>Loading...</p>
    </div>
    <div v-else class="card p-6">
        <h2 class="text-xl font-bold mb-4">Modifier Véhicule</h2>
        <form @submit.prevent="updateVehicule" class="space-y-6">
            <!-- Carte grise -->
            <div>
                <label class="block font-semibold mb-2">Numéro de Carte Grise *</label>
                <InputText v-model="vehicule.carte_grise" placeholder="Numéro de carte grise" class="w-full" />
                <small v-if="errors.carte_grise" class="text-red-500">{{ errors.carte_grise }}</small>
            </div>

            <!-- Carte grise images -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Recto</label>
                    <FileUpload mode="basic" accept="image/*" customUpload :auto="true" @uploader="handleFrontUpload" />
                    <div v-if="carteGriseFrontPreview" class="mt-2">
                        <Image :src="carteGriseFrontPreview" alt="Carte grise recto" class="w-40 rounded shadow" preview  />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Verso</label>
                    <FileUpload mode="basic" accept="image/*" customUpload :auto="true" @uploader="handleBackUpload" />
                    <div v-if="carteGriseBackPreview" class="mt-2">
                        <Image :src="carteGriseBackPreview" alt="Carte grise verso" class="w-40 rounded shadow" preview />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Identity -->
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations administratives</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Immatriculation *</label>
                            <InputText v-model="vehicule.immatriculation" class="w-full" />
                            <small v-if="errors.immatriculation" class="text-red-500">{{ errors.immatriculation }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Châssis *</label>
                            <InputText v-model="vehicule.chassis" class="w-full" />
                            <small v-if="errors.chassis" class="text-red-500">{{ errors.chassis }}</small>
                        </div>
                        <div>
                            <label class="block m-1">DMC *</label>
                            <DatePicker v-model="vehicule.dmc" dateFormat="yy-mm-dd" class="w-full" />
                            <small v-if="errors.dmc" class="text-red-500">{{ errors.dmc }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Famille Véhicule *</label>
                            <Select
                                v-model="vehicule.famille_vehicule_id"
                                :options="familles"
                                optionLabel="name"
                                optionValue="id"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                            <small v-if="errors.famille_vehicule_id" class="text-red-500">{{ errors.famille_vehicule_id }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Couleur</label>
                            <InputText v-model="vehicule.couleur" class="w-full" />
                        </div>
                        <div>
                            <label class="block m-1">Catégorie</label>
                            <InputText v-model="vehicule.categorie" class="w-full" />
                        </div>

                        <Divider align="left" type="solid" class="col-span-2">
                            <b>Etats</b>
                        </Divider>
                        <div>
                            <label class="block m-1">Situation *</label>
                            <Select
                                v-model="vehicule.situation"
                                :options="options.situation"
                                optionLabel="label"
                                optionValue="value"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                            <small v-if="errors.situation" class="text-red-500">{{ errors.situation }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Statut *</label>
                            <Select
                                v-model="vehicule.statut"
                                :options="options.statut"
                                optionLabel="label"
                                optionValue="value"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                            <small v-if="errors.statut" class="text-red-500">{{ errors.statut }}</small>
                        </div>
                    </div>
                </fieldset>

                <!-- Specs -->
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Caractéristiques techniques</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <!-- Marque -->
                        <div>
                            <label class="block m-1">Marque *</label>
                            <Select
                                v-model="vehicule.marque_id"
                                :options="marques"
                                optionLabel="name"
                                optionValue="id"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                            <small v-if="errors.marque_id" class="text-red-500">{{ errors.marque_id }}</small>
                        </div>
                        <!-- Modèle -->
                        <div>
                            <label class="block m-1">Modèle *</label>
                            <Select
                                v-model="vehicule.modele_id"
                                :options="modeles"
                                optionLabel="name"
                                optionValue="id"
                                :disabled="!vehicule.marque_id"
                                checkmark
                                placeholder="Choisir la marque d'abord"
                                class="w-full"
                            />
                            <small v-if="errors.modele_id" class="text-red-500">{{ errors.modele_id }}</small>
                        </div>
                        <p><strong>Puissance (CV):</strong> {{ selectedModele?.puissance_cv }}</p>
                        <p><strong>Puissance DIN:</strong> {{ selectedModele?.puissance_din }}</p>
                        <p><strong>Nbr Places:</strong> {{ selectedModele?.places }}</p>
                        <p><strong>Poids Vide:</strong> {{ selectedModele?.poids_vide }}</p>
                        <p><strong>Charge Utile:</strong> {{ selectedModele?.charge_utile }}</p>
                        <p><strong>Charge Tc:</strong> {{ selectedModele?.poids_tc }}</p>
                        <p><strong>Cylindres:</strong> {{ selectedModele?.cylindre }}</p>
                        <p><strong>Type Carburant:</strong> {{ selectedModele?.type_carburant.name }}</p>
                        <p><strong>Consommation min:</strong> {{ selectedModele?.consommation_min }}</p>
                        <p><strong>Consommation max:</strong> {{ selectedModele?.consommation_max }}</p>
                        <p><strong>Consommation moy:</strong> {{ selectedModele?.consommation_moy }}</p>
                    </div>
                </fieldset>


                <!-- Achat -->
                <fieldset class="border p-4 rounded lg:col-span-2">
                    <legend class="font-bold">Détails d’acquisition</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Formule *</label>
                            <Select
                                v-model="vehicule.formule_achat"
                                :options="options.formuleAchat"
                                optionLabel="label"
                                optionValue="value"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                            <small v-if="errors.formule_achat" class="text-red-500">{{ errors.formule_achat }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Fournisseur</label>
                            <Select
                                v-model="vehicule.fournisseur_id"
                                :options="fournisseurs"
                                optionLabel="name"
                                optionValue="id"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Date d’acquisition *</label>
                            <Calendar v-model="vehicule.date" dateFormat="yy-mm-dd" class="w-full" />
                            <small v-if="errors.date" class="text-red-500">{{ errors.date }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Valeur *</label>
                            <InputNumber v-model="vehicule.valeur" class="w-full" mode="decimal" />
                            <small v-if="errors.valeur" class="text-red-500">{{ errors.valeur }}</small>
                        </div>
                        <div v-if="vehicule.formule_achat !== 'fond_propre'">
                            <label class="block m-1">
                                {{ vehicule.formule_achat === 'leasing' ? 'Amortissement' : 'Loyer' }}
                            </label>
                            <InputNumber
                                v-model="vehicule.loyer"
                                class="w-full"
                                mode="decimal"
                                :placeholder="vehicule.formule_achat === 'leasing' ? 'Amortissement' : 'Loyer'"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Date Garantie</label>
                            <Calendar v-model="vehicule.date_garantie" dateFormat="yy-mm-dd" class="w-full" />
                        </div>
                        <div>
                            <label class="block m-1">Km Garantie</label>
                            <InputNumber v-model="vehicule.km_garantie" class="w-full" mode="decimal" />
                        </div>
                        <Divider align="left" type="solid" class="col-span-2">
                            <b>Informations de cession</b>
                        </Divider>
                        <div>
                            <label class="block m-1">Date Cession</label>
                            <Calendar v-model="vehicule.date_cession" dateFormat="yy-mm-dd" class="w-full" />
                        </div>
                        <div>
                            <label class="block m-1">Valeur Cession</label>
                            <InputNumber v-model="vehicule.valeur_cession" class="w-full" mode="decimal" />
                        </div>
                    </div>
                </fieldset>

            </div>

            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Annuler" severity="secondary" @click="router.push({ name: 'vehicules' })" />
                <Button type="submit" label="Mettre à jour" :loading="saving" />
            </div>
        </form>
    </div>
</template>
