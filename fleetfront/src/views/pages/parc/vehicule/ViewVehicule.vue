<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { useParcStore } from "@/store/parc";
import { useParamsParcStore } from "@/store/paramsParc";
import { useParamsGenereauxStore } from "@/store/ParamsGeneraux";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";

const toast = useToast();
const router = useRouter();

const parcStore = useParcStore();
const paramsParcStore = useParamsParcStore();
const paramsGenereauxStore = useParamsGenereauxStore();
const authStore = useAuthStore();

// state
const vehicules = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref({});

// delete dialog
const deleteVehiculeDialog = ref(false);
const vehicule = ref({});

// options
const modeles = ref([]);
const familles = ref([]);
const fournisseurs = ref([]);

// filters
const defaultFilters = {
    immatriculation: { value: null, matchMode: FilterMatchMode.CONTAINS },
    chassis: { value: null, matchMode: FilterMatchMode.CONTAINS },
    modele__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    familleVehicule__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    fournisseur__name: { value: null, matchMode: FilterMatchMode.EQUALS },
    situation: { value: null, matchMode: FilterMatchMode.EQUALS },
    statut: { value: null, matchMode: FilterMatchMode.EQUALS },
    formule_achat: { value: null, matchMode: FilterMatchMode.EQUALS },
};
const filters = ref({ ...defaultFilters });
const authUserId = computed(() => authStore.user?.id);

// Permission checks
const canViewVehicule = (assigned_to = null) => {
    const perms = authStore.permissions || [];

    if (perms.includes("vehicules view (all)")) return true;
    if (perms.includes("vehicules view (own)") && assigned_to === authUserId.value) return true;
    return perms.includes("vehicules view");
};

const canEditVehicule = (assigned_to = null) => {
    const perms = authStore.permissions || [];

    if (perms.includes("vehicules edit (all)")) return true;
    if (perms.includes("vehicules edit (own)") && assigned_to === authUserId.value) return true;
    return perms.includes("vehicules edit");
};

const canDeleteVehicule = (assigned_to = null) => {
    const perms = authStore.permissions || [];

    if (perms.includes("vehicules delete (all)")) return true;
    if (perms.includes("vehicules delete (own)") && assigned_to === authUserId.value) return true;
    return perms.includes("vehicules delete");
};

const canCreateVehicule = computed(() =>
    authStore.permissions?.includes("vehicules create")
);

// fetch data
onMounted(() => {
    fetchVehicules();
    fetchOptions();
});

const fetchVehicules = async (params = {}) => {
    loading.value = true;
    try {
        const data = await parcStore.getVehicules(params, toast);
        vehicules.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

async function fetchOptions() {
    modeles.value = (await paramsParcStore.getModelesName({}, toast));
    familles.value = (await paramsGenereauxStore.getFamilleVehiculesName({}, toast)).data;
    fournisseurs.value = (await paramsGenereauxStore.getFournisseurs({}, toast)).data;
}

// table events
const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchVehicules(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchVehicules(lazyParams.value);
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
    fetchVehicules(lazyParams.value);
};

const getSituationSeverity = (situation) => {
    switch (situation) {
        case "en_exploitation": return "success";
        case "en_reparation": return "warn";
        case "accidentee": return "danger";
        case "arretee": return "secondary";
        case "litige": return "info";
        case "epave": return "contrast";
        case "vendue": return "primary";
        default: return "info";
    }
};

const getStatutSeverity = (statut) => {
    switch (statut) {
        case "libre": return "success";
        case "affectee": return "primary";
        case "hors_service": return "danger";
        case "vendue": return "secondary";
        default: return "info";
    }
};

const getFormuleAchatSeverity = (formule) => {
    switch (formule) {
        case "fond_propre": return "success";
        case "leasing": return "info";
        case "LLD": return "primary";
        default: return "info";
    }
};
// CRUD navigation
function openNew() {
    router.push({ name: "createVehicule" });
}

function editVehicule(v) {
    router.push({ name: "editVehicule", params: { id: v.id } });
}

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(date);
};

function showVehicule(v) {
    router.push({ name: "showVehicule", params: { id: v.id } });
}

function confirmDeleteVehicule(v) {
    vehicule.value = v;
    deleteVehiculeDialog.value = true;
}

async function deleteVehicule() {
    await parcStore.deleteVehicule(vehicule.value.id, toast);
    deleteVehiculeDialog.value = false;
    fetchVehicules(lazyParams.value);
}
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">Véhicules : {{ totalRecords }}</h1>
                <div
                    class="flex items-center justify-center bg-green-100 dark:bg-green-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-car text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="col-span-12">
            <div class="card">
                <!-- Toolbar -->
                <Toolbar class="mb-6">
                    <template #start>
                        <Button v-if="canCreateVehicule" label="New" icon="pi pi-plus" severity="secondary"
                                @click="openNew" />
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
                    :value="vehicules"
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
                    <template #empty>No véhicules found.</template>

                    <Column field="id" header="ID" sortable style="max-width: 4rem" />
                    <Column field="immatriculation" header="Immatriculation" sortable />
                    <Column field="chassis" header="Chassis" sortable />
                    <Column field="situation" header="Situation" filterField="situation" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.situation" :severity="getSituationSeverity(data.situation)" />
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                :options="[
                                    { label: 'En exploitation', value: 'en_exploitation' },
                                    { label: 'En réparation', value: 'en_reparation' },
                                    { label: 'Accidentée', value: 'accidentee' },
                                    { label: 'Arrêtée', value: 'arretee' },
                                    { label: 'Litige', value: 'litige' },
                                    { label: 'Épave', value: 'epave' },
                                    { label: 'Vendue', value: 'vendue' }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Any Situation"
                                class="w-full"
                                @change="filterCallback"
                            />
                        </template>
                    </Column>
                    <Column field="statut" header="Statut" filterField="statut" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.statut" :severity="getStatutSeverity(data.statut)" />
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                    v-model="filterModel.value"
                                :options="[
                                    { label: 'Libre', value: 'libre' },
                                    { label: 'Affectée', value: 'affectee' },
                                    { label: 'Hors service', value: 'hors_service' },
                                    { label: 'Vendue', value: 'vendue' }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Any Statut"
                                class="w-full"
                                @change="filterCallback"
                            />
                        </template>
                    </Column>
                    <Column header="Marque" field="modele.marque_id" style="min-width: 12rem" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-4 pl-4">
                                <Image v-if="data.marque?.image_url" :src="data.marque?.image_url" class="w-10 rounded" preview />
                                <span>{{ data.marque?.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Modèle" field="modele.name" filterField="modele__name" sortable>
                        <template #body="{ data }">{{ data.modele?.name }} </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                @change="filterCallback()"
                                :options="modeles"
                                optionLabel="name"
                                optionValue="name"
                                placeholder="Any Marque"
                                class="w-full"
                            >
                                <template #option="{ option }">
                                    <div class="flex items-center gap-4 pl-4">
                                        <span>{{ option.name }}</span>
                                    </div>
                                </template>
                            </MultiSelect>
                        </template>
                    </Column>
                    <Column header="Famille" field="famille.name" filterField="familleVehicule__name" sortable>
                        <template #body="{ data }">{{ data.famille?.name }}</template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect v-model="filterModel.value" @change="filterCallback()"
                                      :options="familles" optionLabel="name" optionValue="name"
                                      placeholder="Any Famille" class="w-full" />
                        </template>
                    </Column>
                    <Column header="Fournisseur" field="fournisseur.name" filterField="fournisseur__name" sortable>
                        <template #body="{ data }">{{ data.fournisseur?.name }}</template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect v-model="filterModel.value" @change="filterCallback()"
                                      :options="fournisseurs" optionLabel="name" optionValue="name"
                                      placeholder="Any Fournisseur" class="w-full" />
                        </template>
                    </Column>
                    <Column field="categorie" header="Categorie" sortable />
                    <Column field="formule_achat" header="Formule Achat" filterField="formule_achat" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.formule_achat" :severity="getFormuleAchatSeverity(data.formule_achat)" />
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <MultiSelect
                                v-model="filterModel.value"
                                :options="[
                                    { label: 'Fond propre', value: 'fond_propre' },
                                    { label: 'Leasing', value: 'leasing' },
                                    { label: 'LLD', value: 'LLD' }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Any Formule"
                                class="w-full"
                                @change="filterCallback"
                            />
                        </template>
                    </Column>
                    <Column field="date" header="Date" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            {{ formatDate(data.date) }}
                        </template>
                    </Column>

                    <Column field="date_cession" header="Date Cession" sortable style="min-width: 12rem">
                        <template #body="{ data }">
                            {{ formatDate(data.date_cession) }}
                        </template>
                    </Column>


                    <!-- Actions -->
                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button v-if="canViewVehicule(data.assigned_to)" icon="pi pi-eye" outlined rounded class="mr-2"
                                    @click="showVehicule(data)" />
                            <Button v-if="canEditVehicule(data.assigned_to)" icon="pi pi-pencil" outlined rounded severity="warn"
                                    class="mr-2" @click="editVehicule(data)" />
                            <Button v-if="canDeleteVehicule(data.assigned_to)" icon="pi pi-trash" outlined rounded severity="danger"
                                    class="mr-2" @click="confirmDeleteVehicule(data)" />
                        </template>
                    </Column>
                </DataTable>

                <!-- Delete Dialog -->
                <Dialog v-model:visible="deleteVehiculeDialog" header="Confirm" modal>
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="vehicule">Are you sure you want to delete <b>{{ vehicule.immatriculation
                            }}</b>?</span>
                    </div>
                    <template #footer>
                        <Button label="No" icon="pi pi-times" text @click="deleteVehiculeDialog = false" />
                        <Button label="Yes" icon="pi pi-check" @click="deleteVehicule" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
