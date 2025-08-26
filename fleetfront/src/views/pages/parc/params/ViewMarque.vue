<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsParcStore } from "@/store/paramsParc";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();
const paramsParcStore = useParamsParcStore();

// state
const marques = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// drawers/dialogs
const marqueDrawer = ref(false);
const deleteMarqueDialog = ref(false);

// form
const marque = ref({});
const submitted = ref(false);
const saving = ref(false); // <-- track saving state

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchMarques();
});

// fetch marques
const fetchMarques = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsParcStore.getMarques(params, toast);
        marques.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchMarques(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchMarques(lazyParams.value);
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
    fetchMarques(lazyParams.value);
};

// CRUD actions
function openNew() {
    marque.value = {};
    submitted.value = false;
    marqueDrawer.value = true;
}

function hideDrawer() {
    marqueDrawer.value = false;
    submitted.value = false;
}

async function saveMarque() {
    submitted.value = true;
    if (!marque.value.name?.trim()) return;

    saving.value = true; // block button
    try {
        const formData = new FormData();
        formData.append("name", marque.value.name ?? "");

        if (marque.value.image) {
            formData.append("image", marque.value.image, marque.value.image.name ?? "upload.jpg");
        }

        if (marque.value.id) {
            await paramsParcStore.updateMarques(marque.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Marque updated", life: 3000 });
        } else {
            await paramsParcStore.createMarques(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Marque created", life: 3000 });
        }
        marqueDrawer.value = false;
        fetchMarques(lazyParams.value);
    } catch (err) {
        console.error(err);
    } finally {
        saving.value = false; // unblock button
    }
}

function editMarque(m) {
    marque.value = {
        ...m,
        image: null,
        imagePreview: null,
    };
    marqueDrawer.value = true;
}

function confirmDeleteMarque(m) {
    marque.value = m;
    deleteMarqueDialog.value = true;
}

async function deleteMarque() {
    await paramsParcStore.deleteMarque(marque.value.id, toast);
    deleteMarqueDialog.value = false;
    fetchMarques(lazyParams.value);
}

function onFileSelect(event) {
    const file = event.files[0];
    if (file) {
        marque.value.image = file;
        marque.value.imagePreview = URL.createObjectURL(file);
    }
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Marque : {{ totalRecords }}</h1>
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
                    :value="marques"
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
                    <template #empty>No marques found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />

                    <Column field="name" header="Name" sortable style="min-width: 12rem">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column header="Image" field="image_url" style="min-width: 4rem">
                        <template #body="{ data }">
                            <Image v-if="data.image_url" :src="data.image_url" class="rounded" style="max-width: 2rem" preview />
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2"
                                    @click="editMarque(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteMarque(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Drawer -->
                <Drawer v-model:visible="marqueDrawer" header="Marque Details" position="right" class="w-[600px]">
                    <div class="flex flex-col gap-6 p-4">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="marque.name" required autofocus
                                       :invalid="submitted && !marque.name" class="w-full" />
                            <small v-if="submitted && !marque.name" class="text-red-500">Name is required.</small>
                        </div>
                        <div>
                            <label for="image" class="block font-bold mb-3">Image</label>
                            <FileUpload mode="basic" @select="onFileSelect" customUpload auto severity="secondary"
                                        class="p-button-outlined" />
                            <Image v-if="marque.imagePreview || marque.image_url"
                                 :src="marque.imagePreview || marque.image_url"
                                 class="mt-2 rounded w-32" preview />
                        </div>
                    </div>

                    <template #footer>
                        <div class="flex justify-end gap-2 p-4 border-t">
                            <Button label="Cancel" icon="pi pi-times" text @click="hideDrawer" />
                            <Button label="Save" icon="pi pi-check"
                                    :disabled="saving" :loading="saving"
                                    @click="saveMarque" />
                        </div>
                    </template>
                </Drawer>

                <!-- Delete Confirmation Dialog -->
                <Dialog v-model:visible="deleteMarqueDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="marque">Are you sure you want to delete <b>{{ marque.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteMarqueDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteMarque" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
