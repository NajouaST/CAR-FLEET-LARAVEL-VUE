<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { useRouter } from "vue-router";
import { useParcStore } from "@/store/parc";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";

const toast = useToast();
const router = useRouter();

const parcStore = useParcStore();
const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const vehicule = ref({});
const modeles = ref([]);
const familles = ref([]);
const fournisseurs = ref([]);
const saving = ref(false);

// image previews
const carteGriseFrontPreview = ref(null);
const carteGriseBackPreview = ref(null);

onMounted(async () => {
    modeles.value = (await paramsParcStore.getModelesName({}, toast));
    familles.value = (await paramsGenereauxStore.getFamilleVehiculesName({}, toast)).data;
    fournisseurs.value = (await paramsGenereauxStore.getFournisseurs({}, toast)).data;
});

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

async function saveVehicule() {
    saving.value = true;
    try {
        const formData = new FormData();

        Object.keys(vehicule.value).forEach((key) => {
            let val = vehicule.value[key];

            // convertir les dates
            if (val instanceof Date) {
                val = val.toISOString().split("T")[0];
            }

            if (key !== "carte_grise_front" && key !== "carte_grise_back") {
                formData.append(key, val ?? "");
            }
        });

        // fichiers
        if (vehicule.value.carte_grise_front) {
            formData.append("carte_grise_front", vehicule.value.carte_grise_front);
        }
        if (vehicule.value.carte_grise_back) {
            formData.append("carte_grise_back", vehicule.value.carte_grise_back);
        }

        await parcStore.createVehicule(formData, toast);
        toast.add({ severity: "success", summary: "Succès", detail: "Véhicule créé avec succès", life: 3000 });
        router.push({ name: "vehicules" });
    } catch (err) {
        console.error(err);
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="card p-6">
        <h2 class="text-xl font-bold mb-4">Créer un Véhicule</h2>

        <form @submit.prevent="saveVehicule" class="space-y-6">
            <!-- Carte grise (string) -->
            <div>
                <label class="block font-semibold mb-2">Numéro de Carte Grise</label>
                <InputText v-model="vehicule.carte_grise" placeholder="Numéro de carte grise" class="w-full" />
            </div>

            <!-- Carte grise images -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Recto</label>
                    <FileUpload mode="basic" accept="image/*" customUpload :auto="true" @uploader="handleFrontUpload" />
                    <div v-if="carteGriseFrontPreview" class="mt-2">
                        <img :src="carteGriseFrontPreview" alt="Carte grise recto" class="w-40 rounded shadow" />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold mb-2">Carte Grise - Verso</label>
                    <FileUpload mode="basic" accept="image/*" customUpload :auto="true" @uploader="handleBackUpload" />
                    <div v-if="carteGriseBackPreview" class="mt-2">
                        <img :src="carteGriseBackPreview" alt="Carte grise verso" class="w-40 rounded shadow" />
                    </div>
                </div>
            </div>

            <!-- Identity -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Identité</legend>
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Immatriculation</label>
                        <InputText v-model="vehicule.immatriculation" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Châssis</label>
                        <InputText v-model="vehicule.chassis" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">DMC</label>
                        <Calendar v-model="vehicule.dmc" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                </div>
            </fieldset>

            <!-- Specs -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Caractéristiques</legend>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Couleur</label>
                        <InputText v-model="vehicule.couleur" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Catégorie</label>
                        <InputText v-model="vehicule.categorie" class="w-full" />
                    </div>
                </div>
            </fieldset>

            <!-- Situation -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Situation</legend>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Situation</label>
                        <Dropdown v-model="vehicule.situation"
                                  :options="[
                        {label: 'En exploitation', value: 'en_exploitation'},
                        {label: 'En réparation', value: 'en_reparation'},
                        {label: 'Accidentée', value: 'accidentee'},
                        {label: 'Arrêtée', value: 'arretee'},
                        {label: 'Litige', value: 'litige'},
                        {label: 'Épave', value: 'epave'},
                        {label: 'Vendue', value: 'vondue'},
                      ]"
                                  optionLabel="label" optionValue="value"
                                  placeholder="Choisir" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Statut</label>
                        <Dropdown v-model="vehicule.statut"
                                  :options="[
                        {label: 'Libre', value: 'libre'},
                        {label: 'Affectée', value: 'affectee'},
                        {label: 'Hors service', value: 'hors_service'},
                        {label: 'Vendue', value: 'vondue'},
                      ]"
                                  optionLabel="label" optionValue="value"
                                  placeholder="Choisir" class="w-full" />
                    </div>
                </div>
            </fieldset>

            <!-- Achat -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Achat</legend>
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Formule</label>
                        <Dropdown v-model="vehicule.formule_achat"
                                  :options="[
                        {label: 'Fond propre', value: 'fond propre'},
                        {label: 'Leasing', value: 'leasing'},
                        {label: 'LLD', value: 'LLD'},
                      ]"
                                  optionLabel="label" optionValue="value"
                                  placeholder="Choisir" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Date</label>
                        <Calendar v-model="vehicule.date" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Valeur</label>
                        <InputNumber v-model="vehicule.valeur" class="w-full" mode="decimal" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Loyer</label>
                        <InputNumber v-model="vehicule.loyer" class="w-full" mode="decimal" />
                    </div>
                    <div>
                        <label class="block mb-1">Date Garantie</label>
                        <Calendar v-model="vehicule.date_garantie" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Km Garantie</label>
                        <InputNumber v-model="vehicule.km_garantie" class="w-full" mode="decimal" />
                    </div>
                </div>
            </fieldset>

            <!-- Cession -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Cession</legend>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Date Cession</label>
                        <Calendar v-model="vehicule.date_cession" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Valeur Cession</label>
                        <InputNumber v-model="vehicule.valeur_cession" class="w-full" mode="decimal" />
                    </div>
                </div>
            </fieldset>

            <!-- Foreign keys -->
            <fieldset class="border p-4 rounded">
                <legend class="font-bold">Relations</legend>
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="block mb-1">Modèle</label>
                        <Dropdown v-model="vehicule.modele_id" :options="modeles" optionLabel="name" optionValue="id"
                                  placeholder="Choisir" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Famille Véhicule</label>
                        <Dropdown v-model="vehicule.famille_vehicule_id" :options="familles" optionLabel="name"
                                  optionValue="id" placeholder="Choisir" class="w-full" />
                    </div>
                    <div>
                        <label class="block mb-1">Fournisseur</label>
                        <Dropdown v-model="vehicule.fournisseur_id" :options="fournisseurs" optionLabel="name"
                                  optionValue="id" placeholder="Choisir" class="w-full" />
                    </div>
                </div>
            </fieldset>

            <!-- Submit -->
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Annuler" severity="secondary"
                        @click="router.push({ name: 'vehicules' })" />
                <Button type="submit" label="Enregistrer" :loading="saving" />
            </div>
        </form>
    </div>
</template>
