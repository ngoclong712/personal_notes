import axios from 'axios';
import { toast } from '@/lib/toast'
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        try {
            const status = error?.response?.status
            const payload = error?.response?.data
            const message = payload?.message || payload?.error || error?.message || 'Something went wrong'
            if (status === 422 && payload?.errors) {
                const first = Object.values(payload.errors)[0]
                const msg = Array.isArray(first) ? first[0] : String(first)
                toast.error(msg || message)
            } else {
                toast.error(message)
            }
        } catch (e) {
            toast.error('Unexpected error')
        }
        return Promise.reject(error)
    }
)
