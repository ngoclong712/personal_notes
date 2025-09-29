<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink, useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();

type Topic = {
    id: number,
    name: string,
}

type Attachment = {
    id: number,
    note_id: number,
    file_path: string,
    file_type: string,
    file_name: string,
}
type Note = {
    id: number,
    title: string,
    content: string,
    topic_id: number | null,
    topic?: Topic,  // 👈 thêm field này để nhận object topic từ API
    status: number,
    attachments?: Attachment[],
}

const note = ref<Note>()

function reformat(int) {
    if(int === 1) {
        return "Draft";
    }
    else return "Done";
}

onMounted(async () => {
    try {
        const id = route.params.id;
        const res = await axios.get(`/api/notes/${id}`);
        console.log(res.data.data);
        note.value = res.data.data;
    }
    catch (e) {
        console.error('Can not get notes detail')
    }
})
</script>

<template>
    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Note Detail</h1>
            <div>
                <RouterLink :to="{ name: 'note.edit'}" class="px-3 py-2 rounded-md bg-green-600 text-white mr-2">Edit</RouterLink>
                <RouterLink :to="{ name: 'note' }" class="px-3 py-2 rounded-md bg-gray-200">Back</RouterLink>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div v-if="!note" class="text-gray-500">Note not found.</div>

            <div v-else class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-medium">ID:</span> {{ note.id }}
                </div>
                <div>
                    <span class="font-medium">Topic:</span> {{ note.topic?.name }}
                </div>
                <div>
                    <span class="font-medium">Title:</span> {{ note.title }}
                </div>
                <div>
                    <span class="font-medium">Status:</span> {{ reformat(note.status) }}
                </div>
                <div class="col-span-2">
                    <span class="font-medium">Content:</span>
                    <div class="prose max-w-none dark:prose-invert mt-2" v-html="note.content"></div>
                </div>
                <div class="col-span-2 mt-4">
                    <span class="font-medium">Attachments:</span>
                    <div v-if="note.attachments?.length" class="mt-2 space-y-2">
                        <div
                            v-for="file in note.attachments"
                            :key="file.id"
                            class="flex items-center justify-between px-3 py-2 border rounded-md bg-gray-50"
                        >
                            <div class="flex items-center space-x-2">
                                <a
                                    :href="file.file_path"
                                    target="_blank"
                                    class="text-blue-600 underline hover:text-blue-800"
                                >
                                    {{ file.file_name ?? file.file_path.split('/').pop() }}
                                </a>
                                <span class="text-xs text-gray-500">({{ file.file_type.toUpperCase() }})</span>
                            </div>
                            <a
                                :href="file.file_path"
                                download
                                class="text-sm text-green-600 hover:underline"
                            >
                                Download
                            </a>
                        </div>
                    </div>
                    <div v-else class="text-gray-500">No attachments</div>
                </div>
            </div>
        </div>
    </div>
</template>
