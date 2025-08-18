<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-50 space-y-2">
            <TransitionGroup
                name="toast"
                tag="div"
                class="space-y-2"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="max-w-sm w-full"
                >
                    <div
                        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 shadow-2xl"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center"
                                    :class="getIconBgClass(toast.type || 'info')"
                                >
                                    <component :is="getIconComponent(toast.type || 'info')" class="w-5 h-5 text-white" />
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-white">
                                    {{ toast.title }}
                                </p>
                                <p v-if="toast.message" class="text-xs text-blue-200 mt-1">
                                    {{ toast.message }}
                                </p>
                            </div>
                            <div class="ml-4">
                                <button
                                    @click="removeToast(toast.id)"
                                    class="inline-flex text-white/60 hover:text-white transition-colors"
                                >
                                    <X class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { CheckCircle, XCircle, AlertCircle, Info, X } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { toasts, removeToast } = useToast();

const getIconComponent = (type: string) => {
    switch (type) {
        case 'success': return CheckCircle;
        case 'error': return XCircle;
        case 'warning': return AlertCircle;
        default: return Info;
    }
};

const getIconBgClass = (type: string) => {
    switch (type) {
        case 'success': return 'bg-green-500';
        case 'error': return 'bg-red-500';
        case 'warning': return 'bg-yellow-500';
        default: return 'bg-blue-500';
    }
};
</script>

<style scoped>
.toast-enter-active {
    transition: all 0.3s ease-out;
}

.toast-leave-active {
    transition: all 0.3s ease-in;
}

.toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>
