<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { useRoleStore } from "@/store/role";
import router from "@/router";
import { useConfirm } from "primevue";
import { useAuthStore } from "@/store/auth";

const roleStore = useRoleStore();
const toast = useToast();
const roles = ref();
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref();
const { permissions } = useAuthStore();

const confirm = useConfirm();

const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS }
};

const canDo = (entity, action) => {
    const Permission = `${entity} ${action}`;
    const allPermission = `${entity} ${action} (all)`;

    if (permissions.includes(allPermission) || permissions.includes(Permission)) return true;

    return false;
};

const filters = ref({ ...defaultFilters });

const fetchRoles = async (params = {}) => {
    loading.value = true;
    try {
        const data = await roleStore.getRoles(params, toast);
        roles.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchRoles(event);
};

const clearFilters = () => {
    filters.value = { ...defaultFilters };
    if (lazyParams.value.filters) lazyParams.value.filters = null;
    fetchRoles(lazyParams.value);
};

const filterChips = computed(() => {
    const activeFilters = lazyParams.value?.filters ?? {};
    return Object.entries(activeFilters)
        .filter(([_, filter]) => filter.value)
        .map(([field, filter]) => ({
            field,
            label: `${field}: ${filter.value instanceof Date
                ? filter.value.toLocaleDateString()
                : filter.value}`
        }));
});

const deleteRole = (roleId) => {
    confirm.require({
        message: 'Are you sure you want to delete this role?',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: async () => {
            loading.value = true;
            try {
                await roleStore.deleteRole(roleId, toast);
                fetchRoles(lazyParams.value);
            } finally {
                loading.value = false;
            }
        },
        reject: () => {
            // Optional: handle rejection
        }
    });
};
const removeFilter = (field) => {
    if (filters.value[field]) filters.value[field].value = null;
    if (lazyParams.value?.filters?.[field]) lazyParams.value.filters[field].value = null;
    fetchRoles(lazyParams.value);
};

onMounted(() => {
    fetchRoles();
});
</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <!-- Header Summary -->
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">{{ totalRecords }} Roles</h1>
                <div class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-border w-10 h-10">
                    <i class="pi pi-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Main Table Section -->
        <div class="col-span-12">
            <div class="card mb-0">
                <!-- Toolbar -->
                <Toolbar class="mb-6">
                    <template #start>
                        <div class="flex gap-4">
                            <Button
                                v-if="canDo('roles', 'create')"
                                icon="pi pi-plus"
                                label="New"
                                @click="router.push({ name: 'createRole' })"
                            />
                            <Button icon="pi pi-filter-slash" label="Clear" variant="outlined" @click="clearFilters" />
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
                        <Button label="Export" icon="pi pi-file-export" />
                    </template>
                </Toolbar>

                <!-- Data Table -->
                <DataTable
                    :value="roles"
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
                    <template #empty>No roles found.</template>

                    <Column field="id" header="ID" dataType="numeric" style="max-width: 4rem" sortable />

                    <Column field="name" header="Name" style="min-width: 12rem" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText
                                v-model="filterModel.value"
                                @input="filterCallback()"
                                placeholder="Search by name"
                            />
                        </template>
                    </Column>

                    <Column header="Permissions" field="permissions" style="min-width: 40rem">
                        <template #body="{ data }">
                            <div class="flex flex-wrap gap-2 items-center">
                                <Tag
                                    v-for="permission in data.permissions.slice(0, 5)"
                                    :key="permission.id"
                                    :value="permission.name"
                                    severity="info"
                                    class="whitespace-nowrap"
                                />
                                <span v-if="data.permissions_count > 5" class="text-xs text-gray-500">
                                    +{{ data.permissions_count - 5 }} more
                                </span>
                            </div>
                        </template>
                    </Column>

                    <Column field="created_at" header="Created at" dataType="date" style="min-width: 12rem" sortable>
                        <template #body="{ data }">
                            <span class="whitespace-nowrap">
                                {{ new Date(data.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) }}
                            </span>
                        </template>
                        <template #filter="{ filterModel, filterCallback }">
                            <Calendar
                                v-model="filterModel.value"
                                @date-select="filterCallback()"
                                placeholder="mm/dd/yy"
                                dateFormat="mm/dd/yy"
                            />
                        </template>
                    </Column>

                    <Column :exportable="false" header="Action" alignFrozen="right" style="min-width: 12rem" frozen>
                        <template #body="{ data }">
                            <Button
                                v-if="canDo('roles', 'edit')"
                                icon="pi pi-pencil"
                                variant="outlined"
                                rounded
                                severity="success"
                                class="mr-2"
                                @click="router.push({ name: 'editRole', params: { id: data.id } })"
                            />
                            <Button
                                v-if="canDo('roles', 'view')"
                                icon="pi pi-eye"
                                variant="outlined"
                                rounded
                                severity="info"
                                class="mr-2"
                                @click="router.push({ name: 'showRole', params: { id: data.id } })"
                            />
                            <Button
                                v-if="canDo('roles', 'delete')"
                                icon="pi pi-trash"
                                variant="outlined"
                                rounded
                                severity="danger"
                                @click="deleteRole(data.id)"
                            />
                        </template>
                    </Column>

                </DataTable>

                <ConfirmDialog />

            </div>
        </div>
    </div>
</template>

