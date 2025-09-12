<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";
import { useRouter } from "vue-router";

// Import PrimeVue components
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import Checkbox from 'primevue/checkbox';
import Textarea from 'primevue/textarea';

const toast = useToast();
const router = useRouter();
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
});



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
    router.push({name: "createPersonnel"})
    
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
                

                    <Column field="user.name" header="Nom" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.user?.name || '-' }}
                        </template>
                        
                        
                    </Column>

                    <Column field="cin" header="CIN" sortable style="min-width: 120px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search CIN" />
                        </template>
                    </Column>

                    <Column field="user.email" header="email" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.user?.email || '-' }}
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

                    <Column field="centre_cout.nom" header="Centre Coût" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            {{ data.centre_cout?.nom || '-' }}
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="router.push({ name: 'showPersonnel', params: { id: data.id } })" />
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2" @click="router.push({ name: 'editPersonnel', params: { id: data.id } })" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="confirmDeletePersonnel(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Comprehensive Create/Edit Dialog -->
                <Dialog v-model:visible="personnelDialog" :style="{ width: '90vw', maxWidth: '1200px' }" header="Personnel Details" modal>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        

                        

                        <!-- Location and Additional Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Localisation et Informations Supplémentaires</h3>


                            <div class="flex items-center">
                                <Checkbox id="tjrs_actif" v-model="personnel.tjrs_actif" :binary="true" />
                                <label for="tjrs_actif" class="ml-2 font-medium">Toujours Actif</label>
                            </div>
                        </div>
                    </div>
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
