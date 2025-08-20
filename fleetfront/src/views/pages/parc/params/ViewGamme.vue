<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsParcStore } from "@/store/paramsParc";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();
const paramsParcStore = useParamsParcStore();

// state
const gammes = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const gammeDialog = ref(false);
const deleteGammeDialog = ref(false);

// form
const gamme = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchGammes();
});

// fetch gammes
const fetchGammes = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsParcStore.getGammes(params, toast);
        gammes.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchGammes(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchGammes(lazyParams.value);
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
    fetchGammes(lazyParams.value);
};

// CRUD actions
function openNew() {
    gamme.value = {};
    submitted.value = false;
    gammeDialog.value = true;
}

function hideDialog() {
    gammeDialog.value = false;
    submitted.value = false;
}

async function saveGamme() {
    submitted.value = true;
    if (!gamme.value.name?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("name", gamme.value.name);

        if (gamme.value.id) {
            await paramsParcStore.updateGammes(gamme.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Gamme updated", life: 3000 });
        } else {
            await paramsParcStore.createGammes(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Gamme created", life: 3000 });
        }
        gammeDialog.value = false;
        fetchGammes(lazyParams.value);
    } catch (err) {
        console.error(err);
    }
}

function editGamme(g) {
    gamme.value = { ...g };
    gammeDialog.value = true;
}

function confirmDeleteGamme(g) {
    gamme.value = g;
    deleteGammeDialog.value = true;
}

async function deleteGamme() {
    await paramsParcStore.deleteGamme(gamme.value.id, toast);
    deleteGammeDialog.value = false;
    fetchGammes(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Gammes : {{ totalRecords }} </h1>
                <div class="flex items-center justify-center bg-green-100 dark:bg-green-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-list text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="col-span-12">
            <div class="card">
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
                    :value="gammes"
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
                    <template #empty>No gammes found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />

                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editGamme(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteGamme(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="gammeDialog" :style="{ width: '600px' }" header="Gamme Details" modal>
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="gamme.name" required autofocus
                                       :invalid="submitted && !gamme.name" class="w-full" />
                            <small v-if="submitted && !gamme.name" class="text-red-500">Name is required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveGamme" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteGammeDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="gamme">Are you sure you want to delete <b>{{ gamme.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteGammeDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteGamme" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
