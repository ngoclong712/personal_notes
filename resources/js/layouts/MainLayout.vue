<template>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="h-16 bg-sky-300 text-slate-800 flex items-center px-4">
            <h1 class="text-xl font-bold">My Notes</h1>
        </header>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside class="w-64 bg-orange-100 text-slate-800 p-4">
                <ul class="space-y-2">
                    <li><a href="#" class="block hover:text-sky-600">Dashboard</a></li>
                    <li><a href="#" class="block hover:text-sky-600">Notes</a></li>
                    <li><a href="#" class="block hover:text-sky-600">Settings</a></li>
                </ul>
            </aside>

            <!-- Main content with table -->
            <main class="flex-1 bg-white text-slate-900 p-6 overflow-x-auto">
                <h2 class="text-lg font-semibold mb-4">Notes Table</h2>

                <table class="min-w-full border border-slate-300 rounded-lg overflow-hidden">
                    <thead class="bg-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-slate-700">#</th>
                        <th class="px-4 py-2 text-left text-slate-700">Title</th>
                        <th class="px-4 py-2 text-left text-slate-700">Category</th>
                        <th class="px-4 py-2 text-left text-slate-700">Created At</th>
                        <th class="px-4 py-2 text-left text-slate-700">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-sky-50">
                        <td class="px-4 py-2">1</td>
                        <td class="px-4 py-2">Learning Vue</td>
                        <td class="px-4 py-2">Study</td>
                        <td class="px-4 py-2">2025-09-15</td>
                        <td class="px-4 py-2">
                            <button class="text-sky-600 hover:underline mr-2">Edit</button>
                            <button class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-sky-50">
                        <td class="px-4 py-2">2</td>
                        <td class="px-4 py-2">Buy groceries</td>
                        <td class="px-4 py-2">Personal</td>
                        <td class="px-4 py-2">2025-09-14</td>
                        <td class="px-4 py-2">
                            <button class="text-sky-600 hover:underline mr-2">Edit</button>
                            <button class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <!-- Bạn có thể thêm nhiều row test ở đây -->
                    </tbody>
                </table>
            </main>
        </div>

        <!-- Footer -->
        <footer class="h-12 bg-sky-400 text-slate-800 flex items-center justify-center">
            © 2025 My Notes
        </footer>
    </div>
</template>



<script setup>
import Header from '@/components/layout/Header.vue'
import Sidebar from '@/components/layout/Sidebar.vue'
import Footer from '@/components/layout/Footer.vue'
</script>
