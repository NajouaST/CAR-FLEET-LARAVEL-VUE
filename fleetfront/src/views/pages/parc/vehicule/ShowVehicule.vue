<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useRouter, useRoute } from "vue-router";
import { useParcStore } from "@/store/parc";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import VehiculeDocuments from "@/views/pages/parc/vehicule-documents/VehiculeDocuments.vue";

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
const isLoading = ref(true);

const carteGriseFrontPreview = ref(null);
const carteGriseBackPreview = ref(null);

onMounted(async () => {
    isLoading.value = true;
    marques.value = (await paramsParcStore.getMarques({}, toast)).data;
    familles.value = (await paramsGenereauxStore.getFamilleVehiculesName({}, toast)).data;
    fournisseurs.value = (await paramsGenereauxStore.getFournisseurs({}, toast)).data;

    const vehiculeId = route.params.id;
    const { data } = await parcStore.getVehiculeById(vehiculeId, toast);

    vehicule.value = data;

    carteGriseFrontPreview.value = data.carte_grise_front_url || null;
    carteGriseBackPreview.value = data.carte_grise_back_url || null;

    if (vehicule.value.marque?.id) {
        modeles.value = (await paramsParcStore.getModeles(
            { filters: { marque_id: { value: vehicule.value.marque.id, matchMode: "equals" } } },
            toast
        )).data;
    }

    isLoading.value = false;
});

const selectedModele = computed(() =>
    modeles.value.find(m => m.id === vehicule.value?.modele?.id) || null
);
</script>

<template>
    <div v-if="isLoading" class="card text-center my-10 min-h-80">
        <i class="pi pi-spin pi-spinner text-2xl"></i>
        <p>Chargement...</p>
    </div>

    <div v-else class="space-y-6">
        <div class="card p-6">
            <h2 class="text-xl font-bold mb-4">Détails Véhicule</h2>

            <!-- Carte grise numéro -->
            <div>
                <label class="block font-semibold mb-2">Numéro de Carte Grise</label>
                <InputText v-model="vehicule.carte_grise" class="w-full" readonly />
            </div>

            <!-- Carte grise images -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Recto</label>
                    <Image v-if="carteGriseFrontPreview" :src="carteGriseFrontPreview" class="w-40 rounded shadow" preview />
                    <p v-else class="text-gray-400 italic">Non fourni</p>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Verso</label>
                    <Image v-if="carteGriseBackPreview" :src="carteGriseBackPreview" class="w-40 rounded shadow" preview />
                    <p v-else class="text-gray-400 italic">Non fourni</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                <!-- Infos administratives -->
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations administratives</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Immatriculation</label>
                            <InputText v-model="vehicule.immatriculation" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Châssis</label>
                            <InputText v-model="vehicule.chassis" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">DMC</label>
                            <InputText :value="vehicule.dmc" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Famille Véhicule</label>
                            <InputText :value="vehicule.famille?.name" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Couleur</label>
                            <InputText v-model="vehicule.couleur" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Catégorie</label>
                            <InputText v-model="vehicule.categorie" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Situation</label>
                            <InputText :value="vehicule.situation" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Statut</label>
                            <InputText :value="vehicule.statut" class="w-full" readonly />
                        </div>
                    </div>
                </fieldset>

                <!-- Caractéristiques techniques -->
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Caractéristiques techniques</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Marque</label>
                            <InputText :value="vehicule.marque?.name" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Modèle</label>
                            <InputText :value="vehicule.modele?.name" class="w-full" readonly />
                        </div>
                        <p><b>Puissance (CV):</b> {{ selectedModele?.puissance_cv }}</p>
                        <p><b>Puissance DIN:</b> {{ selectedModele?.puissance_din }}</p>
                        <p><b>Places:</b> {{ selectedModele?.places }}</p>
                        <p><b>Poids Vide:</b> {{ selectedModele?.poids_vide }}</p>
                        <p><b>Charge Utile:</b> {{ selectedModele?.charge_utile }}</p>
                        <p><b>Charge Totale:</b> {{ selectedModele?.poids_tc }}</p>
                        <p><b>Cylindres:</b> {{ selectedModele?.cylindre }}</p>
                        <p><b>Carburant:</b> {{ selectedModele?.type_carburant?.name }}</p>
                        <p><b>Conso Moy:</b> {{ selectedModele?.consommation_moy }}</p>
                    </div>
                </fieldset>

                <!-- Détails acquisition -->
                <fieldset class="border p-4 rounded lg:col-span-2">
                    <legend class="font-bold">Détails d’acquisition</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Formule</label>
                            <InputText :value="vehicule.formule_achat" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Fournisseur</label>
                            <InputText :value="vehicule.fournisseur?.name" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Date Acquisition</label>
                            <InputText :value="vehicule.date" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Valeur</label>
                            <InputText :value="vehicule.valeur" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Loyer / Amortissement</label>
                            <InputText :value="vehicule.loyer" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Date Garantie</label>
                            <InputText :value="vehicule.date_garantie" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Km Garantie</label>
                            <InputText :value="vehicule.km_garantie" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Date Cession</label>
                            <InputText :value="vehicule.date_cession" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Valeur Cession</label>
                            <InputText :value="vehicule.valeur_cession" class="w-full" readonly />
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Documents -->
        <VehiculeDocuments :vehicule-id="vehicule.id" />
    </div>
</template>
