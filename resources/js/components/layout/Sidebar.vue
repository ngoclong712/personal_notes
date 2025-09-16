<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
    <aside class="w-64 bg-slate-100 text-slate-700 dark:bg-gray-800 p-4 dark:text-gray-100">
        <nav>
            <ul class="space-y-2">
                <li>
                    <RouterLink to="/note" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                        Note
                    </RouterLink>
                </li>
                <li>
                    <RouterLink to="/deadline" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                        Deadline
                    </RouterLink>
                </li>
            </ul>
        </nav>
    </aside>
</template>
