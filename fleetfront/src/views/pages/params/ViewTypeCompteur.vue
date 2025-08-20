<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";
const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const typeCompteurs = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const typeCompteurDialog = ref(false);
const deleteTypeCompteurDialog = ref(false);

// form
const typeCompteur = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });


onMounted(() => {
    fetchTypeCompteurs();
});

// fetch typeCompteurs
const fetchTypeCompteurs = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getTypeCompteurs(params, toast);
        typeCompteurs.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchTypeCompteurs(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchTypeCompteurs(lazyParams.value);
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
    fetchTypeCompteurs(lazyParams.value);
};

// CRUD actions
function openNew() {
    typeCompteur.value = {};
    submitted.value = false;
    typeCompteurDialog.value = true;
}

function hideDialog() {
    typeCompteurDialog.value = false;
    submitted.value = false;
}

async function saveTypeCompteur() {
    submitted.value = true;
    if (!typeCompteur.value.name?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("name", typeCompteur.value.name);
        if (typeCompteur.value.id) {
            await paramsGenereauxStore.updateTypeCompteur(typeCompteur.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Type Compteur updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createTypeCompteur(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Type Compteur created", life: 3000 });
        }
        typeCompteurDialog.value = false;
        fetchTypeCompteurs(lazyParams.value);
    } catch (err) {
        console.error(err);
    }
}

function editTypeCompteur(m) {
    typeCompteur.value = {
        ...m,
        image: null,
        imagePreview: null,
    };
    typeCompteurDialog.value = true;
}

function confirmDeleteTypeCompteur(m) {
    typeCompteur.value = m;
    deleteTypeCompteurDialog.value = true;
}

async function deleteTypeCompteur() {
    await paramsGenereauxStore.deleteTypeCompteur(typeCompteur.value.id, toast);
    deleteTypeCompteurDialog.value = false;
    fetchTypeCompteurs(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Type Compteur : {{ totalRecords }} </h1>
                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <div class="card">
                <!-- Header Summary -->
                <!-- Toolbar -->
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
                <!-- DataTable -->
                <DataTable
                    :value="typeCompteurs"
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
                    <template #empty>No Type Compteurs found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />

                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>

                    <!--            <Column field="created_at" header="Created at" dataType="date" sortable style="min-width: 12rem">
                                    <template #body="{ data }">
                                        {{ new Date(data.created_at).toLocaleDateString() }}
                                    </template>
                                    <template #filter="{ filterModel, filterCallback }">
                                        <Calendar v-model="filterModel.value" @date-select="filterCallback()" placeholder="mm/dd/yy"
                                                  dateFormat="mm/dd/yy" />
                                    </template>
                                </Column>-->

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editTypeCompteur(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteTypeCompteur(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="typeCompteurDialog" :style="{ width: '600px' }" header="Type Compteur Details" modal>
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="typeCompteur.name" required autofocus
                                       :invalid="submitted && !typeCompteur.name" class="w-full" />
                            <small v-if="submitted && !typeCompteur.name" class="text-red-500">Name is required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveTypeCompteur" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteTypeCompteurDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="typeCompteur">Are you sure you want to delete <b>{{ typeCompteur.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteTypeCompteurDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteTypeCompteur" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
