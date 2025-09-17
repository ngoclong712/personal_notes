import { ref } from 'vue'

const toasts = ref([])

function remove(id) {
    toasts.value = toasts.value.filter(t => t.id !== id)
}

function pushToast(message, type = 'info', duration = 4000) {
    const id = Date.now() + Math.random()
    toasts.value.push({ id, message, type })
    if (duration > 0) {
        setTimeout(() => remove(id), duration)
    }
}

export function useToast() {
    return {
        toasts,
        push: pushToast,
        remove,
        success: (msg, ms = 3000) => pushToast(msg, 'success', ms),
        error: (msg, ms = 5000) => pushToast(msg, 'error', ms),
        info: (msg, ms = 4000) => pushToast(msg, 'info', ms),
        warn: (msg, ms = 4000) => pushToast(msg, 'warn', ms),
    }
}

export const toast = {
    success: (msg, ms = 3000) => pushToast(msg, 'success', ms),
    error: (msg, ms = 5000) => pushToast(msg, 'error', ms),
    info: (msg, ms = 4000) => pushToast(msg, 'info', ms),
    warn: (msg, ms = 4000) => pushToast(msg, 'warn', ms),
}


