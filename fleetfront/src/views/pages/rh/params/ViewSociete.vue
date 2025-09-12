<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

// Import PrimeVue components
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const societes = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});
const logoPreview = ref(null);

// dialogs
const societeDialog = ref(false);
const deleteSocieteDialog = ref(false);

// form
const societe = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    nom: { value: null, matchMode: FilterMatchMode.CONTAINS },
    description: { value: null, matchMode: FilterMatchMode.CONTAINS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchSocietes();
});

// fetch societes
const fetchSocietes = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getSocietes(params, toast);
        societes.value = data?.data ?? [];
        totalRecords.value = data?.totalRecords ?? 0;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchSocietes(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchSocietes(lazyParams.value);
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
    fetchSocietes(lazyParams.value);
};

// CRUD actions
function openNew() {
    societe.value = {
        nom: '',
        description: '',
        logo_path: null
    };
    submitted.value = false;
    societeDialog.value = true;
}

function hideDialog() {
    societeDialog.value = false;
    submitted.value = false;
}

function handleLogoUpload(event) {
    const file = event.files[0];
    societe.value.logo_path = file;
    logoPreview.value = URL.createObjectURL(file);
    console.log("uploaded logo", societe.value.logo_path);
}

async function saveSociete() {
    submitted.value = true;
    if (!societe.value.nom?.trim()) return;

    try {
        const formData = new FormData();

        formData.append("nom", societe.value.nom || "");
        formData.append("description", societe.value.description || "");
        formData.append("logo_path", societe.value.logo_path);
        

        

        console.log('Sending FormData with:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }


        if (societe.value.id) {
            // update existing societe
            await paramsGenereauxStore.updateSociete(societe.value.id, formData, toast);
            

            toast.add({ severity: "success", summary: "Updated", detail: "Société updated", life: 3000 });
        } else {
            // create new societe
            

            await paramsGenereauxStore.createSociete(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Société created", life: 3000 });
        }

        societeDialog.value = false;
        fetchSocietes(lazyParams.value);
    } catch (err) {
        console.error('Error saving société:', err);
        if (err.response?.status === 422) {
            console.error('Validation errors:', err.response.data.errors);
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
            toast.add({ severity: "error", summary: "Error", detail: "Error saving société", life: 3000 });
        }
    }
}

function editSociete(s) {
    societe.value = { ...s };
    logoPreview.value = null;
    societeDialog.value = true;
}

function confirmDeleteSociete(s) {
    societe.value = s;
    deleteSocieteDialog.value = true;
}

async function deleteSociete() {
    await paramsGenereauxStore.deleteSociete(societe.value.id, toast);
    deleteSocieteDialog.value = false;
    toast.add({ severity: "success", summary: "Deleted", detail: "Société deleted", life: 3000 });
    fetchSocietes(lazyParams.value);
}



const onLogoClear = () => {
    societe.value.logo_path = null;
};



</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Sociétés : {{ totalRecords }} </h1>
                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-building text-blue-500 text-xl"></i>
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
                    :value="societes"
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
                    <template #empty>No Sociétés found.</template>

                    <Column field="id" header="ID" sortable style="min-width: 60px; max-width: 80px" frozen />

                    <Column field="nom" header="Nom" sortable style="min-width: 150px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>

                    <Column field="description" header="Description" sortable style="min-width: 200px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search description" />
                        </template>
                    </Column>

					<!-- logo -->
                    <Column field="logo_path" header="Logo" sortable style="min-width: 120px">
                        <template #body="{ data }">
                            <div v-if="data.logo_path" class="flex items-center gap-2">
                                <Image
                                    :src="data.logo_path"
                                    :alt="data.nom"
                                    class="w-12 h-12 object-cover rounded"
                                    preview
                                    @error="$event.target.style.display='none'"
                                />
                            </div>
                            <span v-else class="text-gray-400 text-sm">No logo</span>
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 120px" frozen>
                        <template #body="{ data }">
                            <Button icon="pi pi-pencil" outlined rounded severity="warn" class="mr-2" @click="editSociete(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="confirmDeleteSociete(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="societeDialog" :style="{ width: '50vw', maxWidth: '600px' }" header="Société Details" modal>
                    <div class="space-y-4">
                        <div>
                            <label for="nom" class="block font-medium mb-2">Nom *</label>
                            <InputText id="nom" v-model.trim="societe.nom" required :invalid="submitted && !societe.nom" class="w-full" />
                            <small v-if="submitted && !societe.nom" class="text-red-500">Nom is required.</small>
                        </div>

                        <div>
                            <label for="description" class="block font-medium mb-2">Description</label>
                            <Textarea id="description" v-model="societe.description" rows="3" class="w-full" />
                        </div>

                        <div>
                            <label for="logo_path" class="block font-medium mb-2">Logo</label>
                            <FileUpload
                                id="logo_path"
                                mode="basic"
                                :auto="true"
                                accept="image/*"
                                :maxFileSize="2048000"
                                @select="handleLogoUpload"
                                @clear="onLogoClear"
                                chooseLabel="Choose Logo"
                                cancelLabel="Clear"
                                class="w-full"
                            />
                            <small class="text-gray-600">Max file size: 2MB. Supported formats: JPEG, PNG, JPG, GIF, SVG</small>
                        </div>

                        <div v-if="logoPreview">
                            <label class="block font-medium mb-2">Current Logo</label>
                            <Image
                                :src="logoPreview"
                                :alt="societe.nom"
                                class="w-20 h-20 object-cover rounded border"
                                preview
                            />
                        </div>

                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveSociete" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteSocieteDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="societe">Are you sure you want to delete <b>{{ societe.nom }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteSocieteDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteSociete" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
