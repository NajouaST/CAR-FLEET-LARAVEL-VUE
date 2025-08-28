<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { useParcStore } from "@/store/parc";
import { useAuthStore } from "@/store/auth";

const props = defineProps({
    vehiculeId: {
        type: [Number, String],
        required: true,
    },
});

const toast = useToast();
const parcStore = useParcStore();
const authStore = useAuthStore();

// state
const docs = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// drawers/dialogs
const docDrawer = ref(false);
const deleteDocDialog = ref(false);

// form
const doc = ref({});
const submitted = ref(false);
const saving = ref(false);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    note: { value: null, matchMode: FilterMatchMode.CONTAINS },
};
const filters = ref({ ...defaultFilters });

// Auth & permissions
const authUserId = computed(() => authStore.user?.id);

const canEditDoc = (assigned_to = null) => {
    const perms = authStore.permissions || [];
    if (perms.includes("vehicules edit (all)")) return true;
    if (perms.includes("vehicules edit (own)") && assigned_to === authUserId.value) return true;
    return perms.includes("vehicules edit");
};

const canDeleteDoc = (assigned_to = null) => {
    const perms = authStore.permissions || [];
    if (perms.includes("vehicules delete (all)")) return true;
    if (perms.includes("vehicules delete (own)") && assigned_to === authUserId.value) return true;
    return perms.includes("vehicules delete");
};

const canCreateDoc = computed(() =>
    authStore.permissions?.includes("vehicules create")
);

// fetch data
const fetchDocs = async (params = {}) => {
    loading.value = true;
    try {
        const mergedParams = {
            ...params,
            filters: {
                ...(params.filters || {}),
                vehicule_id: { value: props.vehiculeId, matchMode: "equals" },
            },
        };
        const data = await parcStore.getDocVehicules(mergedParams, toast);
        docs.value = data?.data ?? [];
        totalRecords.value = data?.totalRecords ?? 0;
    } finally {
        loading.value = false;
    }
};

onMounted(() => fetchDocs());
watch(() => props.vehiculeId, () => fetchDocs());

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchDocs(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchDocs(lazyParams.value);
};

const filterChips = computed(() => {
    const activeFilters = lazyParams.value?.filters ?? {};
    return Object.entries(activeFilters)
        .filter(([_, f]) => f.value)
        .map(([field, f]) => ({
            field,
            label: `${field}: ${f.value instanceof Date ? f.value.toLocaleDateString() : f.value}`,
        }));
});

const removeFilter = (field) => {
    if (filters.value[field]) filters.value[field].value = null;
    if (lazyParams.value?.filters?.[field]) lazyParams.value.filters[field].value = null;
    fetchDocs(lazyParams.value);
};

// CRUD
function openNew() {
    if (!canCreateDoc.value) return; // permission check
    if (doc.value?.preview) URL.revokeObjectURL(doc.value.preview);
    doc.value = { name: "", note: "", image: null, preview: null };
    submitted.value = false;
    docDrawer.value = true;
}

function hideDrawer() {
    if (doc.value?.preview) URL.revokeObjectURL(doc.value.preview);
    docDrawer.value = false;
    submitted.value = false;
    doc.value = {};
}

function editDoc(d) {
    if (!canEditDoc(d.assigned_to)) return; // permission check
    if (doc.value?.preview) URL.revokeObjectURL(doc.value.preview);
    doc.value = { ...d, image: null, preview: null };
    docDrawer.value = true;
}

function confirmDeleteDoc(d) {
    if (!canDeleteDoc(d.assigned_to)) return; // permission check
    doc.value = d;
    deleteDocDialog.value = true;
}

async function deleteDoc() {
    try {
        await parcStore.deleteDocVehicule(doc.value.id, toast);
        deleteDocDialog.value = false;
        fetchDocs(lazyParams.value);
    } finally {

    }
}

// File select
function onFileSelect(event) {
    const file = event.files[0];
    if (file) {
        doc.value.image = file;
        doc.value.imagePreview = URL.createObjectURL(file);
    }
}

// Save/create
async function saveDoc() {
    submitted.value = true;
    if (!doc.value.name?.trim()) return;
    saving.value = true;
    try {
        const formData = new FormData();
        formData.append("name", doc.value.name || "");
        formData.append("note", doc.value.note ?? "");
        formData.append("vehicule_id", props.vehiculeId);
        if (doc.value.image instanceof File) formData.append("image", doc.value.image);

        if (doc.value.id) {
            await parcStore.updateDocVehicule(doc.value.id, formData, toast);
        } else {
            await parcStore.createDocVehicule(formData, toast);
        }

        if (doc.value.preview) URL.revokeObjectURL(doc.value.preview);
        docDrawer.value = false;
        doc.value = {};
        fetchDocs(lazyParams.value);
    } finally {
        saving.value = false;
    }
}

// cleanup on unmount
onBeforeUnmount(() => {
    if (doc.value?.preview) URL.revokeObjectURL(doc.value.preview);
});
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card">
                <h1 class="text-muted-color font-medium">Documents : {{ totalRecords }}</h1>

                <!-- Toolbar -->
                <Toolbar class="mb-6">
                    <template #start>
                        <Button v-if="canCreateDoc" label="New" icon="pi pi-plus" severity="secondary" @click="openNew" />
                    </template>
                    <template #center>
                        <template v-if="filterChips.length > 0">
                            <Chip v-for="chip in filterChips" :key="chip.field" :label="chip.label" removable
                                  @remove="removeFilter(chip.field)" />
                        </template>
                        <span v-else class="text-gray-400 text-sm italic">No filters applied</span>
                    </template>
                    <template #end>
                        <Button label="Clear" icon="pi pi-filter-slash" outlined @click="clearFilters" />
                    </template>
                </Toolbar>

                <!-- DataTable -->
                <DataTable
                    :value="docs"
                    :lazy="true"
                    :loading="loading"
                    :paginator="true"
                    :rows="perPage"
                    :totalRecords="totalRecords"
                    :rowsPerPageOptions="[5,10,20,50]"
                    :sortOrder="1"
                    sortField="id"
                    v-model:filters="filters"
                    filterDisplay="row"
                    scrollable
                    @filter="onTableEvent"
                    @page="onTableEvent"
                    @sort="onTableEvent"
                >
                    <template #empty>No documents found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />
                    <Column field="name" header="Name" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column field="note" header="Note" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by note" />
                        </template>
                    </Column>
                    <Column header="Image" field="image_url">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Image v-if="data.image_url" :src="data.image_url" class="rounded" style="max-width:3rem" preview />
                                <span v-else class="text-gray-400 italic text-sm">No image</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Actions -->
                    <Column header="Action" alignFrozen="right" style="min-width:12rem" frozen>
                        <template #body="{ data }">
                            <Button v-if="canEditDoc(data.assigned_to)" icon="pi pi-pencil" rounded outlined severity="warn" class="mr-2" @click="editDoc(data)" />
                            <Button v-if="canDeleteDoc(data.assigned_to)" icon="pi pi-trash" rounded outlined severity="danger" class="mr-2" @click="confirmDeleteDoc(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Drawer -->
                <Drawer v-model:visible="docDrawer" header="Document Details" position="right" class="w-[600px]">
                    <div class="flex flex-col gap-6 p-4">
                        <div>
                            <label class="block font-bold mb-3">Name *</label>
                            <InputText v-model.trim="doc.name" required autofocus :invalid="submitted && !doc.name" class="w-full" />
                            <small v-if="submitted && !doc.name" class="text-red-500">Name is required.</small>
                        </div>

                        <div>
                            <label class="block font-bold mb-3">Note</label>
                            <Textarea v-model.trim="doc.note" class="w-full" />
                        </div>

                        <div>
                            <label class="block font-bold mb-3">Image</label>
                            <FileUpload mode="basic" @select="onFileSelect" customUpload auto severity="secondary" class="p-button-outlined" />
                            <Image v-if="doc.imagePreview || doc.image_url" :src="doc.imagePreview || doc.image_url" class="rounded m-2" preview />
                        </div>
                    </div>

                    <template #footer>
                        <div class="flex justify-end gap-2 p-4 border-t">
                            <Button label="Cancel" icon="pi pi-times" text @click="hideDrawer" />
                            <Button label="Save" icon="pi pi-check" :disabled="saving" :loading="saving" @click="saveDoc" />
                        </div>
                    </template>
                </Drawer>

                <!-- Delete Confirmation Dialog -->
                <Dialog v-model:visible="deleteDocDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="doc">Are you sure you want to delete <b>{{ doc.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteDocDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteDoc" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
