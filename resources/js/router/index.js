import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from "@/layouts/MainLayout.vue";
import Home from '@/views/Home.vue'
import Note from "@/views/Note.vue";
import Deadline from "@/views/Deadline.vue";

const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: '',
                name: 'home',
                component: Home
            },
            {
                path: 'note',
                name: 'note',
                component: Note
            },
            {
                path: 'deadline',
                name: 'deadline',
                component: Deadline
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
