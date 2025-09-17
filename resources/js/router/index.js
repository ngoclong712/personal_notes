import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from "@/layouts/MainLayout.vue";
import BareLayout from "@/layouts/BareLayout.vue";
import Home from '@/views/Home.vue'
import Note from "@/views/Note.vue";
import Deadline from "@/views/Deadline.vue";
import Topic from "@/views/Topic.vue";
import AddTopic from "@/views/topics/AddTopic.vue";
import TopicDetail from "@/views/topics/TopicDetail.vue";
import EditTopic from "@/views/topics/EditTopic.vue";

const routes = [
    {
        path: '/',
        component: MainLayout,
        children: [
            {
                path: 'topic',
                name: 'topic',
                component: Topic,
            },
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
    },
    {
        path: '/',
        component: BareLayout,
        children: [
            {
                path: 'topic/add',
                name: 'topic.add',
                component: AddTopic,
            },
            {
                path: 'topic/:id',
                name: 'topic.detail',
                component: TopicDetail,
            },
            {
                path: 'topic/:id/edit',
                name: 'topic.edit',
                component: EditTopic,
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
