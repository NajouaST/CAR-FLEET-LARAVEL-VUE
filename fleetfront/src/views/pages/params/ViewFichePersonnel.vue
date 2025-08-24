<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

// Import PrimeVue components
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import Checkbox from 'primevue/checkbox';
import Textarea from 'primevue/textarea';

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const personnels = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const personnelDialog = ref(false);
const deletePersonnelDialog = ref(false);

// form
const personnel = ref({});
const submitted = ref(false);

// dropdown data 
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
const type = ref([{ id: 1, nom: 'sédentaire' },
    { id: 2, nom: 'force de vente' },])

// filters
const defaultFilters = {
    nom: { value: null, matchMode: FilterMatchMode.CONTAINS },
    matriculation: { value: null, matchMode: FilterMatchMode.CONTAINS },
    cin: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    tel: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchPersonnels();
    loadDropdownData();
});

// Load dropdown data with error handling
const loadDropdownData = async () => {
    try {
        console.log('Loading dropdown data...');
        
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
            centreCoutsData
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
            paramsGenereauxStore.getCentreCouts(toast).catch(() => [])
        ]);

        console.log('Societes data:', societesData);
        console.log('Centre Cout data:', centreCoutsData);
        console.log('Societes ref:', societes);
        console.log('Directions data:', directionsData);

		// assign data to ref
		societes.value = societesData;
		directions.value = directionsData;
		fonctions.value = fonctionsData;
		regions.value = regionsData;
		zones.value = zonesData;
		sites.value = sitesData;
		departements.value = departementsData;
		grades.value = gradesData;
		divisions.value = divisionsData;
		centreCouts.value = centreCoutsData;


    } catch (error) {
        console.error('Error loading dropdown data:', error);
    }
};

// fetch personnels
const fetchPersonnels = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getPersonnels(params, toast);
        personnels.value = data?.data ?? [];
        totalRecords.value = data?.totalRecords ?? 0;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchPersonnels(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchPersonnels(lazyParams.value);
};

const filterChips = computed(() => {
    const activeFilters = lazyParams.value?.filters ?? {};
    return Object.entries(activeFilters)
        .filter(([_, filter]) => filter.value)
        .map(([field, filter]) => ({
            field,
            label:
                `${field}: ${
                    filter.value instanceof Date
                        ? filter.value.toLocaleDateString()
                        : filter.value
                }`,
        }));
});

const removeFilter = (field) => {
    if (filters.value[field]) filters.value[field].value = null;
    if (lazyParams.value?.filters?.[field]) lazyParams.value.filters[field].value = null;
    fetchPersonnels(lazyParams.value);
};

// CRUD actions
function openNew() {
    personnel.value = {
        tjrs_actif: true,
		type: 'sédentaire',
		sexe: 'h',
		societe_id: 1,
		direction_id: 1,
		fonction_id: 1,
		region_id: 1,
		zone_id: 1,
		site_id: 1,
		departement_id: 1,
		grade_id: 1,
		division_id: 1,
		centre_cout_id: 1,
        
    };
    submitted.value = false;
    personnelDialog.value = true;
}

function hideDialog() {
    personnelDialog.value = false;
    submitted.value = false;
}

async function savePersonnel() {
    submitted.value = true;
    if (!personnel.value.nom?.trim()) return;

    try {
        const formData = new FormData();
        
        // Basic information
        formData.append("matriculation", personnel.value.matriculation || "");
        formData.append("nom", personnel.value.nom || "");
        formData.append("cin", personnel.value.cin || "");
        formData.append("email", personnel.value.email || "");
        formData.append("tel", personnel.value.tel || "");
        formData.append("titre", personnel.value.titre || "");
        formData.append("adresse", personnel.value.adresse || "");
        formData.append("type", personnel.value.type || "");
        formData.append("sexe", personnel.value.sexe || "");
        formData.append("superviseur", personnel.value.superviseur || "");
        formData.append("num_permis", personnel.value.num_permis || "");
        formData.append("num_carte_carb", personnel.value.num_carte_carb || "");
        if (personnel.value.delivre_le) {
            const delivreDate = new Date(personnel.value.delivre_le);
            formData.append("delivre_le", delivreDate.toISOString().split('T')[0]);
        }
        
        if (personnel.value.fin_validite) {
            const finValiditeDate = new Date(personnel.value.fin_validite);
            formData.append("fin_validite", finValiditeDate.toISOString().split('T')[0]);
        }
        formData.append("tjrs_actif", personnel.value.tjrs_actif ? "1" : "0");


        // Foreign keys
        if (personnel.value.societe_id) formData.append("societe_id", personnel.value.societe_id);
        if (personnel.value.direction_id) formData.append("direction_id", personnel.value.direction_id);
        if (personnel.value.fonction_id) formData.append("fonction_id", personnel.value.fonction_id);
        if (personnel.value.region_id) formData.append("region_id", personnel.value.region_id);
        if (personnel.value.zone_id) formData.append("zone_id", personnel.value.zone_id);
        if (personnel.value.site_id) formData.append("site_id", personnel.value.site_id);
        if (personnel.value.departement_id) formData.append("departement_id", personnel.value.departement_id);
        if (personnel.value.grade_id) formData.append("grade_id", personnel.value.grade_id);
        if (personnel.value.division_id) formData.append("division_id", personnel.value.division_id);
        if (personnel.value.centre_cout_id) formData.append("centre_cout_id", personnel.value.centre_cout_id);

		// log personnel
		console.log('personnel:', personnel.value);
		
        if (personnel.value.id) {
            await paramsGenereauxStore.updatePersonnel(personnel.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Personnel updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createPersonnel(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Personnel created", life: 3000 });
        }

        personnelDialog.value = false;
        fetchPersonnels(lazyParams.value);
    } catch (err) {
        console.error('Error saving personnel:', err);
		// ADD THIS TO SEE VALIDATION ERRORS
        if (err.response?.status === 422) {
            console.error('Validation errors:', err.response.data.errors);
            // Show validation errors to user
            const errors = err.response.data.errors;
            for (const [field, messages] of Object.entries(errors)) {
                toast.add({ 
                    severity: "error", 
                    summary: "Validation Error", 
                    detail: `${field}: ${messages.join(', ')}`,
                    life: 5000 
                });
            }
        } else {
            toast.add({ severity: "error", summary: "Error", detail: "Error saving personnel", life: 3000 });
        }
    }
}

function editPersonnel(p) {
    personnel.value = { ...p };
    personnelDialog.value = true;
}

function confirmDeletePersonnel(p) {
    personnel.value = p;
    deletePersonnelDialog.value = true;
}

async function deletePersonnel() {
    await paramsGenereauxStore.deletePersonnel(personnel.value.id, toast);
    deletePersonnelDialog.value = false;
    fetchPersonnels(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Fiche Personnel : {{ totalRecords }} </h1>
                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <div class="card">
                <!-- Toolbar and DataTable remain the same -->
                <Toolbar class="mb-6">
                    <template #start>
                        <div class="flex gap-4">
                            <Button label="New" icon="pi pi-plus" severity="secondary" @click="openNew" />
                        </div>
                    </template>
                    <template #center>
                        <template v-if="filterChips.length > 0">
                            <Chip
                                v-for="chip in filterChips"
                                :key="chip.field"
                                :label="chip.label"
                                removable
                                @remove="removeFilter(chip.field)"
                            />
                        </template>
                        <span v-else class="text-gray-400 text-sm italic">No filters applied</span>
                    </template>
                    <template #end>
                        <div class="flex gap-4">
                            <Button label="Clear" icon="pi pi-filter-slash" outlined @click="clearFilters" />
                        </div>
                    </template>
                </Toolbar>

                <!-- DataTable remains the same as before -->
                <DataTable
                    :value="personnels"
                    :lazy="true"
                    :loading="loading"
                    :paginator="true"
                    :rows="perPage"
                    :totalRecords="totalRecords"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    :sortOrder="1"
                    sortField="id"
                    v-model:filters="filters"
                    filterDisplay="row"
                    scrollable
                    scrollHeight="400px"
                    scrollDirection="both"
                    @filter="onTableEvent"
                    @page="onTableEvent"
                    @sort="onTableEvent"
                >
                    <!-- ... existing columns ... -->
                    <template #empty>No Personnel found.</template>

                    <Column field="id" header="ID" sortable style="min-width: 60px; max-width: 80px" frozen />

                    <Column field="matriculation" header="Matriculation" sortable style="min-width: 120px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search matriculation" />
                        </template>
                    </Column>

                    <Column field="nom" header="Nom" sortable style="min-width: 150px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column field="cin" header="CIN" sortable style="min-width: 120px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search CIN" />
                        </template>
                    </Column>

                    <Column field="email" header="Email" sortable style="min-width: 180px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search email" />
                        </template>
                    </Column>

                    <Column field="tel" header="Téléphone" sortable style="min-width: 130px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search phone" />
                        </template>
                    </Column>

                    <Column field="titre" header="Titre" sortable style="min-width: 120px" />

                    <Column field="adresse" header="Adresse" sortable style="min-width: 200px" />

                    <Column field="type" header="Type" sortable style="min-width: 100px" />

                    <Column field="sexe" header="Sexe" sortable style="min-width: 80px" />

                    <Column field="superviseur" header="Superviseur" sortable style="min-width: 130px" />

                    <Column field="num_permis" header="N° Permis" sortable style="min-width: 120px" />

                    <Column field="num_carte_carb" header="N° Carte Carb" sortable style="min-width: 120px" />

                    <Column field="delivre_le" header="Délivré le" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.delivre_le ? new Date(data.delivre_le).toLocaleDateString() : '-' }}
                        </template>
                    </Column>

                    <Column field="fin_validite" header="Fin Validité" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.fin_validite ? new Date(data.fin_validite).toLocaleDateString() : '-' }}
                        </template>
                    </Column>

                    <Column field="tjrs_actif" header="Toujours Actif" sortable style="min-width: 100px">
                        <template #body="{ data }">
                            <i v-if="data.tjrs_actif" class="pi pi-check text-green-500"></i>
                            <i v-else class="pi pi-times text-red-500"></i>
                        </template>
                    </Column>

                    <!-- Related data columns -->
                    <Column field="societe.nom" header="Société" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.societe?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="direction.nom" header="Direction" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.direction?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="fonction.nom" header="Fonction" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.fonction?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="region.nom" header="Région" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.region?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="zone.nom" header="Zone" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.zone?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="site.nom" header="Site" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.site?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="departement.nom" header="Département" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.departement?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="grade.nom" header="Grade" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.grade?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="division.nom" header="Division" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.division?.nom || '-' }}
                        </template>
                    </Column>

                    <Column field="centreCout.nom" header="Centre Coût" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.centreCout?.nom || '-' }}
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 120px" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2" @click="editPersonnel(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="confirmDeletePersonnel(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Comprehensive Create/Edit Dialog -->
                <Dialog v-model:visible="personnelDialog" :style="{ width: '90vw', maxWidth: '1200px' }" header="Personnel Details" modal>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Informations de Base</h3>
                            
                            <div>
                                <label for="matriculation" class="block font-medium mb-2">Matriculation *</label>
                                <InputText id="matriculation" v-model="personnel.matriculation" required :invalid="submitted && !personnel.matriculation" class="w-full" />
                                <small v-if="submitted && !personnel.matriculation" class="text-red-500">Matriculation is required.</small>
                            </div>

                            <div>
                                <label for="nom" class="block font-medium mb-2">Nom *</label>
                                <InputText id="nom" v-model.trim="personnel.nom" required :invalid="submitted && !personnel.nom" class="w-full" />
                                <small v-if="submitted && !personnel.nom" class="text-red-500">Nom is required.</small>
                            </div>

                            <div>
                                <label for="cin" class="block font-medium mb-2">CIN *</label>
                                <InputText id="cin" v-model="personnel.cin" required :invalid="submitted && !personnel.cin" class="w-full" />
                                <small v-if="submitted && !personnel.cin" class="text-red-500">CIN is required.</small>
                            </div>

                            <div>
                                <label for="email" class="block font-medium mb-2">Email *</label>
                                <InputText id="email" v-model="personnel.email" required :invalid="submitted && !personnel.email" type="email" class="w-full" />
                                <small v-if="submitted && !personnel.email" class="text-red-500">Email is required.</small>
                            </div>

                            <div>
                                <label for="tel" class="block font-medium mb-2">Téléphone *</label>
                                <InputText id="tel" v-model="personnel.tel" required :invalid="submitted && !personnel.tel" class="w-full" />
                                <small v-if="submitted && !personnel.tel" class="text-red-500">Téléphone is required.</small>
                            </div>

                            <div>
                                <label for="titre" class="block font-medium mb-2">Titre *</label>
                                <InputText id="titre" v-model="personnel.titre" required :invalid="submitted && !personnel.titre" class="w-full" />
                                <small v-if="submitted && !personnel.titre" class="text-red-500">Titre is required.</small>
                            </div>

                            <div>
                                <label for="adresse" class="block font-medium mb-2">Adresse *</label>
                                <Textarea id="adresse" v-model="personnel.adresse" required :invalid="submitted && !personnel.adresse" rows="3" class="w-full" />
                                <small v-if="submitted && !personnel.adresse" class="text-red-500">Adresse is required.</small>
                            </div>
                        </div>

                        <!-- Organizational Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Informations Organisationnelles</h3>
                            
                            <div>
                                <label for="societe_id" class="block font-medium mb-2">Société</label>
                                <Dropdown 
                                    id="societe_id" 
                                    v-model="personnel.societe_id" 
                                    :options="societes" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une société" 
                                    class="w-full"
                                    :loading="societes.length === 0"
                                />
                            </div>

                            <div>
                                <label for="direction_id" class="block font-medium mb-2">Direction</label>
                                <Dropdown 
                                    id="direction_id" 
                                    v-model="personnel.direction_id" 
                                    :options="directions" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une direction" 
                                    class="w-full"
                                    :loading="directions.length === 0"
                                />
                            </div>

                            <div>
                                <label for="fonction_id" class="block font-medium mb-2">Fonction</label>
                                <Dropdown 
                                    id="fonction_id" 
                                    v-model="personnel.fonction_id" 
                                    :options="fonctions" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une fonction" 
                                    class="w-full"
                                    :loading="fonctions.length === 0"
                                />
                            </div>

                            <div>
                                <label for="grade_id" class="block font-medium mb-2">Grade</label>
                                <Dropdown 
                                    id="grade_id" 
                                    v-model="personnel.grade_id" 
                                    :options="grades" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner un grade" 
                                    class="w-full"
                                    :loading="grades.length === 0"
                                />
                            </div>

                            <div>
                                <label for="departement_id" class="block font-medium mb-2">Département</label>
                                <Dropdown 
                                    id="departement_id" 
                                    v-model="personnel.departement_id" 
                                    :options="departements" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner un département" 
                                    class="w-full"
                                    :loading="departements.length === 0"
                                />
                            </div>

                            <div>
                                <label for="division_id" class="block font-medium mb-2">Division</label>
                                <Dropdown 
                                    id="division_id" 
                                    v-model="personnel.division_id" 
                                    :options="divisions" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une division" 
                                    class="w-full"
                                    :loading="divisions.length === 0"
                                />
                            </div>

                            <div>
                                <label for="centre_cout_id" class="block font-medium mb-2">Centre de Coût</label>
                                <Dropdown 
                                    id="centre_cout_id" 
                                    v-model="personnel.centre_cout_id" 
                                    :options="centreCouts" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner un centre de coût" 
                                    class="w-full"
                                    :loading="centreCouts.length === 0"
                                />
                            </div>
                        </div>

                        <!-- Location and Additional Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Localisation et Informations Supplémentaires</h3>
                            
                            <div>
                                <label for="region_id" class="block font-medium mb-2">Région</label>
                                <Dropdown 
                                    id="region_id" 
                                    v-model="personnel.region_id" 
                                    :options="regions" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une région" 
                                    class="w-full"
                                    :loading="regions.length === 0"
                                />
                            </div>

                            <div>
                                <label for="zone_id" class="block font-medium mb-2">Zone</label>
                                <Dropdown 
                                    id="zone_id" 
                                    v-model="personnel.zone_id" 
                                    :options="zones" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner une zone" 
                                    class="w-full"
                                    :loading="zones.length === 0"
                                />
                            </div>

                            <div>
                                <label for="site_id" class="block font-medium mb-2">Site</label>
                                <Dropdown 
                                    id="site_id" 
                                    v-model="personnel.site_id" 
                                    :options="sites" 
                                    optionLabel="nom" 
                                    optionValue="id" 
                                    placeholder="Sélectionner un site" 
                                    class="w-full"
                                    :loading="sites.length === 0"
                                />
                            </div>

							<div>
                                <label for="type" class="block font-medium mb-2">Type</label>
                                <Dropdown 
                                    id="type" 
                                    v-model="personnel.type" 
                                    :options="type" 
                                    optionLabel="nom" 
                                    optionValue="nom" 
                                    placeholder="Sélectionner le type" 
                                    class="w-full"
                                    :loading="type.length === 0"
                                />
                            </div>

                            <div>
                                <label for="sexe" class="block font-medium mb-2">Sexe</label>
                                <Dropdown 
                                    id="sexe" 
                                    v-model="personnel.sexe" 
                                    :options="[
                                        { label: 'Masculin', value: 'h' },
                                        { label: 'Féminin', value: 'f' }
                                    ]" 
                                    optionLabel="label" 
                                    optionValue="value" 
                                    placeholder="Sélectionner le sexe" 
                                    class="w-full" 
                                />
                            </div>

                            <div>
                                <label for="superviseur" class="block font-medium mb-2">Superviseur</label>
                                <InputText id="superviseur" v-model="personnel.superviseur" class="w-full" />
                            </div>

                            <div>
                                <label for="num_permis" class="block font-medium mb-2">N° Permis *</label>
                                <InputText id="num_permis" v-model="personnel.num_permis" required :invalid="submitted && !personnel.num_permis" class="w-full" />
                                <small v-if="submitted && !personnel.num_permis" class="text-red-500">N° Permis is required.</small>
                            </div>

                            <div>
                                <label for="num_carte_carb" class="block font-medium mb-2">N° Carte Carburant</label>
                                <InputNumber id="num_carte_carb" v-model="personnel.num_carte_carb" class="w-full" />
                            </div>

                            <div>
                                <label for="delivre_le" class="block font-medium mb-2">Délivré le *</label>
                                <Calendar id="delivre_le" v-model="personnel.delivre_le" required :invalid="submitted && !personnel.delivre_le" dateFormat="dd/mm/yy" class="w-full" />
                                <small v-if="submitted && !personnel.delivre_le" class="text-red-500">Délivré le is required.</small>
                            </div>

                            <div>
                                <label for="fin_validite" class="block font-medium mb-2">Fin de Validité *</label>
                                <Calendar id="fin_validite" v-model="personnel.fin_validite" required :invalid="submitted && !personnel.fin_validite" dateFormat="dd/mm/yy" class="w-full" />
                                <small v-if="submitted && !personnel.fin_validite" class="text-red-500">Fin de Validité is required.</small>
                            </div>

                            <div class="flex items-center">
                                <Checkbox id="tjrs_actif" v-model="personnel.tjrs_actif" :binary="true" />
                                <label for="tjrs_actif" class="ml-2 font-medium">Toujours Actif</label>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="savePersonnel" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deletePersonnelDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="personnel">Are you sure you want to delete <b>{{ personnel.nom }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deletePersonnelDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deletePersonnel" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>