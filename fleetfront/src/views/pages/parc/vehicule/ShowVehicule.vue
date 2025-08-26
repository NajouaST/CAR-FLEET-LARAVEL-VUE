<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useParcStore } from "@/store/parc";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";

const router = useRouter();
const route = useRoute();

const parcStore = useParcStore();
const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();

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

    marques.value = (await paramsParcStore.getMarques({}, null)).data;
    familles.value = (await paramsGenereauxStore.getFamilleVehiculesName({}, null)).data;
    fournisseurs.value = (await paramsGenereauxStore.getFournisseurs({}, null)).data;

    const vehiculeId = route.params.id;
    const { data } = await parcStore.getVehiculeById(vehiculeId, null);

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

    carteGriseFrontPreview.value = data.carte_grise_front_url || null;
    carteGriseBackPreview.value = data.carte_grise_back_url || null;

    if (vehicule.value.marque_id) {
        modeles.value = (await paramsParcStore.getModeles(
            { filters: { marque_id: { value: vehicule.value.marque_id, matchMode: "equals" } } },
            null
        )).data;
    }

    isLoading.value = false;
});

const selectedModele = computed(() => modeles.value.find(m => m.id === vehicule.value.modele_id) || null);
const selectedMarque = computed(() => marques.value.find(m => m.id === vehicule.value.marque_id)?.name || '');
const selectedFamille = computed(() => familles.value.find(f => f.id === vehicule.value.famille_vehicule_id)?.name || '');
const selectedFournisseur = computed(() => fournisseurs.value.find(f => f.id === vehicule.value.fournisseur_id)?.name || '');
</script>

<template>
    <div v-if="isLoading" class="card text-center my-10">
        <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        <p>Loading...</p>
    </div>

    <div v-else class="card p-6">
        <h2 class="text-xl font-bold mb-4">Détails du Véhicule</h2>

        <!-- Carte grise -->
        <div class="mb-4">
            <strong>Numéro de Carte Grise:</strong> {{ vehicule.carte_grise }}
        </div>

        <!-- Carte grise images -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div v-if="carteGriseFrontPreview">
                <strong>Carte Grise - Recto:</strong>
                <Image :src="carteGriseFrontPreview" preview  alt="Carte grise recto" class="w-40 rounded shadow mt-2" />
            </div>
            <div v-if="carteGriseBackPreview">
                <strong>Carte Grise - Verso:</strong>
                <Image :src="carteGriseBackPreview" preview  alt="Carte grise verso" class="w-40 rounded shadow mt-2" />
            </div>
        </div>

        <!-- Identity -->
        <fieldset class="border p-4 rounded mb-4">
            <legend class="font-bold">Informations administratives</legend>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <p><strong>Immatriculation:</strong> {{ vehicule.immatriculation }}</p>
                <p><strong>Châssis:</strong> {{ vehicule.chassis }}</p>
                <p><strong>DMC:</strong> {{ vehicule.dmc?.toLocaleDateString() }}</p>
                <p><strong>Famille Véhicule:</strong> {{ selectedFamille }}</p>
                <p><strong>Couleur:</strong> {{ vehicule.couleur }}</p>
                <p><strong>Catégorie:</strong> {{ vehicule.categorie }}</p>
                <p><strong>Situation:</strong> {{ vehicule.situation }}</p>
                <p><strong>Statut:</strong> {{ vehicule.statut }}</p>
            </div>
        </fieldset>

        <!-- Specs -->
        <fieldset class="border p-4 rounded mb-4">
            <legend class="font-bold">Caractéristiques techniques</legend>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <p><strong>Marque:</strong> {{ selectedMarque }}</p>
                <p><strong>Modèle:</strong> {{ selectedModele?.name }}</p>
                <p><strong>Puissance (CV):</strong> {{ selectedModele?.puissance_cv }}</p>
                <p><strong>Puissance DIN:</strong> {{ selectedModele?.puissance_din }}</p>
                <p><strong>Nb Places:</strong> {{ selectedModele?.places }}</p>
                <p><strong>Poids Vide:</strong> {{ selectedModele?.poids_vide }}</p>
                <p><strong>Charge Utile:</strong> {{ selectedModele?.charge_utile }}</p>
                <p><strong>Charge Tc:</strong> {{ selectedModele?.poids_tc }}</p>
                <p><strong>Cylindres:</strong> {{ selectedModele?.cylindre }}</p>
                <p><strong>Type Carburant:</strong> {{ selectedModele?.type_carburant?.name }}</p>
                <p><strong>Consommation min:</strong> {{ selectedModele?.consommation_min }}</p>
                <p><strong>Consommation max:</strong> {{ selectedModele?.consommation_max }}</p>
                <p><strong>Consommation moy:</strong> {{ selectedModele?.consommation_moy }}</p>
            </div>
        </fieldset>

        <!-- Acquisition -->
        <fieldset class="border p-4 rounded mb-4">
            <legend class="font-bold">Détails d’acquisition</legend>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <p><strong>Formule:</strong> {{ vehicule.formule_achat }}</p>
                <p><strong>Fournisseur:</strong> {{ selectedFournisseur }}</p>
                <p><strong>Date d’acquisition:</strong> {{ vehicule.date?.toLocaleDateString() }}</p>
                <p><strong>Valeur:</strong> {{ vehicule.valeur }}</p>
                <p v-if="vehicule.loyer"><strong>Loyer/Amortissement:</strong> {{ vehicule.loyer }}</p>
                <p><strong>Date Garantie:</strong> {{ vehicule.date_garantie?.toLocaleDateString() }}</p>
                <p><strong>Km Garantie:</strong> {{ vehicule.km_garantie }}</p>
                <p><strong>Date Cession:</strong> {{ vehicule.date_cession?.toLocaleDateString() }}</p>
                <p><strong>Valeur Cession:</strong> {{ vehicule.valeur_cession }}</p>
            </div>
        </fieldset>

        <div class="flex justify-end mt-6">
            <Button label="Retour" severity="secondary" @click="router.push({ name: 'vehicules' })" />
        </div>
    </div>
</template>
