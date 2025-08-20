<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { FilterMatchMode } from "@primevue/core/api";
const toast = useToast();
const paramsGenereauxStore = useParamsGenereauxStore();

// state
const typeCarburants = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// dialogs
const typeCarburantDialog = ref(false);
const deleteTypeCarburantDialog = ref(false);

// form
const typeCarburant = ref({});
const submitted = ref(false);

// filters
const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
};
const filters = ref({ ...defaultFilters });


onMounted(() => {
    fetchTypeCarburants();
});

// fetch typeCarburants
const fetchTypeCarburants = async (params = {}) => {
    loading.value = true;
    try {
        const data = await paramsGenereauxStore.getTypeCarburants(params, toast);
        typeCarburants.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchTypeCarburants(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchTypeCarburants(lazyParams.value);
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
    fetchTypeCarburants(lazyParams.value);
};

// CRUD actions
function openNew() {
    typeCarburant.value = {};
    submitted.value = false;
    typeCarburantDialog.value = true;
}

function hideDialog() {
    typeCarburantDialog.value = false;
    submitted.value = false;
}

async function saveTypeCarburant() {
    submitted.value = true;
    if (!typeCarburant.value.name?.trim()) return;

    try {
        const formData = new FormData();
        formData.append("name", typeCarburant.value.name);
        if (typeCarburant.value.id) {
            await paramsGenereauxStore.updateTypeCarburant(typeCarburant.value.id, formData, toast);
            toast.add({ severity: "success", summary: "Updated", detail: "Type Carburant updated", life: 3000 });
        } else {
            await paramsGenereauxStore.createTypeCarburant(formData, toast);
            toast.add({ severity: "success", summary: "Created", detail: "Type Carburant created", life: 3000 });
        }
        typeCarburantDialog.value = false;
        fetchTypeCarburants(lazyParams.value);
    } catch (err) {
        console.error(err);
    }
}

function editTypeCarburant(m) {
    typeCarburant.value = {
        ...m,
        image: null,
        imagePreview: null,
    };
    typeCarburantDialog.value = true;
}

function confirmDeleteTypeCarburant(m) {
    typeCarburant.value = m;
    deleteTypeCarburantDialog.value = true;
}

async function deleteTypeCarburant() {
    await paramsGenereauxStore.deleteTypeCarburant(typeCarburant.value.id, toast);
    deleteTypeCarburantDialog.value = false;
    fetchTypeCarburants(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Type Carburant : {{ totalRecords }} </h1>
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
                    :value="typeCarburants"
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
                    <template #empty>No Type Carburants found.</template>

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
                                    @click="editTypeCarburant(data)" />
                            <Button icon="pi pi-trash" outlined rounded severity="danger" class="mr-2"
                                    @click="confirmDeleteTypeCarburant(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Create/Edit Dialog -->
                <Dialog v-model:visible="typeCarburantDialog" :style="{ width: '600px' }" header="Type Carburant Details" modal>
                    <div class="flex flex-col gap-6">
                        <div>
                            <label for="name" class="block font-bold mb-3">Name</label>
                            <InputText id="name" v-model.trim="typeCarburant.name" required autofocus
                                       :invalid="submitted && !typeCarburant.name" class="w-full" />
                            <small v-if="submitted && !typeCarburant.name" class="text-red-500">Name is required.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Save" icon="pi pi-check" @click="saveTypeCarburant" />
                    </template>
                </Dialog>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteTypeCarburantDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="typeCarburant">Are you sure you want to delete <b>{{ typeCarburant.name }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteTypeCarburantDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteTypeCarburant" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
