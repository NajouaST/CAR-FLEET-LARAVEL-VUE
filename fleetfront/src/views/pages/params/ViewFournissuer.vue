<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const fournisseurs = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const fournisseurDialog = ref(false);
const deleteFournisseurDialog = ref(false);

// form
const fournisseur = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchFournisseurs();
});

// fetch fournisseurs
const fetchFournisseurs = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getFournisseurs(params, toast);
        fournisseurs.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchFournisseurs(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchFournisseurs(lazyParams.value);
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
    fetchFournisseurs(lazyParams.value);
};

// CRUD actions
function openNew() {
    fournisseur.value = {};
    submitted.value = false;
    fournisseurDialog.value = true;
}

function hideDialog() {
    fournisseurDialog.value = false;
    submitted.value = false;
}

async function saveFournisseur() {
    submitted.value = true;
    if (!fournisseur.value.name?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("name", fournisseur.value.name);
        formData.append("email", fournisseur.value.email);
        formData.append("tel", fournisseur.value.tel);
        formData.append("adresse", fournisseur.value.adresse);

        if (fournisseur.value.id) {
            await paramsGenereauxStore.updateFournisseur(fournisseur.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Fournisseur updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createFournisseur(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Fournisseur created", life: 3000 });
        }
        fournisseurDialog.value = false;
        fetchFournisseurs(lazyParams.value);
    } catch (err) {
        console.error(err);
    }
}

function editFournisseur(m) {
    fournisseur.value = { ...m };
    fournisseurDialog.value = true;
}

function confirmDeleteFournisseur(m) {
    fournisseur.value = m;
    deleteFournisseurDialog.value = true;
}

async function deleteFournisseur() {
    await paramsGenereauxStore.deleteFournisseur(fournisseur.value.id, toast); // ✅ FIXED
    deleteFournisseurDialog.value = false;
    fetchFournisseurs(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Fournisseur : {{ totalRecords }}</h1>
                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-users text-blue-500 text-xl"></i>
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
                    :value="fournisseurs"
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
                    <template #empty>No Fournisseurs found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />

                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()"
                                       placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column field="email" header="Email" sortable style="min-width: 12rem" />
                    <Column field="tel" header="Tel" sortable style="min-width: 12rem" />
                    <Column field="adresse" header="Adresse" sortable style="min-width: 12rem" />

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editFournisseur(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger"
                                    @click="confirmDeleteFournisseur(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="fournisseurDialog" :style="{ width: '600px' }" header="Fournisseur Details"
                        modal>
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="fournisseur.name" required autofocus
                                       :invalid="submitted && !fournisseur.name" class="w-full" />
                            <small v-if="submitted && !fournisseur.name" class="text-red-500">Name is required.</small>
                        </div>

                        <div>
                            <label for="email" class="block font-bold mb-3">Email</label>
                            <InputText id="email" v-model.trim="fournisseur.email" required
                                       :invalid="submitted && !fournisseur.email" class="w-full" />
                            <small v-if="submitted && !fournisseur.email" class="text-red-500">Email is
                                required.</small>
                        </div>

                        <div>
                            <label for="tel" class="block font-bold mb-3">Tel</label>
                            <InputText id="tel" v-model.trim="fournisseur.tel" required
                                       :invalid="submitted && !fournisseur.tel" class="w-full" />
                            <small v-if="submitted && !fournisseur.tel" class="text-red-500">Tel is required.</small>
                        </div>

                        <div>
                            <label for="adresse" class="block font-bold mb-3">Adresse</label>
                            <InputText id="adresse" v-model.trim="fournisseur.adresse" required
                                       :invalid="submitted && !fournisseur.adresse" class="w-full" />
                            <small v-if="submitted && !fournisseur.adresse" class="text-red-500">Adresse is
                                required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveFournisseur" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteFournisseurDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="fournisseur">Are you sure you want to delete <b>{{ fournisseur.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteFournisseurDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteFournisseur" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
