<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Notebook, AlarmClock,Lightbulb } from 'lucide-vue-next'

const collapsed = ref(false)

function toggleSidebar() {
    collapsed.value = !collapsed.value
}
</script>

<template>
    <aside
        :class="[
            'bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-all duration-300 flex flex-col border-r border-gray-200 dark:border-gray-800',
            collapsed ? 'w-20' : 'w-64'
        ]"
    >
        <!-- Toggle Button -->
        <div class="flex justify-end p-2 border-b border-gray-200 dark:border-gray-800">
            <button
                @click="toggleSidebar"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <span v-if="!collapsed">«</span>
                <span v-else>»</span>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 mt-2">
            <ul class="space-y-1 px-2">
                <li>
                    <RouterLink
                        to="/topic"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        :class="collapsed ? 'justify-center' : 'space-x-2'"
                    >
                        <Lightbulb class="w-5 h-5" />
                        <span v-if="!collapsed">Topic</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink
                        to="/note"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        :class="collapsed ? 'justify-center' : 'space-x-2'"
                    >
                        <Notebook class="w-5 h-5" />
                        <span v-if="!collapsed">Note</span>
                    </RouterLink>
                </li>
                <li>
                    <RouterLink
                        to="/deadline"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        :class="collapsed ? 'justify-center' : 'space-x-2'"
                    >
                        <AlarmClock class="w-5 h-5" />
                        <span v-if="!collapsed">Deadline</span>
                    </RouterLink>
                </li>
            </ul>
        </nav>
    </aside>
</template>
