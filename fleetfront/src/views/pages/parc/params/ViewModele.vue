<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsParcStore } from "@/store/paramsParc";
import { FilterMatchMode } from "@primevue/core/api";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";

const toast = useToast();
const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const modeles = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const modeleDialog = ref(false);
const deleteModeleDialog = ref(false);

// form
const modele = ref({});
const submitted = ref(false);

// options (for selects)
const marques = ref([]);
const gammes = ref([]);
const typeCompteurs = ref([]);
const typeCarburants = ref([]);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    marque__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    gamme__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    typeCompteur__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    typeCarburant__name: { value: null, matchMode: FilterMatchMode.EQUALS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchModeles();
    fetchOptions();
});

// fetch modeles
const fetchModeles = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsParcStore.getModeles(params, toast);
        modeles.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// fetch options for dropdowns
async function fetchOptions() {
    marques.value = (await paramsParcStore.getMarques({}, toast)).data;
    gammes.value = (await paramsParcStore.getGammes({}, toast)).data;
    typeCompteurs.value = (await paramsGenereauxStore.getTypeCompteurs({}, toast)).data;
    typeCarburants.value = (await paramsGenereauxStore.getTypeCarburants({}, toast)).data;
}

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchModeles(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchModeles(lazyParams.value);
};

const filterChips = computed(() => {
    const activeFilters = lazyParams.value?.filters ?? {};
    return Object.entries(activeFilters)
        .filter(([_, filter]) => filter.value)
        .map(([field, filter]) => ({
            field,
            label: `${field}: ${filter.value}`,
        }));
});

const removeFilter = (field) => {
    if (filters.value[field]) filters.value[field].value = null;
    if (lazyParams.value?.filters?.[field]) lazyParams.value.filters[field].value = null;
    fetchModeles(lazyParams.value);
};

// CRUD actions
function openNew() {
    modele.value = {};
    submitted.value = false;
    modeleDialog.value = true;
}

function hideDialog() {
    modeleDialog.value = false;
    submitted.value = false;
}

async function saveModele() {
    submitted.value = true;
    if (!modele.value.name?.trim()) return;

    try {
        if (modele.value.id) {
            await paramsParcStore.updateModeles(modele.value.id, modele.value, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Modele updated", life: 3000 });
        } else {
            await paramsParcStore.createModeles(modele.value, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Modele created", life: 3000 });
        }
        modeleDialog.value = false;
        fetchModeles(lazyParams.value);
    } catch (err) {
        console.error(err);
    }
}

function editModele(m) {
    modele.value = { ...m };
    modeleDialog.value = true;
}

function confirmDeleteModele(m) {
    modele.value = m;
    deleteModeleDialog.value = true;
}

async function deleteModele() {
    await paramsParcStore.deleteModele(modele.value.id, toast);
    deleteModeleDialog.value = false;
    fetchModeles(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Modèles : {{ totalRecords }}</h1>
                <div class="flex items-center justify-center bg-purple-100 dark:bg-purple-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-car text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <div class="card">
                <!-- Toolbar -->
                <Toolbar class="mb-6">
                    <template #start>
                        <Button label="New" icon="pi pi-plus" severity="secondary" @click="openNew" />
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
                        <Button label="Clear" icon="pi pi-filter-slash" outlined @click="clearFilters" />
                    </template>
                </Toolbar>

                <!-- DataTable -->
                <DataTable
                    :value="modeles"
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
                    @filter="onTableEvent"
                    @page="onTableEvent"
                    @sort="onTableEvent"
                >
                    <template #empty>No modèles found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />
                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column header="Marque" field="marque.name" filterField="marque__name" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span>{{ data.marque?.name }}</span>
                        </template>

                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="marques"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column header="Gamme" field="gamme.name" filterField="gamme__name" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span>{{ data.gamme?.name }}</span>
                        </template>

                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="gammes"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column header="Type Compteurs" field="type_compteur.name" filterField="typeCompteur__name" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span>{{ data.type_compteur?.name }}</span>
                        </template>

                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="typeCompteurs"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column header="Type Carburants" field="type_carburant.name" filterField="typeCarburant__name" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            <span>{{ data.type_carburant?.name }}</span>
                        </template>

                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="typeCarburants"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            />
                        </template>
                    </Column>
                    <Column field="CO2" header="CO₂" sortable />
                    <Column field="Cylindre" header="Cylindre" sortable />
                    <Column field="Poids" header="Poids" sortable />

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editModele(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteModele(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="modeleDialog" :style="{ width: '600px' }" header="Modele Details" modal>
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="modele.name" required autofocus
                                       :invalid="submitted && !modele.name" class="w-full" />
                            <small v-if="submitted && !modele.name" class="text-red-500">Name is required.</small>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold mb-2">Marque</label>
                                <Dropdown v-model="modele.marque_id" :options="marques" optionLabel="name" optionValue="id" placeholder="Select marque" class="w-full" />
                            </div>
                            <div>
                                <label class="block font-bold mb-2">Gamme</label>
                                <Dropdown v-model="modele.gamme_id" :options="gammes" optionLabel="name" optionValue="id" placeholder="Select gamme" class="w-full" />
                            </div>
                            <div>
                                <label class="block font-bold mb-2">Type Compteur</label>
                                <Dropdown v-model="modele.type_compteur_id" :options="typeCompteurs" optionLabel="name" optionValue="id" placeholder="Select compteur" class="w-full" />
                            </div>
                            <div>
                                <label class="block font-bold mb-2">Type Carburant</label>
                                <Dropdown v-model="modele.type_carburant_id" :options="typeCarburants" optionLabel="name" optionValue="id" placeholder="Select carburant" class="w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block font-bold mb-2">CO₂</label>
                                <InputNumber
                                    v-model="modele.CO2"
                                    class="w-full"
                                    mode="decimal"
                                    minFractionDigits="3"
                                />
                            </div>
                            <div>
                                <label class="block font-bold mb-2">Cylindre</label>
                                <InputNumber
                                    v-model="modele.Cylindre"
                                    class="w-full"
                                    mode="decimal"
                                    minFractionDigits="3"
                                />
                            </div>
                            <div>
                                <label class="block font-bold mb-2">Poids</label>
                                <InputNumber
                                    v-model="modele.Poids"
                                    class="w-full"
                                    mode="decimal"
                                    minFractionDigits="3"
                                />
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveModele" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteModeleDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="modele">Are you sure you want to delete <b>{{ modele.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteModeleDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteModele" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
