<template>
    <Teleport to="body">
        <div
            v-if="visible"
            class="fixed top-4 right-4 z-50 max-w-sm w-full"
        >
            <div
                class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 shadow-2xl transition-all duration-300 transform"
                :class="{
                    'translate-x-0 opacity-100': visible,
                    'translate-x-full opacity-0': !visible
                }"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center"
                            :class="iconBgClass"
                        >
                            <component :is="iconComponent" class="w-5 h-5 text-white" />
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-white">
                            {{ title }}
                        </p>
                        <p v-if="message" class="text-xs text-blue-200 mt-1">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4">
                        <button
                            @click="close"
                            class="inline-flex text-white/60 hover:text-white transition-colors"
                        >
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { CheckCircle, XCircle, AlertCircle, Info, X } from 'lucide-vue-next';

interface ToastProps {
    type?: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message?: string;
    duration?: number;
    visible?: boolean;
}

const props = withDefaults(defineProps<ToastProps>(), {
    type: 'info',
    duration: 4000,
    visible: false
});

const emit = defineEmits<{
    close: [];
}>();

const visible = ref(props.visible);
let timeout: number | null = null;

const iconComponent = computed(() => {
    switch (props.type) {
        case 'success': return CheckCircle;
        case 'error': return XCircle;
        case 'warning': return AlertCircle;
        default: return Info;
    }
});

const iconBgClass = computed(() => {
    switch (props.type) {
        case 'success': return 'bg-green-500';
        case 'error': return 'bg-red-500';
        case 'warning': return 'bg-yellow-500';
        default: return 'bg-blue-500';
    }
});

const close = () => {
    visible.value = false;
    setTimeout(() => {
        emit('close');
    }, 300);
};

onMounted(() => {
    visible.value = true;
    
    if (props.duration > 0) {
        timeout = setTimeout(() => {
            close();
        }, props.duration);
    }
});

onUnmounted(() => {
    if (timeout) {
        clearTimeout(timeout);
    }
});
</script>
