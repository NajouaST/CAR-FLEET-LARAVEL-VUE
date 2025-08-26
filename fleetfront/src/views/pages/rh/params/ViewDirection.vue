<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";

const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const directions = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});
const editingRows = ref([]); // Add this for row editing

// dialogs
const directionDialog = ref(false);
const deleteDirectionDialog = ref(false);

// form
const direction = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    nom: { value: null, matchMode: FilterMatchMode.CONTAINS },
};
const filters = ref({ ...defaultFilters });

onMounted(() => {
    fetchDirections();
});

// fetch directions
const fetchDirections = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getDirections(params, toast);
        directions.value = data?.data ?? [];
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
        
        await paramsGenereauxStore.updateDirection(newData.id, formData, toast);
        
        // Update local data
        directions.value[index] = newData;
        
        toast.add({ severity: "success", summary: "Updated", detail: "Direction updated successfully", life: 3000 });
    } catch (err) {
        console.error('Failed to update direction:', err);
        toast.add({ severity: "error", summary: "Error", detail: "Failed to update direction", life: 3000 });
        
        // Revert the change in UI if update fails
        directions.value[index] = event.data;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchDirections(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchDirections(lazyParams.value);
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
    fetchDirections(lazyParams.value);
};

// CRUD actions
function openNew() {
    direction.value = {
        nom: ''
    };
    submitted.value = false;
    directionDialog.value = true;
}

function hideDialog() {
    directionDialog.value = false;
    submitted.value = false;
}

async function saveDirection() {
    submitted.value = true;
    if (!direction.value.nom?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("nom", direction.value.nom || "");
        
        if (direction.value.id) {
            await paramsGenereauxStore.updateDirection(direction.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "direction updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createDirection(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "direction created", life: 3000 });
        }

        directionDialog.value = false;
        fetchDirections(lazyParams.value);
    } catch (err) {
        console.error('Failed to save direction:', err);
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
            toast.add({ severity: "error", summary: "Error", detail: "Failed to save direction", life: 3000 });
        }
    }
}

function editDirection(s) {
    direction.value = { ...s };
    directionDialog.value = true;
}

function confirmDeleteDirection(s) {
    direction.value = s;
    deleteDirectionDialog.value = true;
}

async function deleteDirection() {
    await paramsGenereauxStore.deleteDirection(direction.value.id, toast);
    deleteDirectionDialog.value = false;
    toast.add({ severity: "success", summary: "Deleted", detail: "direction deleted", life: 3000 });
    fetchDirections(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">directions : {{ totalRecords }} </h1>
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
                    :value="directions"
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
                    <template #empty>No directions found.</template>

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
                                @click="confirmDeleteDirection(slotProps.data)" 
                            />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog (Keep for complex edits) -->
                <Dialog v-model:visible="directionDialog" :style="{ width: '50vw', maxWidth: '600px' }" header="direction Details" modal>
                    <div class="space-y-4">
                        <div>
                            <label for="nom" class="block font-medium mb-2">Nom *</label>
                            <InputText id="nom" v-model.trim="direction.nom" required :invalid="submitted && !direction.nom" class="w-full" />
                            <small v-if="submitted && !direction.nom" class="text-red-500">Nom is required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveDirection" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteDirectionDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="direction">Are you sure you want to delete <b>{{ direction.nom }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteDirectionDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteDirection" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>