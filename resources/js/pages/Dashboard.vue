<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

// Redirect to watchlist as the main page
onMounted(() => {
    router.visit(route('watchlist'));
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <p class="text-white/70">Redirecting to Watchlist...</p>
        </div>
    </div>
</template>
