<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import { useUserStore } from "@/store/user";
import router from "@/router";
import { useConfirm } from "primevue";
import { useAuthStore } from "@/store/auth";

const userStore = useUserStore();
const toast = useToast();
const users = ref();
const totalRecords = ref(0);
const loading = ref(false);
const perPage = ref(10);
const lazyParams = ref();
const { permissions, user } = useAuthStore();

const confirm = useConfirm();

const defaultFilters = {
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    is_active: { value: null, matchMode: FilterMatchMode.EQUALS },
    created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
    deleted_at: { value: null, matchMode: FilterMatchMode.DATE_AFTER }
};

const filters = ref({ ...defaultFilters });

const fetchUsers = async (params = {}) => {
    loading.value = true;
    try {
        const data = await userStore.getUsers(params, toast);
        users.value = data.data;
        totalRecords.value = data.totalRecords;
    } finally {
        loading.value = false;
    }
};

const onTableEvent = (event) => {
    lazyParams.value = event;
    fetchUsers(event);
};

const clearFilters = () => {
    filters.value = {
        name: { value: null, matchMode: FilterMatchMode.CONTAINS },
        email: { value: null, matchMode: FilterMatchMode.CONTAINS },
        is_active: { value: null, matchMode: FilterMatchMode.EQUALS },
        created_at: { value: null, matchMode: FilterMatchMode.DATE_IS },
        deleted_at: { value: null, matchMode: FilterMatchMode.DATE_AFTER }
    };
    if (lazyParams.value.filters) lazyParams.value.filters = filters.value;
    fetchUsers(lazyParams.value);
};

const filterChips = computed(() => {
    const activeFilters = lazyParams.value?.filters ?? {};
    return Object.entries(activeFilters)
        .filter(([_, filter]) => filter.value)
        .map(([field, filter]) => ({
            field,
            label: field === 'deleted_at'
                ? 'deleted'
                : `${field}: ${filter.value instanceof Date
                    ? filter.value.toLocaleDateString()
                    : filter.value}`
        }));
});

const deleteuser = (userId) => {
    confirm.require({
        message: 'Are you sure you want to delete this user?',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-success',
        rejectClass: 'p-button-danger',
        accept: async () => {
            loading.value = true;
            try {
                await userStore.deleteUser(userId, toast);
                fetchUsers(lazyParams.value);
            } finally {
                loading.value = false;
            }
        },
        reject: () => {
            // Optional: handle rejection
        }
    });
};

const permadeleteuser = (userId) => {
    confirm.require({
        message: 'Are you sure you want to permanently delete this user?',
        header: 'Permanently Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-success',
        rejectClass: 'p-button-danger',
        accept: async () => {
            loading.value = true;
            try {
                await userStore.permaDeleteUser(userId, toast);
                fetchUsers(lazyParams.value);
            } finally {
                loading.value = false;
            }
        },
        reject: () => {
            // Optional: handle rejection
        }
    });
};

const restoreuser = (userId) => {
    confirm.require({
        message: 'Are you sure you want to restore this user?',
        header: 'Restore Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-success',
        rejectClass: 'p-button-danger',
        accept: async () => {
            loading.value = true;
            try {
                await userStore.restoreUsers(userId, toast);
                fetchUsers(lazyParams.value);
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
    fetchUsers(lazyParams.value);
};

const Trash = () => {
    if (!lazyParams.value) {
        lazyParams.value = { filters: {} };
    } else if (!lazyParams.value.filters) {
        lazyParams.value.filters = {};
    }

    if (filters.value.deleted_at.value == null)
        filters.value.deleted_at.value = new Date('1970-01-01T00:00:00Z');
    else
        filters.value.deleted_at.value = null;

    lazyParams.value.filters = filters.value;

    fetchUsers(lazyParams.value); // Fetch users with updated filter
};

const canDo = (entity, action, resourceUserId = null) => {
    const Permission = `${entity} ${action}`;
    const allPermission = `${entity} ${action} (all)`;
    const ownPermission = `${entity} ${action} (own)`;

    if (permissions.includes(allPermission) || permissions.includes(Permission)) return true;

    if (permissions.includes(ownPermission) && user.id === resourceUserId) {
        return true;
    }

    return false;
};

onMounted(() => {
    fetchUsers();
});

</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <!-- Header Summary -->
        <div class="col-span-12">
            <div class="card flex justify-between items-center">
                <h1 class="text-muted-color font-medium">{{ totalRecords }} users</h1>
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
                                v-if="canDo('users', 'create')"
                                icon="pi pi-plus"
                                label="New"
                                @click="router.push({ name: 'createUser' })"
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
                        <div class="flex gap-4">
                            <Button label="Export" icon="pi pi-file-export" />
                            <Button icon="pi pi-trash" label="Trash" variant="outlined" severity="danger" @click="Trash" />
                        </div>
                    </template>
                </Toolbar>

                <!-- Data Table -->
                <DataTable
                    :value="users"
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
                    <template #empty>No users found.</template>

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

                    <Column field="email" header="Email" style="min-width: 12rem" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText
                                v-model="filterModel.value"
                                @input="filterCallback()"
                                placeholder="Search by name"
                            />
                        </template>
                    </Column>
                    <Column field="is_active" header="Active" dataType="boolean" bodyClass="text-center" sortable>
                        <template #body="{ data }">
                            <i class="pi" :class="{ 'pi-check-circle text-green-500 ': data.is_active, 'pi-times-circle text-red-500': !data.is_active }"></i>
                        </template>
                    </Column>
                    <Column header="Roles" field="role" style="min-width: 40rem">
                        <template #body="{ data }">
                            <div class="flex flex-wrap gap-2 items-center">
                                <Tag
                                    v-for="role in data.roles.slice(0, 5)"
                                    :key="role.id"
                                    :value="role.name"
                                    severity="info"
                                    class="whitespace-nowrap"
                                />
                                <span v-if="data.roles_count > 5" class="text-xs text-gray-500">
                                    +{{ data.roles_count - 5 }} more
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
                            <!-- For active users -->
                            <template v-if="!data.deleted_at">
                                <Button
                                    v-if="canDo('users', 'edit', data.id)"
                                    icon="pi pi-pencil"
                                    variant="outlined"
                                    rounded
                                    severity="success"
                                    class="mr-2"
                                    @click="router.push({ name: 'editUser', params: { id: data.id } })"
                                />
                                <Button
                                    v-if="canDo('users', 'view', data.id)"
                                    icon="pi pi-eye"
                                    variant="outlined"
                                    rounded
                                    severity="info"
                                    class="mr-2"
                                    @click="router.push({ name: 'showUser', params: { id: data.id } })"
                                />
                                <Button
                                    v-if="canDo('users', 'delete', data.id)"
                                    icon="pi pi-trash"
                                    variant="outlined"
                                    rounded
                                    severity="danger"
                                    @click="deleteuser(data.id)"
                                />
                            </template>

                            <!-- For trashed users -->
                            <template v-else>
                                <Button
                                    v-if="canDo('users', 'view', data.id)"
                                    icon="pi pi-eye"
                                    variant="outlined"
                                    rounded
                                    severity="info"
                                    class="mr-2"
                                    @click="router.push({ name: 'showUser', params: { id: data.id } })"
                                />
                                <Button
                                    v-if="canDo('users', 'delete', data.id)"
                                    icon="pi pi-refresh"
                                    variant="outlined"
                                    rounded
                                    class="mr-2"
                                    @click="restoreuser(data.id)"
                                />
                                <Button
                                    v-if="canDo('users', 'delete', data.id)"
                                    icon="pi pi-trash"
                                    variant="outlined"
                                    rounded
                                    severity="danger"
                                    @click="permadeleteuser(data.id)"
                                />
                            </template>
                        </template>
                    </Column>

                </DataTable>

                <ConfirmDialog />

            </div>
        </div>
    </div>
</template>

