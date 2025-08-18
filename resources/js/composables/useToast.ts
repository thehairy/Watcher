import { ref, Component } from 'vue';
import Toast from '@/components/Toast.vue';

interface ToastOptions {
    type?: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message?: string;
    duration?: number;
}

interface ToastInstance extends ToastOptions {
    id: string;
    component?: Component;
}

const toasts = ref<ToastInstance[]>([]);

export function useToast() {
    const showToast = (options: ToastOptions) => {
        const id = Math.random().toString(36).substr(2, 9);
        const toast: ToastInstance = {
            id,
            ...options,
            component: Toast
        };
        
        toasts.value.push(toast);
        
        // Auto remove after duration + animation time
        const duration = options.duration || 4000;
        setTimeout(() => {
            removeToast(id);
        }, duration + 500);
        
        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex(toast => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (title: string, message?: string, duration?: number) => {
        return showToast({ type: 'success', title, message, duration });
    };

    const error = (title: string, message?: string, duration?: number) => {
        return showToast({ type: 'error', title, message, duration });
    };

    const warning = (title: string, message?: string, duration?: number) => {
        return showToast({ type: 'warning', title, message, duration });
    };

    const info = (title: string, message?: string, duration?: number) => {
        return showToast({ type: 'info', title, message, duration });
    };

    return {
        toasts,
        showToast,
        removeToast,
        success,
        error,
        warning,
        info
    };
}
