<script setup lang="ts">
import Multiselect from "vue-multiselect";
import {QuillEditor} from "@vueup/vue-quill";
import { onMounted, ref, reactive } from 'vue'
import { RouterLink, useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from '@/lib/toast'

const { success } = useToast()
const route = useRoute();
const router = useRouter();
const id = route.params.id as string

const topics = ref<any[]>([])

const form = reactive({
    title: '',
    status: 1,
    topic: null as any,
    content: '',
})

onMounted(async () => {
    try {
        const topicRes = await axios.get('/api/topics')
        topics.value = topicRes.data.data

        const res = await axios.get(`/api/notes/${route.params.id}`)
        const data = res.data.data ?? res.data

        form.title = data.title
        form.status = data.status
        form.content = data.content

        // map topic từ note về object trong topics
        if (data.topic && data.topic.id) {
            form.topic = topics.value.find(t => t.id === data.topic.id) || null
        }
    } catch (e) {
        console.error('Can not load data', e)
    }
})

async function submit() {
    const payload = {
        title: form.title,
        status: form.status,
        content: form.content,
        topic_id: form.topic?.id ?? null,
    }
    // console.log(payload)

    await axios.put(`/api/notes/${route.params.id}`, payload)
    success('Note updated')
    router.push({ name: 'note' })
}
</script>
<template>
    <div class="p-4">
        <form
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-4 grid grid-cols-2 gap-4"
            @submit.prevent="submit"
        >
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    v-model="form.title"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    type="text"
                    placeholder="Input title ..."
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800"
                >
                    <option :value="1">Draft</option>
                    <option :value="2">Done</option>
                </select>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                <Multiselect
                    v-model="form.topic"
                    :options="topics"
                    label="name"
                    track-by="id"
                    placeholder="-- Please Select Topic --"
                    class="w-full"
                />
            </div>

            <div class="col-span-2">
                <QuillEditor
                    v-model:content="form.content"
                    contentType="html"
                    theme="snow"
                    toolbar="full"
                    class="custom-editor"
                />
            </div>
            <div class="mt-15">
                <button class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:cursor-pointer">Edit</button>
            </div>
        </form>
    </div>
</template>
