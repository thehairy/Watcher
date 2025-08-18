<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Play, Film, Tv, Star, Users, TrendingUp, Clock, Heart } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const isVisible = ref(false);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Welcome to Watcher">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    </Head>
    
    <!-- Background with liquid glass effect -->
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 relative overflow-hidden">
        <!-- Animated background blobs -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 -left-40 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute -bottom-40 right-1/3 w-72 h-72 bg-blue-600/25 rounded-full blur-3xl animate-pulse delay-2000"></div>
        </div>
        
        <!-- Navigation -->
        <nav class="relative z-10 w-full px-4 sm:px-6 py-4 sm:py-6">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="relative">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center backdrop-blur-xl shadow-lg border border-white/20">
                            <Film class="w-4 h-4 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl blur opacity-60"></div>
                    </div>
                    <span class="text-lg sm:text-2xl font-bold text-white">Watcher</span>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-2 sm:space-x-6">
                    <div v-if="$page.props.auth.user" class="flex items-center space-x-2 sm:space-x-4">
                        <Link
                            :href="route('dashboard')"
                            class="px-3 py-2 sm:px-6 sm:py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-white hover:bg-white/20 transition-all duration-300 hover:scale-105 text-sm sm:text-base"
                        >
                            Dashboard
                        </Link>
                    </div>
                    <div v-else class="flex items-center space-x-2 sm:space-x-4">
                        <Link
                            :href="route('login')"
                            class="px-3 py-2 sm:px-6 sm:py-2 text-white/90 hover:text-white transition-colors duration-300 text-sm sm:text-base"
                        >
                            Sign In
                        </Link>
                        <Link
                            :href="route('register')"
                            class="px-3 py-2 sm:px-6 sm:py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 hover:scale-105 shadow-lg backdrop-blur-md border border-white/20 text-sm sm:text-base"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section -->
        <main class="relative z-10 flex-1 flex items-center justify-center px-4 sm:px-6 py-12 sm:py-20">
            <div class="max-w-7xl mx-auto text-center">
                <div 
                    class="transition-all duration-1000 ease-out"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <!-- Hero Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-bold text-white mb-6 sm:mb-8 leading-tight">
                        <span class="bg-gradient-to-r from-white via-blue-100 to-cyan-200 bg-clip-text text-transparent">
                            Track Your
                        </span>
                        <br>
                        <span class="bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-300 bg-clip-text text-transparent">
                            Entertainment
                        </span>
                    </h1>
                    
                    <!-- Hero Subtitle -->
                    <p class="text-lg sm:text-xl md:text-2xl text-blue-100/90 mb-8 sm:mb-12 max-w-3xl mx-auto leading-relaxed px-4">
                        Discover, track, and organize your favorite movies and TV shows. 
                        Build your personal entertainment universe with Watcher.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 mb-12 sm:mb-16 px-4">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="group relative w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-base sm:text-lg font-semibold rounded-2xl hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 hover:scale-105 shadow-2xl backdrop-blur-md border border-white/20"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl blur opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>
                            <span class="relative flex items-center justify-center space-x-2">
                                <Play class="w-5 h-5" />
                                <span>Start Watching</span>
                            </span>
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="group relative w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-base sm:text-lg font-semibold rounded-2xl hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 hover:scale-105 shadow-2xl backdrop-blur-md border border-white/20"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl blur opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>
                            <span class="relative flex items-center justify-center space-x-2">
                                <Play class="w-5 h-5" />
                                <span>Go to Dashboard</span>
                            </span>
                        </Link>
                        
                        <button class="group w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 text-white text-base sm:text-lg font-semibold hover:bg-white/20 transition-all duration-300 hover:scale-105">
                            <span class="flex items-center justify-center space-x-2">
                                <Film class="w-5 h-5" />
                                <span>Learn More</span>
                            </span>
                        </button>
                    </div>
                </div>
                
                <!-- Feature Cards with Liquid Glass Design -->
                <div 
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 mt-16 sm:mt-20 px-4 transition-all duration-1000 ease-out delay-300"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <!-- Movie Tracking -->
                    <div class="group relative">
                        <!-- Liquid Glass Glow -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-400/40 via-cyan-400/30 to-blue-500/40 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                        
                        <!-- Glass Card -->
                        <div class="relative bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-2xl rounded-3xl border border-white/20 group-hover:border-blue-400/40 p-6 sm:p-8 transition-all duration-500 group-hover:translate-y-[-2px] group-hover:bg-gradient-to-br group-hover:from-white/20 group-hover:via-blue-50/10 group-hover:to-white/5">
                            <!-- Liquid Shimmer Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 via-transparent to-cyan-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl"></div>
                            
                            <!-- Header with Glass Design -->
                            <div class="relative z-10 flex items-start space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                                <!-- Glass Icon Container -->
                                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-400/20 via-cyan-400/15 to-blue-500/20 backdrop-blur-xl rounded-2xl border border-blue-300/30 flex items-center justify-center group-hover:border-blue-400/50 group-hover:bg-gradient-to-br group-hover:from-blue-400/30 group-hover:via-cyan-400/25 group-hover:to-blue-500/30 transition-all duration-500 group-hover:shadow-lg group-hover:shadow-blue-400/25">
                                    <Film class="w-6 h-6 sm:w-7 sm:h-7 text-blue-200 group-hover:text-blue-100 transition-colors duration-300" />
                                </div>
                                
                                <!-- Title & Number with Glass Styling -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                        <h3 class="text-lg sm:text-xl font-bold text-white/90 group-hover:text-white transition-colors duration-300">Movies</h3>
                                        <span class="text-xl sm:text-2xl font-bold text-blue-300/80 group-hover:text-blue-200 transition-colors duration-300">01</span>
                                    </div>
                                    <div class="h-0.5 bg-gradient-to-r from-blue-300/40 via-cyan-300/60 to-transparent rounded-full group-hover:from-blue-300/70 group-hover:via-cyan-300/80 transition-all duration-500"></div>
                                </div>
                            </div>
                            
                            <!-- Glass Content -->
                            <div class="relative z-10 space-y-4">
                                <p class="text-white/70 leading-relaxed group-hover:text-white/85 transition-colors duration-300 text-sm sm:text-base">
                                    Track, organize, and discover movies with intelligent recommendations and personal watchlists.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TV Show Tracking -->
                    <div class="group relative">
                        <!-- Liquid Glass Glow -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-400/40 via-blue-400/30 to-purple-500/40 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                        
                        <!-- Glass Card -->
                        <div class="relative bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-2xl rounded-3xl border border-white/20 group-hover:border-purple-400/40 p-6 sm:p-8 transition-all duration-500 group-hover:translate-y-[-2px] group-hover:bg-gradient-to-br group-hover:from-white/20 group-hover:via-purple-50/10 group-hover:to-white/5">
                            <!-- Liquid Shimmer Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-400/10 via-transparent to-blue-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl"></div>
                            
                            <!-- Header with Glass Design -->
                            <div class="relative z-10 flex items-start space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                                <!-- Glass Icon Container -->
                                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-400/20 via-blue-400/15 to-purple-500/20 backdrop-blur-xl rounded-2xl border border-purple-300/30 flex items-center justify-center group-hover:border-purple-400/50 group-hover:bg-gradient-to-br group-hover:from-purple-400/30 group-hover:via-blue-400/25 group-hover:to-purple-500/30 transition-all duration-500 group-hover:shadow-lg group-hover:shadow-purple-400/25">
                                    <Tv class="w-6 h-6 sm:w-7 sm:h-7 text-purple-200 group-hover:text-purple-100 transition-colors duration-300" />
                                </div>
                                
                                <!-- Title & Number with Glass Styling -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                        <h3 class="text-lg sm:text-xl font-bold text-white/90 group-hover:text-white transition-colors duration-300">TV Shows</h3>
                                        <span class="text-xl sm:text-2xl font-bold text-purple-300/80 group-hover:text-purple-200 transition-colors duration-300">02</span>
                                    </div>
                                    <div class="h-0.5 bg-gradient-to-r from-purple-300/40 via-blue-300/60 to-transparent rounded-full group-hover:from-purple-300/70 group-hover:via-blue-300/80 transition-all duration-500"></div>
                                </div>
                            </div>
                            
                            <!-- Glass Content -->
                            <div class="relative z-10 space-y-4">
                                <p class="text-white/70 leading-relaxed group-hover:text-white/85 transition-colors duration-300 text-sm sm:text-base">
                                    Follow series, track episodes, and never miss premieres with smart notifications.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ratings & Reviews -->
                    <div class="group relative">
                        <!-- Liquid Glass Glow -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400/40 via-blue-400/30 to-teal-500/40 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                        
                        <!-- Glass Card -->
                        <div class="relative bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-2xl rounded-3xl border border-white/20 group-hover:border-cyan-400/40 p-6 sm:p-8 transition-all duration-500 group-hover:translate-y-[-2px] group-hover:bg-gradient-to-br group-hover:from-white/20 group-hover:via-cyan-50/10 group-hover:to-white/5">
                            <!-- Liquid Shimmer Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-cyan-400/10 via-transparent to-teal-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl"></div>
                            
                            <!-- Header with Glass Design -->
                            <div class="relative z-10 flex items-start space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                                <!-- Glass Icon Container -->
                                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-cyan-400/20 via-blue-400/15 to-teal-500/20 backdrop-blur-xl rounded-2xl border border-cyan-300/30 flex items-center justify-center group-hover:border-cyan-400/50 group-hover:bg-gradient-to-br group-hover:from-cyan-400/30 group-hover:via-blue-400/25 group-hover:to-teal-500/30 transition-all duration-500 group-hover:shadow-lg group-hover:shadow-cyan-400/25">
                                    <Star class="w-6 h-6 sm:w-7 sm:h-7 text-cyan-200 group-hover:text-cyan-100 transition-colors duration-300" />
                                </div>
                                
                                <!-- Title & Number with Glass Styling -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                        <h3 class="text-lg sm:text-xl font-bold text-white/90 group-hover:text-white transition-colors duration-300">Reviews</h3>
                                        <span class="text-xl sm:text-2xl font-bold text-cyan-300/80 group-hover:text-cyan-200 transition-colors duration-300">03</span>
                                    </div>
                                    <div class="h-0.5 bg-gradient-to-r from-cyan-300/40 via-blue-300/60 to-transparent rounded-full group-hover:from-cyan-300/70 group-hover:via-blue-300/80 transition-all duration-500"></div>
                                </div>
                            </div>
                            
                            <!-- Glass Content -->
                            <div class="relative z-10 space-y-4">
                                <p class="text-white/70 leading-relaxed group-hover:text-white/85 transition-colors duration-300 text-sm sm:text-base">
                                    Rate content, write reviews, and discover hidden gems through community insights.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Features -->
                    <div class="group relative">
                        <!-- Liquid Glass Glow -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-teal-400/40 via-blue-400/30 to-emerald-500/40 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-all duration-700"></div>
                        
                        <!-- Glass Card -->
                        <div class="relative bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-2xl rounded-3xl border border-white/20 group-hover:border-teal-400/40 p-6 sm:p-8 transition-all duration-500 group-hover:translate-y-[-2px] group-hover:bg-gradient-to-br group-hover:from-white/20 group-hover:via-teal-50/10 group-hover:to-white/5">
                            <!-- Liquid Shimmer Effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-teal-400/10 via-transparent to-emerald-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-700 rounded-3xl"></div>
                            
                            <!-- Header with Glass Design -->
                            <div class="relative z-10 flex items-start space-x-3 sm:space-x-4 mb-4 sm:mb-6">
                                <!-- Glass Icon Container -->
                                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-teal-400/20 via-blue-400/15 to-emerald-500/20 backdrop-blur-xl rounded-2xl border border-teal-300/30 flex items-center justify-center group-hover:border-teal-400/50 group-hover:bg-gradient-to-br group-hover:from-teal-400/30 group-hover:via-blue-400/25 group-hover:to-emerald-500/30 transition-all duration-500 group-hover:shadow-lg group-hover:shadow-teal-400/25">
                                    <Users class="w-6 h-6 sm:w-7 sm:h-7 text-teal-200 group-hover:text-teal-100 transition-colors duration-300" />
                                </div>
                                
                                <!-- Title & Number with Glass Styling -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1 sm:mb-2">
                                        <h3 class="text-lg sm:text-xl font-bold text-white/90 group-hover:text-white transition-colors duration-300">Social</h3>
                                        <span class="text-xl sm:text-2xl font-bold text-teal-300/80 group-hover:text-teal-200 transition-colors duration-300">04</span>
                                    </div>
                                    <div class="h-0.5 bg-gradient-to-r from-teal-300/40 via-blue-300/60 to-transparent rounded-full group-hover:from-teal-300/70 group-hover:via-blue-300/80 transition-all duration-500"></div>
                                </div>
                            </div>
                            
                            <!-- Glass Content -->
                            <div class="relative z-10 space-y-4">
                                <p class="text-white/70 leading-relaxed group-hover:text-white/85 transition-colors duration-300 text-sm sm:text-base">
                                    Connect with friends, share recommendations, and build your entertainment community.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer Section -->
        <footer class="relative z-10 px-4 sm:px-6 py-8 sm:py-12">
            <div class="max-w-7xl mx-auto">
                <!-- Stats Section -->
                <div 
                    class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 mb-8 sm:mb-12 transition-all duration-1000 ease-out delay-500"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-2xl backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <TrendingUp class="w-6 h-6 sm:w-8 sm:h-8 text-blue-300" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-white mb-1 sm:mb-2">10K+</div>
                        <div class="text-blue-100/70 text-sm sm:text-base">Movies Tracked</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-400/20 to-blue-400/20 rounded-2xl backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <Clock class="w-6 h-6 sm:w-8 sm:h-8 text-purple-300" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-white mb-1 sm:mb-2">5K+</div>
                        <div class="text-blue-100/70 text-sm sm:text-base">TV Shows</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-400/20 to-emerald-400/20 rounded-2xl backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <Users class="w-6 h-6 sm:w-8 sm:h-8 text-green-300" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-white mb-1 sm:mb-2">1K+</div>
                        <div class="text-blue-100/70 text-sm sm:text-base">Active Users</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="relative">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-400/20 to-orange-400/20 rounded-2xl backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <Heart class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-300" />
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-white mb-1 sm:mb-2">50K+</div>
                        <div class="text-blue-100/70 text-sm sm:text-base">Reviews</div>
                    </div>
                </div>
                
                <!-- Footer Content -->
                <div class="text-center border-t border-white/10 pt-6 sm:pt-8">
                    <div class="flex items-center justify-center space-x-2 sm:space-x-3 mb-3 sm:mb-4">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-lg flex items-center justify-center">
                            <Film class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                        </div>
                        <span class="text-lg sm:text-xl font-bold text-white">Watcher</span>
                    </div>
                    <p class="text-blue-100/70 mb-4 sm:mb-6 text-sm sm:text-base">Your personal entertainment tracking companion</p>
                    <div class="text-blue-100/50 text-xs sm:text-sm">
                        © 2025 Watcher. Made with ❤️ for movie and TV lovers.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
