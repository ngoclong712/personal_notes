import './bootstrap';
import { createApp } from 'vue';

import ExampleComponent from "@/components/ExampleComponent.vue";

const app = createApp({})

app.component(ExampleComponent.name, ExampleComponent);

app.mount('#app')
