<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();

const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const modeles = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const drawerLoading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// drawers/dialogs
const modeleDrawer = ref(false);
const deleteModeleDialog = ref(false);

// form
const modele = ref({});
const submitted = ref(false);
const saving = ref(false);

// options
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

async function fetchOptions() {
    marques.value = (await paramsParcStore.getMarques({}, toast)).data;
    gammes.value = (await paramsParcStore.getGammes({}, toast)).data;
    typeCompteurs.value = (await paramsGenereauxStore.getTypeCompteurs({}, toast)).data;
    typeCarburants.value = (await paramsGenereauxStore.getTypeCarburants({}, toast)).data;
}

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
async function openNew() {
    drawerLoading.value = true;
    modeleDrawer.value = true;

    try {
        // Fetch the options again
        await fetchOptions();

        modele.value = {};
        submitted.value = false;
        modeleDrawer.value = true;
    } finally {
        drawerLoading.value = false;
    }
}

function hideDrawer() {
    modeleDrawer.value = false;
    submitted.value = false;
}

async function saveModele() {
    submitted.value = true;
    if (!modele.value.name?.trim()) return;

    saving.value = true;
    try {
        if (modele.value.id) {
            await paramsParcStore.updateModeles(modele.value.id, modele.value, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Modele updated", life: 3000 });
        } else {
            await paramsParcStore.createModeles(modele.value, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Modele created", life: 3000 });
        }
        modeleDrawer.value = false;
        fetchModeles(lazyParams.value);
    } catch (err) {
        console.error(err);
    } finally {
        saving.value = false;
    }
}

async function editModele(m) {
    drawerLoading.value = true;
    modeleDrawer.value = true;
    try {
        // Refresh options before opening drawer
        await fetchOptions();

        const response = await paramsParcStore.getModeleById(m.id, toast);
        const data = response.data;

        modele.value = {
            ...data,
            marque_id: data.marque?.id || null,
            gamme_id: data.gamme?.id || null,
            type_compteur_id: data.type_compteur?.id || null,
            type_carburant_id: data.type_carburant?.id || null,
        };

        submitted.value = false;
    } catch (err) {
        modeleDrawer.value = false;
    } finally {
        drawerLoading.value = false;
    }
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
                        <!-- Body cell -->
                        <template #body="{ data }">
                            <div class="flex items-center gap-4 pl-4">
                                <Image v-if="data.marque?.image_url" :src="data.marque?.image_url" class="w-10 rounded" preview />
                                <span>{{ data.marque?.name }}</span>
                            </div>
                        </template>

                        <!-- Filter  -->
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="marques"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            >
                                <template #option="{ option }">
                                    <div class="flex items-center gap-4 pl-4">
                                        <Image v-if="option.image_url" :src="option.image_url" class="w-6 rounded" />
                                        <span>{{ option.name }}</span>
                                    </div>
                                </template>
                            </MultiSelect>
                        </template>
                    </Column>

                    <Column header="Gamme" field="gamme.name" filterField="gamme__name" sortable style="min-width: 12rem">
                        <template #body="{ data }"><span>{{ data.gamme?.name }}</span></template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect v-model="filterModel.value" @change="filterCallback()"
                                         :options="gammes" optionLabel="name" optionValue="name" placeholder="Any Gamme" class="w-full" />
                        </template>
                    </Column>
                    <Column header="Type Compteur" field="type_compteur.name" filterField="typeCompteur__name" sortable style="min-width: 12rem">
                        <template #body="{ data }"><span>{{ data.type_compteur?.name }}</span></template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect v-model="filterModel.value" @change="filterCallback()"
                                         :options="typeCompteurs" optionLabel="name" optionValue="name" placeholder="Any Compteur" class="w-full" />
                        </template>
                    </Column>
                    <Column header="Type Carburant" field="type_carburant.name" filterField="typeCarburant__name" sortable style="min-width: 12rem">
                        <template #body="{ data }"><span>{{ data.type_carburant?.name }}</span></template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect v-model="filterModel.value" @change="filterCallback()"
                                         :options="typeCarburants" optionLabel="name" optionValue="name" placeholder="Any Carburant" class="w-full" />
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editModele(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteModele(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Drawer -->
                <Drawer v-model:visible="modeleDrawer" header="Modele Details" position="right" class="!w-full md:!w-3/4 lg:!w-2/3">
                        <div v-if="drawerLoading" class="flex flex-col gap-6 p-4"  >
                            <p>Loading...</p>
                        </div>
                        <div v-if="!drawerLoading" class="flex flex-col gap-6 p-4" >
                            <div class="flex flex-col gap-6 p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Marque Dropdown -->
                                    <div>
                                        <label class="block font-bold mb-2">Marque</label>
                                        <Dropdown
                                            v-model="modele.marque_id"
                                            :options="marques"
                                            optionLabel="name"
                                            optionValue="id"
                                            placeholder="Select Marque"
                                            class="w-full"
                                        />
                                    </div>

                                    <!-- Selected Marque Image -->
                                    <div class="flex justify-center">
                                        <div v-if="modele.marque_id">
                                            <Image
                                                v-if="marques.find(m => m.id === modele.marque_id)?.image_url"
                                                :src="marques.find(m => m.id === modele.marque_id)?.image_url"
                                                preview
                                                alt="Marque Image"
                                                class="rounded max-w-20 object-contain"
                                            />
                                            <span v-else class="text-gray-400 italic">No Image</span>
                                        </div>
                                        <div v-else class="text-gray-400 italic">No Image</div>
                                    </div>
                                </div>

                                <!-- Name -->

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-bold mb-2">Name</label>
                                        <InputText v-model.trim="modele.name" required :invalid="submitted && !modele.name" class="w-full" />
                                        <small v-if="submitted && !modele.name" class="text-red-500">Name is required.</small>
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Places</label>
                                        <InputNumber v-model="modele.places" class="w-full" mode="decimal" minFractionDigits="0" />
                                    </div>
                                </div>

                                <!-- Dropdowns -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-bold mb-2">Gamme</label>
                                        <Dropdown v-model="modele.gamme_id" :options="gammes" optionLabel="name" optionValue="id" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Type Compteur</label>
                                        <Dropdown v-model="modele.type_compteur_id" :options="typeCompteurs" optionLabel="name" optionValue="id" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Type Carburant</label>
                                        <Dropdown v-model="modele.type_carburant_id" :options="typeCarburants" optionLabel="name" optionValue="id" class="w-full" />
                                    </div>
                                </div>

                                <!-- Numeric Inputs Row 1 -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-bold mb-2">Puissance CV</label>
                                        <InputNumber v-model="modele.puissance_cv" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Puissance DIN</label>
                                        <InputNumber v-model="modele.puissance_din" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Cylindre</label>
                                        <InputNumber v-model="modele.cylindre" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                </div>

                                <!-- Numeric Inputs Row 2 -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-bold mb-2">Poids Vide</label>
                                        <InputNumber v-model="modele.poids_vide" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Poids TC</label>
                                        <InputNumber v-model="modele.poids_tc" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Charge Utile</label>
                                        <InputNumber v-model="modele.charge_utile" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                </div>

                                <!-- Numeric Inputs Row 3 -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-bold mb-2">Consommation Min</label>
                                        <InputNumber v-model="modele.consommation_min" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Consommation Max</label>
                                        <InputNumber v-model="modele.consommation_max" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Consommation Moy</label>
                                        <InputNumber v-model="modele.consommation_moy" class="w-full" mode="decimal" minFractionDigits="2" />
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end gap-4 mt-4">
                                    <Button label="Cancel" icon="pi pi-times" text @click="hideDrawer" />
                                    <Button label="Save" icon="pi pi-check" :disabled="saving" :loading="saving" @click="saveModele" />
                                </div>
                            </div>
                        </div>
                </Drawer>

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
