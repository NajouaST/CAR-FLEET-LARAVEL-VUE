<script setup>
import { DatePicker, Select, useToast } from 'primevue';
import { onMounted, ref } from 'vue';
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { useRouter, useRoute } from 'vue-router';


import Skeleton from 'primevue/skeleton';
import Checkbox from 'primevue/checkbox';


const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();
const router = useRouter();
const route = useRoute();


// state
const personnel = ref({});
const saving = ref(false);
const isLoading = ref(true);

const errors = ref({})





const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const loadPersonnel = async () => {
	isLoading.value = true;
	try {
		const personnelId = route.params.id;
		const data = await paramsGenereauxStore.getPersonnelById(personnelId, toast);
		personnel.value = data;

		console.log("personnel", personnel.value);

		// keep the skeleton visible for X seconds after data arrives
		await sleep(100);
	} catch (error) {
		console.error('Error loading personnel:', error);
	} finally {
		isLoading.value = false;
	}
};

onMounted(() => {
    loadPersonnel();
	
})

</script>
<template>
	<div v-if="isLoading" >
		<Skeleton width="100%" height="800px" />
        
    </div>

	<div v-else class="card p-6">
		<h2 class="text-xl font-bold mb-4">Détails du personnel</h2>
		<form class="space-y-6">
			<div>
                <label class="block font-semibold mb-2">Matriculation *</label>
                <InputText v-model="personnel.matriculation" placeholder="Matriculation" class="w-full" readonly />
                <small v-if="errors.matriculation" class="text-red-500">{{ errors.matriculation }}</small>
            </div>
			<div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- informations de base -->
				<fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations de Base</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Nom *</label>
							<InputText :value="personnel.user?.name ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">CIN *</label>
                            <InputText :value="personnel.cin ?? '-'" class="w-full" readonly />
                        </div>
                        <div class="col-span-2">
                            <label class="block m-1">Email *</label>
                            <InputText :value="personnel.user?.email ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Téléphone *</label>
                            <InputText :value="personnel.tel ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Titre</label>
                            <InputText :value="personnel.titre ?? '-'" class="w-full" readonly />
                        </div>
                        <div class="col-span-2">
                            <label class="block m-1">Adresse</label>
                            <Textarea :value="personnel.adresse ?? '-'" class="w-full" readonly />
                        </div>                        
                    </div>
                </fieldset>

				<!-- informations organisationnelles -->
				<fieldset class="border p-4 rounded">
                    <legend class="font-bold">Informations Organisationnelles</legend>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block m-1">Societé *</label>
                            <InputText :value="personnel.societe?.nom ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Direction *</label>
                            <InputText :value="personnel.direction?.nom ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Fonction *</label>
                            <InputText :value="personnel.fonction?.nom ?? '-'" class="w-full" readonly />
                        </div>

                        <div>
                            <label class="block m-1">Grade</label>
                            <InputText :value="personnel.grade?.nom ?? '-'" class="w-full" readonly />
                        </div>

                        <div>
                            <label class="block m-1">Département</label>
                            <InputText :value="personnel.departement?.nom ?? '-'" class="w-full" readonly />
                        </div>

                        <div>
                            <label class="block m-1">Division</label>
                            <InputText :value="personnel.division?.nom ?? '-'" class="w-full" readonly /> 
                        </div>

                        <div>
                            <label class="block m-1">Centre de Coût</label>
                            <InputText :value="personnel.centre_cout?.nom ?? '-'" class="w-full" readonly />
                        </div>

						<div class="flex items-center mt-6">
							<Checkbox v-model="personnel.tjrs_actif" class="w-full" id="tjrs_actif" binary readonly/>
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
                            <InputText :value="personnel.region?.nom ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Zone</label>
                            <InputText :value="personnel.zone?.nom ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Site</label>
                            <InputText :value="personnel.site?.nom ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Type</label>
                            <InputText :value="personnel.type ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Sexe</label>
                            <InputText :value="personnel.sexe === 'h' ? 'Homme' : 'Femme'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Superviseur</label>
                            <InputText :value="personnel.superviseur ?? '-'" class="w-full" readonly />
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
                            <InputText :value="personnel.carte_carburant?.n_carte ?? '-'" class="w-full" readonly />

                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div>
                            <label class="block m-1">N° Permis *</label>
                            <InputText :value="personnel.num_permis ?? '-'" class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Délivré le *</label>
                            <DatePicker v-model="personnel.delivre_le" dateFormat="dd/mm/yy" showIcon class="w-full" readonly />
                        </div>
                        <div>
                            <label class="block m-1">Fin de Validité *</label>
                            <DatePicker v-model="personnel.fin_validite" dateFormat="dd/mm/yy" showIcon class="w-full" readonly />
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Annuler" severity="secondary" @click="router.push({ name: 'personnels' })" />
            </div>
		</form>
	</div>
</template>