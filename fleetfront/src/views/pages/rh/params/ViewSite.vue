<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const sites = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});
const editingRows = ref([]); // Add this for row editing

// dialogs
const siteDialog = ref(false);
const deleteSiteDialog = ref(false);

// form
const site = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    nom: { value: null, matchMode: FilterMatchMode.CONTAINS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchSites();
});

// fetch sites
const fetchSites = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getSites(params, toast);
        sites.value = data?.data ?? [];
        totalRecords.value = data?.totalRecords ?? 0;
    } finally {
        loading.value = false;
    }
};

// Row edit save handler
const onRowEditSave = async (event) => {
    const { newData, index } = event;
    
    try {
        const formData = new FormData();
        if(!newData.nom){
            toast.add({severity: "error", summary:"Erreur", detail:"Indiquer le nom avent la mise a jour!"});
            return;
        }
        formData.append("nom", newData.nom);
        
        await paramsGenereauxStore.updateSite(newData.id, formData, toast);
        
        // Update local data
        sites.value[index] = newData;
        
        toast.add({ severity: "success", summary: "Updated", detail: "Site updated successfully", life: 3000 });
    } catch (err) {
        console.error('Failed to update site:', err);
        toast.add({ severity: "error", summary: "Error", detail: "Failed to update site", life: 3000 });
        
        // Revert the change in UI if update fails
        sites.value[index] = event.data;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchSites(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchSites(lazyParams.value);
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
    fetchSites(lazyParams.value);
};

// CRUD actions
function openNew() {
    site.value = {
        nom: ''
    };
    submitted.value = false;
    siteDialog.value = true;
}

function hideDialog() {
    siteDialog.value = false;
    submitted.value = false;
}

async function saveSite() {
    submitted.value = true;
    if (!site.value.nom?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("nom", site.value.nom || "");
        
        if (site.value.id) {
            await paramsGenereauxStore.updateSite(site.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Site updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createSite(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Site created", life: 3000 });
        }

        siteDialog.value = false;
        fetchSites(lazyParams.value);
    } catch (err) {
        console.error('Failed to save site:', err);
        if (err.response?.status === 422) {
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
            toast.add({ severity: "error", summary: "Error", detail: "Failed to save site", life: 3000 });
        }
    }
}

function editSite(s) {
    site.value = { ...s };
    siteDialog.value = true;
}

function confirmDeleteSite(s) {
    site.value = s;
    deleteSiteDialog.value = true;
}

async function deleteSite() {
    await paramsGenereauxStore.deleteSite(site.value.id, toast);
    deleteSiteDialog.value = false;
    toast.add({ severity: "success", summary: "Deleted", detail: "Site deleted", life: 3000 });
    fetchSites(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Sites : {{ totalRecords }} </h1>
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

                <!-- DataTable with Row Editing -->
                <DataTable
                    v-model:editingRows="editingRows"
                    :value="sites"
                    :lazy="true"
                    :loading="loading"
                    :paginator="true"
                    :rows="perPage"
                    :totalRecords="totalRecords"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    :sortOrder="1"
                    sortField="id"
                    editMode="row"
                    dataKey="id"
                    @row-edit-save="onRowEditSave"
                    v-model:filters="filters"
                    filterDisplay="row"
                    scrollable
                    scrollHeight="400px"
                    scrollDirection="both"
                    @filter="onTableEvent"
                    @page="onTableEvent"
                    @sort="onTableEvent"
                    :pt="{
                        table: { style: 'min-width: 50rem' },
                        column: {
                            bodycell: ({ state }) => ({
                                style: state['d_editing'] && 'padding-top: 0.75rem; padding-bottom: 0.75rem'
                            })
                        }
                    }"
                >
                    <template #empty>No Sites found.</template>

                    <Column field="id" header="ID" sortable style="min-width: 60px; max-width: 80px" frozen />

                    <Column field="nom" header="Nom" sortable style="min-width: 150px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" fluid />
                        </template>
                    </Column>

                    <!-- Action Column with Row Editor -->
                    <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:right"></Column>
                    
                    <!-- Additional Action Buttons -->
                    <Column header="Actions" style="width: 15%; min-width: 10rem" bodyStyle="text-align:left">
                        <template #body="slotProps">
                            <Button 
                                icon="pi pi-trash" 
                                outlined rounded 
                                severity="danger" 
                                @click="confirmDeleteSite(slotProps.data)" 
                            />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog (Keep for complex edits) -->
                <Dialog v-model:visible="siteDialog" :style="{ width: '50vw', maxWidth: '600px' }" header="Site Details" modal>
                    <div class="space-y-4">
                        <div>
                            <label for="nom" class="block font-medium mb-2">Nom *</label>
                            <InputText id="nom" v-model.trim="site.nom" required :invalid="submitted && !site.nom" class="w-full" />
                            <small v-if="submitted && !site.nom" class="text-red-500">Nom is required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveSite" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteSiteDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="site">Are you sure you want to delete <b>{{ site.nom }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteSiteDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteSite" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>