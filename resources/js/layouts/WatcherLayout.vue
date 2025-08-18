<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Film, Calendar, Search, List, User, Settings, LogOut } from 'lucide-vue-next';
import { computed } from 'vue';
import ToastContainer from '@/components/ToastContainer.vue';
import SettingsDropdown from '@/components/SettingsDropdown.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 relative overflow-hidden">
        <!-- Animated background blobs -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 -left-40 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute -bottom-40 right-1/3 w-72 h-72 bg-blue-600/25 rounded-full blur-3xl animate-pulse delay-2000"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-col min-h-screen">
            <!-- Top Navigation -->
            <nav class="bg-white/10 backdrop-blur-md border-b border-white/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <Link :href="route('dashboard')" class="flex items-center gap-3 font-medium">
                            <div class="relative">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center backdrop-blur-xl shadow-lg border border-white/20">
                                    <Film class="w-6 h-6 text-white" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl blur opacity-60"></div>
                            </div>
                            <span class="text-xl font-bold text-white">Watcher</span>
                        </Link>

                        <!-- Main Navigation -->
                        <div class="hidden md:flex items-center gap-8">
                            <Link :href="route('watchlist')" 
                                  class="flex items-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200"
                                  :class="{ 'text-white bg-white/10': $page.component === 'Watchlist' }">
                                <List class="w-5 h-5" />
                                Watchlist
                            </Link>
                            <Link :href="route('calendar')" 
                                  class="flex items-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200"
                                  :class="{ 'text-white bg-white/10': $page.component === 'Calendar' }">
                                <Calendar class="w-5 h-5" />
                                Calendar
                            </Link>
                            <Link :href="route('discover')" 
                                  class="flex items-center gap-2 px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200"
                                  :class="{ 'text-white bg-white/10': $page.component === 'Discover' }">
                                <Search class="w-5 h-5" />
                                Discover
                            </Link>
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center gap-4">
                            <div class="hidden md:flex items-center gap-3">
                                <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" class="w-8 h-8 rounded-full border border-white/20">
                                <div v-else class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center border border-white/20">
                                    <User class="w-4 h-4 text-white" />
                                </div>
                                <span class="text-white/90 text-sm">{{ user?.name }}</span>
                            </div>
                            
                            <!-- Settings Dropdown -->
                            <SettingsDropdown />
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="flex-1 relative pb-20 md:pb-0">
                <Head :title="title" />
                <slot />
            </main>

            <!-- Bottom Navigation (Mobile) -->
            <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-black/50 backdrop-blur-md border-t border-white/20 z-50">
                <div class="flex items-center justify-around py-2 px-4">
                    <Link :href="route('watchlist')" 
                          class="flex flex-col items-center gap-1 py-2 px-3 transition-colors duration-200"
                          :class="$page.component === 'Watchlist' ? 'text-blue-400' : 'text-white/60'">
                        <List class="w-5 h-5" />
                        <span class="text-xs">Watchlist</span>
                    </Link>
                    <Link :href="route('calendar')" 
                          class="flex flex-col items-center gap-1 py-2 px-3 transition-colors duration-200"
                          :class="$page.component === 'Calendar' ? 'text-blue-400' : 'text-white/60'">
                        <Calendar class="w-5 h-5" />
                        <span class="text-xs">Calendar</span>
                    </Link>
                    <Link :href="route('discover')" 
                          class="flex flex-col items-center gap-1 py-2 px-3 transition-colors duration-200"
                          :class="$page.component === 'Discover' ? 'text-blue-400' : 'text-white/60'">
                        <Search class="w-5 h-5" />
                        <span class="text-xs">Discover</span>
                    </Link>
                    <Link :href="route('profile.edit')" 
                          class="flex flex-col items-center gap-1 py-2 px-3 text-white/60">
                        <User class="w-5 h-5" />
                        <span class="text-xs">Profile</span>
                    </Link>
                </div>
            </nav>
        </div>
        
        <!-- Toast Container -->
        <ToastContainer />
    </div>
</template>
