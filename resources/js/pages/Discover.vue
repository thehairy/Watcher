<template>
    <WatcherLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 p-6">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-white mb-2">Discover</h1>
                    <p class="text-blue-200">Find your next favorite show or movie</p>
                </div>

                <!-- Search -->
                <div class="mb-12">
                    <div class="relative max-w-2xl">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-5 w-5 text-blue-300" />
                        </div>
                        <input
                            v-model="searchQuery"
                            @input="searchContent"
                            type="text"
                            placeholder="Search movies and TV shows..."
                            class="block w-full pl-10 pr-3 py-4 border border-white/20 rounded-2xl bg-white/10 backdrop-blur-md text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Search Results -->
                    <div v-if="searchResults.length > 0" class="mt-8">
                        <h2 class="text-2xl font-semibold text-white mb-6">Search Results</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                            <div
                                v-for="item in searchResults"
                                :key="item.id"
                                class="relative group cursor-pointer"
                            >
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300 hover:scale-105">
                                    <div class="relative">
                                        <img
                                            :src="getImageUrl(item.poster_path)"
                                            :alt="getTitle(item)"
                                            class="w-full h-48 object-cover"
                                            @error="handleImageError"
                                        />
                                        <button
                                            @click="addToWatchlist(item)"
                                            class="absolute top-2 right-2 bg-blue-600 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-700"
                                        >
                                            <Plus class="w-4 h-4 text-white" />
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="font-medium text-white text-sm truncate mb-1">{{ getTitle(item) }}</h3>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-white/60">{{ getReleaseYear(item) }}</span>
                                            <div class="flex items-center">
                                                <Star class="w-3 h-3 text-yellow-400 mr-1" />
                                                <span class="text-white/80">{{ item.vote_average?.toFixed(1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trending -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <TrendingUp class="w-6 h-6 mr-2 text-pink-400" />
                        Trending This Week
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        <div
                            v-for="item in displayedTrending"
                            :key="item.id"
                            class="relative group cursor-pointer"
                        >
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300 hover:scale-105">
                                <div class="relative">
                                    <img
                                        :src="getImageUrl(item.poster_path)"
                                        :alt="getTitle(item)"
                                        class="w-full h-48 object-cover"
                                        @error="handleImageError"
                                    />
                                    <button
                                        @click="addToWatchlist(item)"
                                        class="absolute top-2 right-2 bg-blue-600 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-700"
                                    >
                                        <Plus class="w-4 h-4 text-white" />
                                    </button>
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-white text-sm truncate mb-1">{{ getTitle(item) }}</h3>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white/60">{{ getReleaseYear(item) }}</span>
                                        <div class="flex items-center">
                                            <Star class="w-3 h-3 text-yellow-400 mr-1" />
                                            <span class="text-white/80">{{ item.vote_average?.toFixed(1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Movies -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <Play class="w-6 h-6 mr-2 text-green-400" />
                        Popular Movies
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        <div
                            v-for="item in displayedPopularMovies"
                            :key="item.id"
                            class="relative group cursor-pointer"
                        >
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300 hover:scale-105">
                                <div class="relative">
                                    <img
                                        :src="getImageUrl(item.poster_path)"
                                        :alt="getTitle(item)"
                                        class="w-full h-48 object-cover"
                                        @error="handleImageError"
                                    />
                                    <button
                                        @click="addToWatchlist(item)"
                                        class="absolute top-2 right-2 bg-blue-600 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-700"
                                    >
                                        <Plus class="w-4 h-4 text-white" />
                                    </button>
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-white text-sm truncate mb-1">{{ getTitle(item) }}</h3>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white/60">{{ getReleaseYear(item) }}</span>
                                        <div class="flex items-center">
                                            <Star class="w-3 h-3 text-yellow-400 mr-1" />
                                            <span class="text-white/80">{{ item.vote_average?.toFixed(1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular TV Shows -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <Calendar class="w-6 h-6 mr-2 text-purple-400" />
                        Popular TV Shows
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        <div
                            v-for="item in displayedPopularTvShows"
                            :key="item.id"
                            class="relative group cursor-pointer"
                        >
                            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300 hover:scale-105">
                                <div class="relative">
                                    <img
                                        :src="getImageUrl(item.poster_path)"
                                        :alt="getTitle(item)"
                                        class="w-full h-48 object-cover"
                                        @error="handleImageError"
                                    />
                                    <button
                                        @click="addToWatchlist(item)"
                                        class="absolute top-2 right-2 bg-blue-600 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-700"
                                    >
                                        <Plus class="w-4 h-4 text-white" />
                                    </button>
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-white text-sm truncate mb-1">{{ getTitle(item) }}</h3>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-white/60">{{ getReleaseYear(item) }}</span>
                                        <div class="flex items-center">
                                            <Star class="w-3 h-3 text-yellow-400 mr-1" />
                                            <span class="text-white/80">{{ item.vote_average?.toFixed(1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WatcherLayout>
</template>

<script setup lang="ts">
import WatcherLayout from '@/layouts/WatcherLayout.vue';
import { Search, TrendingUp, Star, Plus, Calendar, Play } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

interface TMDBContent {
    id: number;
    title?: string;
    name?: string;
    overview: string;
    poster_path: string;
    backdrop_path: string;
    vote_average: number;
    release_date?: string;
    first_air_date?: string;
    media_type?: 'movie' | 'tv';
    genre_ids: number[];
}

interface Genre {
    id: number;
    name: string;
}

const props = defineProps<{
    trending: TMDBContent[];
    popular_movies: TMDBContent[];
    popular_tv_shows: TMDBContent[];
    genres: Genre[];
}>();

const searchQuery = ref('');
const searchResults = ref<TMDBContent[]>([]);
const isSearching = ref(false);
const { success, error } = useToast();

const getImageUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return 'https://image.tmdb.org/t/p/w500' + path;
};

const getTitle = (item: TMDBContent) => {
    return item.title || item.name || 'Unknown Title';
};

const getReleaseYear = (item: TMDBContent) => {
    const date = item.release_date || item.first_air_date;
    return date ? new Date(date).getFullYear() : '';
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (target) {
        target.src = 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    }
};

const searchContent = async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    isSearching.value = true;
    try {
        const response = await fetch(`/discover/search?query=${encodeURIComponent(searchQuery.value)}`);
        const data = await response.json();
        searchResults.value = data.results || [];
    } catch (error) {
        console.error('Search failed:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

const addToWatchlist = async (item: TMDBContent, status: string = 'plan_to_watch') => {
    try {
        const type = item.media_type || (item.title ? 'movie' : 'tv');
        
        const response = await fetch('/watchlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                tmdb_id: item.id,
                type: type,
                status: status
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 409) {
                error('Already in Watchlist', `${getTitle(item)} is already in your watchlist.`);
            } else {
                error('Failed to Add', errorData.message || 'Something went wrong. Please try again.');
            }
            return;
        }

        const data = await response.json();
        success('Added to Watchlist!', `${getTitle(item)} has been added to your watchlist.`);
    } catch (err: any) {
        console.error('Failed to add to watchlist:', err);
        error('Failed to Add', 'Something went wrong. Please try again.');
    }
};

const displayedTrending = computed(() => props.trending.slice(0, 12));
const displayedPopularMovies = computed(() => props.popular_movies.slice(0, 8));
const displayedPopularTvShows = computed(() => props.popular_tv_shows.slice(0, 8));
</script>
