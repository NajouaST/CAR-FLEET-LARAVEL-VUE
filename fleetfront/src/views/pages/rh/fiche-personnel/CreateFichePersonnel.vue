<script setup>
import { DatePicker, ProgressSpinner, Select, useToast } from 'primevue';
import { onMounted, ref } from 'vue';
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { useRouter } from 'vue-router';

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();
const router = useRouter();


// state
const personnel = ref({});
const saving = ref(false);
const isLoading = ref(false);

// select data
const societes = ref([]);
const directions = ref([]);
const fonctions = ref([]);
const regions = ref([]);
const zones = ref([]);
const sites = ref([]);
const departements = ref([]);
const grades = ref([]);
const divisions = ref([]);
const centreCouts = ref([]);
const superviseurs = ref([]);
const carteCarburants = ref([]);
const users = ref([]);



const errors = ref({})


const loadSelectData = async () => {
    isLoading.value = true;
    try {
        console.log('Loading select data...');

        const [
            societesData,
            directionsData,
            fonctionsData,
            regionsData,
            zonesData,
            sitesData,
            departementsData,
            gradesData,
            divisionsData,
            centreCoutsData,
            superviseursData,
            carteCarburantsData,
            usersData
        ] = await Promise.all([
            paramsGenereauxStore.getSocietes(toast).catch(() => []),
            paramsGenereauxStore.getDirections(toast).catch(() => []),
            paramsGenereauxStore.getFonctions(toast).catch(() => []),
            paramsGenereauxStore.getRegions(toast).catch(() => []),
            paramsGenereauxStore.getZones(toast).catch(() => []),
            paramsGenereauxStore.getSites(toast).catch(() => []),
            paramsGenereauxStore.getDepartements(toast).catch(() => []),
            paramsGenereauxStore.getGrades(toast).catch(() => []),
            paramsGenereauxStore.getDivisions(toast).catch(() => []),
            paramsGenereauxStore.getCentreCouts(toast).catch(() => []),
            paramsGenereauxStore.getSuperviseurs(toast).catch(() => []),
            paramsGenereauxStore.getCarteCarburants(toast).catch(() => []),
            paramsGenereauxStore.getUsers(toast).catch(() => [])
        ]);

        console.log("all data from the api", societesData,
            directionsData,
            fonctionsData,
            regionsData,
            zonesData,
            sitesData,
            departementsData,
            gradesData,
            divisionsData,
            centreCoutsData,
            superviseursData,
            carteCarburantsData,
            usersData)
        
        

        // assign data to ref
        societes.value = societesData;
        options.societes = societesData.data;

        directions.value = directionsData;
        options.directions = directionsData.data;

        fonctions.value = fonctionsData;
        options.fonctions = fonctionsData;

        regions.value = regionsData;
        options.regions = regionsData;

        zones.value = zonesData;
        options.zones = zonesData;

        sites.value = sitesData;
        options.sites = sitesData.data;

        departements.value = departementsData;
        options.departements = departementsData.data;

        grades.value = gradesData;
        options.grades = gradesData;

        divisions.value = divisionsData;
        options.divisions = divisionsData;

        centreCouts.value = centreCoutsData;
        options.centreCouts = centreCoutsData.data;

        superviseurs.value = superviseursData;
        options.superviseurs = superviseursData;

        carteCarburants.value = carteCarburantsData;
        options.carteCarburants = carteCarburantsData;

        users.value = usersData;
        options.users = usersData;
        
        console.log("updated options",options);

    } catch (error) {
        console.error('Error loading dropdown data:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
	loadSelectData();
})

// select options
const options = {
	titres: [
		{label: "mr", value:"mr"},
		{label: "ms", value:"ms"}
	],
    types: [
        {label: "sédentaire", value:"sédentaire"},
        {label: "force de vente", value:"force de vente"}
    ],
    sexes: [
        {label: "Homme", value:"h"},
        {label: "Femme", value:"f"}
    ],
    
}

// default personnel values
personnel.value = {
    type: "sédentaire",
    sexe: "h",
    titre: "mr",
}

// validate form
function validateForm() {
    errors.value = {};
    if (!personnel.value.matriculation) errors.value.matriculation = "Matriculation requise";
    if (!personnel.value.cin) errors.value.cin = "CIN requise";
    if (!personnel.value.tel) errors.value.tel = "Téléphone requise";
    if (!personnel.value.num_permis) errors.value.num_permis = "N° Permis requise";
    if (!personnel.value.delivre_le) errors.value.delivre_le = "Délivré le requise";
    if (!personnel.value.fin_validite) errors.value.fin_validite = "Fin de validité requise";
    return Object.keys(errors.value).length === 0;
}

// save personnel
async function savePersonnel() {
    if (!validateForm()) {
        toast.add({ severity: "error", summary: "Error", detail: "Missing required fields", life: 3000 });
        return;
    }
    saving.value = true;
    try {
        await paramsGenereauxStore.createPersonnel(personnel.value, toast);
        toast.add({ severity: "success", summary: "Success", detail: "Personnel created", life: 3000 });
        return;
    } catch (error) {
        console.error('Error saving personnel:', error);
        toast.add({ severity: "error", summary: "Error", detail: "Error saving personnel", life: 3000 });
        if (error.response?.status === 422) {
            console.error('Validation errors:', error.response.data.errors);
            // Show validation errors to user
            const errors = error.response.data.errors;
            for (const [field, messages] of Object.entries(errors)) {
                toast.add({ severity: "error", summary: "Validation Error", detail: `${field}: ${messages.join(', ')}`, life: 5000 });
            }
        }
    } finally {
        saving.value = false;
    }
}

</script>
<template>
	<div v-if="isLoading" class="flex justify-center items-center h-80 bg-white flex-col gap-4">
		<ProgressSpinner />
        <p>Chargement des données...</p>
        
    </div>

	<div v-else class="card p-6">
		<h2 class="text-xl font-bold mb-4">Créer un personnel</h2>
		<form @submit.prevent="savePersonnel" class="space-y-6">
			<div>
                <label class="block font-semibold mb-2">Matriculation *</label>
                <InputText v-model="personnel.matriculation" placeholder="Matriculation" class="w-full" />
                <small v-if="errors.matriculation" class="text-red-500">{{ errors.matriculation }}</small>
            </div>
			<div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- informations de base -->
				<fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations de Base</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Nom *</label>
                            <Select
                                v-model="personnel.user_id"
                                :options="options.users"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Sélectionner un utilisateur"
                                filter
                                :loading="users.length === 0"
                                class="w-full"
                            />
                            <small v-if="errors.user_id" class="text-red-500">{{ errors.user_id }}</small>
                        </div>
                        <div>
                            <label class="block m-1">CIN *</label>
                            <InputText v-model="personnel.cin" class="w-full" />
                            <small v-if="errors.cin" class="text-red-500">{{ errors.cin }}</small>
                        </div>
                        <div class="col-span-2">
                            <label class="block m-1">Email *</label>
                            <Select
                                v-model="personnel.user_id"
                                :options="options.users"
                                optionLabel="email"
                                optionValue="id"
                                placeholder="Sélectionner un utilisateur"
                                filter
                                :loading="users.length === 0"
                                class="w-full"
                            />
                            <small v-if="errors.user_id" class="text-red-500">{{ errors.user_id }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Téléphone *</label>
                            <InputText v-model="personnel.tel" class="w-full" />
                            <small v-if="errors.tel" class="text-red-500">{{ errors.tel }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Titre</label>
                            <Select
                                v-model="personnel.titre"
                                :options="options.titres"
                                optionLabel="label"
								optionValue="value"
                                checkmark
                                placeholder="Choisir"
                                class="w-full"
                            />
                        </div>
                        <div class="col-span-2">
                            <label class="block m-1">Adresse</label>
                            <Textarea v-model="personnel.adresse" class="w-full" />
                        </div>

                        
                        
                    </div>
                </fieldset>

				<!-- informations organisationnelles -->
				<fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations Organisationnelles</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Societé *</label>
                            <Select
                                    v-model="personnel.societe_id"
                                    :options="options.societes"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une société"
                                    class="w-full"
                                    filter
                                    :loading="societes.length === 0"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Direction *</label>
                            <Select
                                    v-model="personnel.direction_id"
                                    :options="options.directions"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une direction"
                                    class="w-full"
                                    filter
                                    :loading="directions.length === 0"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Fonction *</label>
                            <Select
                                    v-model="personnel.fonction_id"
                                    :options="options.fonctions"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une fonction"
                                    class="w-full"
                                    filter
                                    :loading="fonctions.length === 0"
                            />
                        </div>

                        <div>
                                <label class="block m-1">Grade</label>
                                <Select
                                    v-model="personnel.grade_id"
                                    :options="options.grades"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner un grade"
                                    class="w-full"
                                    :loading="grades.length === 0"
                                />
                        </div>

                        <div>
                                <label class="block m-1">Département</label>
                                <Select
                                    v-model="personnel.departement_id"
                                    :options="options.departements"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner un département"
                                    class="w-full"
                                    filter
                                    :loading="departements.length === 0"
                                />
                        </div>

                        <div>
                                <label class="block m-1">Division</label>
                                <Select
                                    v-model="personnel.division_id"
                                    :options="options.divisions"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une division"
                                    class="w-full"
                                    filter
                                    :loading="divisions.length === 0"
                                />
                        </div>

                        <div>
                                <label class="block m-1">Centre de Coût</label>
                                <Select
                                    v-model="personnel.centre_cout_id"
                                    :options="options.centreCouts"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner un centre de coût"
                                    class="w-full"
                                    filter
                                    :loading="centreCouts.length === 0"
                                />
                        </div>

                        <div class="flex items-center mt-6">
							<Checkbox v-model="personnel.tjrs_actif" class="w-full" id="tjrs_actif" binary />
							<label for="tjrs_actif" class="block m-1">Toujours Actif</label>
						</div>
                                                
                        
                    </div>
                </fieldset>

			</div>
            <!-- Location and Additional Information -->
            <div class="grid grid-cols-1">
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Localisation et Informations Supplémentaires</legend>
                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Région</label>
                            <Select
                                    v-model="personnel.region_id"
                                    :options="options.regions"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une région"
                                    class="w-full"
                                    filter
                                    :loading="regions.length === 0"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Zone</label>
                            <Select
                                    v-model="personnel.zone_id"
                                    :options="options.zones"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner une zone"
                                    class="w-full"
                                    filter
                                    :loading="zones.length === 0"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Site</label>
                            <Select
                                    v-model="personnel.site_id"
                                    :options="options.sites"
                                    optionLabel="nom"
                                    optionValue="id"
                                    placeholder="Sélectionner un site"
                                    class="w-full"
                                    filter
                                    :loading="sites.length === 0"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Type</label>
                            <Select
                                    v-model="personnel.type"
                                    :options="options.types"
                                    optionLabel="label"
                                    optionValue="value"
                                    class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Sexe</label>
                            <Select
                                    v-model="personnel.sexe"
                                    :options="options.sexes"
                                    optionLabel="label"
                                    optionValue="value"
                                    class="w-full"
                            />
                        </div>
                        <div>
                            <label class="block m-1">Superviseur</label>
                            <Select
                                    v-model="personnel.superviseur"
                                    :options="options.superviseurs"
                                    optionLabel="user.name"
                                    optionValue="user.name"
                                    placeholder="Sélectionner un superviseur"
                                    class="w-full"
                                    filter
                                    :loading="superviseurs.length === 0"
                            />
                        </div>
                        
                    </div>
                </fieldset>
            </div>
            <!-- Informations carburants -->
            <div class="grid grid-cols-1">
                <fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations Carburants</legend>
                    <div class="grid grid-cols-1 mt-2">
                        <div>
                            <label class="block m-1">Carte Carburant</label>
                            <Select
                                    v-model="personnel.carte_carburant_id"
                                    :options="options.carteCarburants"
                                    optionLabel="n_carte"
                                    optionValue="id"
                                    placeholder="Sélectionner une carte carburant"
                                    class="w-full"
                                    filter
                                    :loading="carteCarburants.length === 0"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div>
                            <label class="block m-1">N° Permis *</label>
                            <InputText v-model="personnel.num_permis" class="w-full" />
                            <small v-if="errors.num_permis" class="text-red-500">{{ errors.num_permis }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Délivré le *</label>
                            <DatePicker v-model="personnel.delivre_le" dateFormat="dd/mm/yy" showIcon class="w-full" />
                            <small v-if="errors.delivre_le" class="text-red-500">{{ errors.delivre_le }}</small>
                        </div>
                        <div>
                            <label class="block m-1">Fin de Validité *</label>
                            <DatePicker v-model="personnel.fin_validite" dateFormat="dd/mm/yy" showIcon class="w-full" />
                            <small v-if="errors.fin_validite" class="text-red-500">{{ errors.fin_validite }}</small>
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Annuler" severity="secondary" @click="router.push({ name: 'personnels' })" />
                <Button type="submit" label="Enregistrer" :loading="saving" />
            </div>
		</form>
	</div>
</template>