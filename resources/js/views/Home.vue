<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useToast } from "@/lib/toast";

const notes = ref([]);
const deadlines = ref([]);
const { success, error } = useToast();

async function fetchNotes() {
    try {
        const res = await axios.get("/api/notes");
        notes.value = res.data.data;
    } catch (err) {
        error("Error loading notes");
    }
}

async function fetchDeadlines() {
    try {
        const res = await axios.get("/api/deadlines");
        deadlines.value = res.data.data;
    } catch (err) {
        error("Error loading deadlines");
    }
}

onMounted(() => {
    fetchNotes();
    fetchDeadlines();
});

// --- Computed stats ---
const totalDeadlines = computed(() => deadlines.value.length);
const completedDeadlines = computed(() =>
    deadlines.value.filter((d) => d.status === 2).length
);
const cancelledDeadlines = computed(() =>
    deadlines.value.filter((d) => d.status === 3).length
);
const overdueDeadlines = computed(() =>
    deadlines.value.filter((d) => d.status === 4).length
);
const upcomingDeadlines = computed(
    () =>
        totalDeadlines.value -
        (completedDeadlines.value +
            cancelledDeadlines.value +
            overdueDeadlines.value)
);

// --- Helpers ---
function formatDate(dateStr) {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

function getStatusText(status) {
    switch (status) {
        case 1:
            return "In Progress";
        case 2:
            return "Completed";
        case 3:
            return "Cancelled";
        case 4:
            return "Overdue";
        default:
            return "Unknown";
    }
}

function getStatusColor(status) {
    switch (status) {
        case 1:
            return "bg-blue-100 text-blue-800";
        case 2:
            return "bg-green-100 text-green-800";
        case 3:
            return "bg-gray-200 text-gray-700";
        case 4:
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-600";
    }
}
</script>

<template>
    <div class="p-6 max-w-6xl mx-auto space-y-6">
        <!-- Dashboard Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div
                class="bg-purple-500 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition flex flex-col justify-between"
            >
                <h2
                    class="text-xl font-bold mb-2 min-h-[48px] flex items-center"
                >Total Notes</h2>
                <p class="text-4xl font-extrabold leading-tight">
                    {{ notes.length }}
                </p>
            </div>

            <div
                class="bg-green-500 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition flex flex-col justify-between"
            >
                <h2
                    class="text-xl font-bold mb-2 min-h-[48px] flex items-center"
                >Total Deadlines</h2>
                <p class="text-4xl font-extrabold leading-tight">
                    {{ totalDeadlines }}
                </p>
            </div>

            <div
                class="bg-blue-500 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition flex flex-col justify-between"
            >
                <h2
                    class="text-xl font-bold mb-2 min-h-[48px] flex items-center"
                >Upcoming Deadlines</h2>
                <p class="text-4xl font-extrabold leading-tight">
                    {{ upcomingDeadlines }}
                </p>
            </div>

            <div
                class="bg-red-400 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition flex flex-col justify-between"
            >
                <h2
                    class="text-xl font-bold mb-2 min-h-[48px] flex items-center"
                >Overdue Deadlines</h2>
                <p class="text-4xl font-extrabold leading-tight">
                    {{ overdueDeadlines }}
                </p>
            </div>
        </div>

        <!-- Grid 2 columns: Notes | Deadlines -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notes -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Latest Notes</h3>
                <div class="space-y-3">
                    <a
                        v-for="note in notes.slice(0, 4)"
                        :key="note.id"
                        :href="`http://personal_notes.test/note/${note.id}`"
                        class="block p-4 rounded-lg border-l-4 hover:shadow-lg hover:-translate-y-1 hover:bg-purple-100 transition flex justify-between items-center"
                        :class="
              note.status === 2
                ? 'border-green-500 bg-green-50'
                : 'border-purple-500 bg-purple-50'
            "
                    >
                        <div>
                            <h4 class="font-semibold">{{ note.title }}</h4>
                            <p class="text-gray-600 text-sm">
                                Topic: {{ note.topic ? note.topic.name : 'No topic' }}
                            </p>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-semibold"
                            :class="
                note.status === 2
                  ? 'bg-green-500 text-white'
                  : 'bg-purple-500 text-white'
              "
                        >
              {{ note.status === 2 ? 'Done' : 'Draft' }}
            </span>
                    </a>
                </div>
            </div>

            <!-- Deadlines -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Recent Deadlines</h3>
                <div class="space-y-3">
                    <a
                        v-for="deadline in deadlines.slice(0, 4)"
                        :key="deadline.id"
                        :href="`http://personal_notes.test/deadline/${deadline.id}`"
                        class="block p-4 rounded-lg border-l-4 hover:shadow-lg hover:-translate-y-1 transition flex justify-between items-center"
                        :class="{
              'border-blue-500 bg-blue-50': deadline.status === 1,
              'border-green-500 bg-green-50': deadline.status === 2,
              'border-gray-400 bg-gray-50': deadline.status === 3,
              'border-red-500 bg-red-50': deadline.status === 4
            }"
                    >
                        <div>
                            <h4 class="font-semibold">{{ deadline.title }}</h4>
                            <p class="text-gray-600 text-sm">
                                Due: {{ formatDate(deadline.subtasks_min_due_date) }}
                            </p>
                        </div>
                        <span
                            class="px-2 py-1 rounded-full text-xs font-semibold"
                            :class="getStatusColor(deadline.status)"
                        >
              {{ getStatusText(deadline.status) }}
            </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
