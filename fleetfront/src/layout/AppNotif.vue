<script setup>
import { onMounted, ref, computed } from "vue";
import { useNotifStore } from "@/store/notif";
import router from "@/router";

const notifStore = useNotifStore();
const visible = ref(false);

const openNotif = async () => {
    visible.value = true;
    await notifStore.getNotif();
};

// Helper to format relative time
function formatNotifTime(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffMs = now - date;
    const diffMin = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMin / 60);

    if (diffMin < 1) return "just now";
    if (diffMin < 60) return `${diffMin} min ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;

    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Group notifications by date
const groupedNotifs = computed(() => {
    const groups = {};
    notifStore.notifs.forEach(notif => {
        const dateKey = new Date(notif.created_at).toLocaleDateString();
        if (!groups[dateKey]) {
            groups[dateKey] = [];
        }
        groups[dateKey].push(notif);
    });
    return groups;
});

function goToModele(notif) {
    console.log("aaaaaaaaaaaaaaaaaaaaaaaaaaa")
    if (notif.data?.id) {
        router.push({ name: "edit"+notif.data.modele , params: { id: notif.data.id } });
        visible.value = false; // close drawer
    }
}

onMounted(async () => {
    if (localStorage.getItem("token")) {
        await notifStore.getNotifCount();
        setInterval(async () => {
            await notifStore.getNotifCount();
        }, 30 * 1000);
    }
});
</script>


<template>
    <Drawer v-model:visible="visible" header="Notifications" position="right" class="!w-50 lg:!w-[30rem]">
        <div v-for="(dayNotifs, date) in groupedNotifs" :key="date" class="mb-6">
            <h3 class="text-lg font-semibold mb-3 text-muted-color">{{ date }}</h3>
            <ul class="p-0 mx-0 mt-0 mb-6 list-none">
                <li
                    v-for="notif in dayNotifs"
                    :key="notif.id"
                    @click="goToModele(notif)"
                    class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-800 p-2 rounded-lg transition"
                >
                    <div>
                        <span class="text-surface-900 dark:text-surface-0 font-medium mr-2 mb-1 md:mb-0">
                            {{ notif.data.modele }}
                        </span>
                        <div class="mt-1 text-muted-color">
                             id :{{ notif.data.id }} - {{ notif.data.action }}
                        </div>
                    </div>
                    <div class="mt-2 md:mt-0 flex items-center">
                        <span class="text-orange-500 ml-4 font-medium">
                            {{ formatNotifTime(notif.created_at) }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </Drawer>

    <template v-if="notifStore.count > 0">
        <OverlayBadge :value="notifStore.count" severity="danger" class="inline-flex">
            <button type="button" class="layout-topbar-action" @click="openNotif">
                <i class="pi pi-bell"></i>
            </button>
        </OverlayBadge>
    </template>
    <template v-else>
        <button type="button" class="layout-topbar-action" @click="openNotif">
            <i class="pi pi-bell"></i>
        </button>
    </template>
</template>
