<template>
    <WatcherLayout title="Calendar">
        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-white mb-2">Release Calendar</h1>
                    <p class="text-blue-200">Track upcoming releases and ended content from your watchlist</p>
                </div>

                <!-- Upcoming Releases Timeline -->
                <div v-if="sortedUpcoming.length > 0" class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <CalendarIcon class="w-6 h-6 mr-2 text-green-400" />
                        Upcoming Releases
                    </h2>

                    <!-- Mobile & Desktop Timeline -->
                    <div class="relative max-w-4xl">
                        <!-- Timeline Container -->
                        <div class="relative pl-16 sm:pl-16 md:pl-20">
                            <!-- Timeline Line -->
                            <div class="absolute left-4 sm:left-6 md:left-8 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-400 via-blue-400 to-indigo-500 opacity-70" 
                                 :style="{ 
                                     top: sortedUpcoming.length > 0 ? '56px' : '0', 
                                     bottom: sortedUpcoming.length > 0 ? '56px' : '0' 
                                 }"></div>
                            
                            <!-- Timeline Items -->
                            <div class="space-y-4 sm:space-y-6">
                                <div 
                                    v-for="(item, index) in sortedUpcoming" 
                                    :key="item.id"
                                    class="relative flex items-center gap-3 sm:gap-4"
                                >
                                    <!-- Date Circle Badge -->
                                    <div class="absolute -left-19 sm:-left-16 md:-left-20 top-1/2 transform -translate-y-1/2 z-10">
                                        <div 
                                            :class="[
                                                'w-14 h-14 sm:w-12 sm:h-12 md:w-16 md:h-16 rounded-full flex flex-col items-center justify-center text-white font-bold',
                                                getTimelineItemClass(item.days_until)
                                            ]"
                                        >
                                            <div class="text-center leading-none">
                                                <div class="text-xs sm:text-xs md:text-sm font-bold">{{ getDaysNumber(item.days_until) }}</div>
                                                <div class="text-xs sm:text-xs md:text-xs opacity-90">{{ getDaysUnit(item.days_until) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Content Card -->
                                    <div class="flex-1 min-w-0 max-w-2xl">
                                        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/15 transition-all duration-300 shadow-lg">
                                            <div class="flex h-32 sm:h-36">
                                                <!-- Full Height Thumbnail -->
                                                <div class="w-20 sm:w-24 md:w-28 flex-shrink-0">
                                                    <img
                                                        :src="getImageUrl(item.poster_path)"
                                                        :alt="item.title"
                                                        class="w-full h-full object-cover"
                                                        @error="handleImageError"
                                                    />
                                                </div>
                                                
                                                <!-- Content -->
                                                <div class="flex-1 p-4 flex flex-col justify-between min-w-0">
                                                    <div>
                                                        <h3 class="text-white font-semibold text-sm sm:text-base md:text-lg leading-tight mb-2">{{ item.title }}</h3>
                                                        <p v-if="item.episode_info" class="text-blue-200 text-xs sm:text-sm mb-2">
                                                            <span class="text-yellow-400 font-medium">S{{ String(item.episode_info.season).padStart(2, '0') }}E{{ String(item.episode_info.episode).padStart(2, '0') }}:</span> {{ item.episode_info.name }}
                                                        </p>
                                                        <p v-else class="text-blue-200 text-xs sm:text-sm mb-2">
                                                            <span class="text-yellow-400 font-medium">★</span> {{ item.type === 'movie' ? 'Movie' : 'TV Show' }}
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="mt-auto">
                                                        <div class="text-blue-300 text-xs mb-2">{{ formatDate(item.release_date) }}</div>
                                                        <div class="flex items-center gap-2 text-xs">
                                                            <div class="flex items-center text-yellow-400">
                                                                <Star class="w-3 h-3 mr-1" />
                                                                <span>{{ item.vote_average?.toFixed(1) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waiting For Release Date -->
                <div v-if="waitingForReleaseDate.length > 0" class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <Clock class="w-6 h-6 mr-2 text-purple-400" />
                        Waiting For Release Date
                    </h2>
                    <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        <div v-for="item in waitingForReleaseDate" :key="item.id"
                             class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300">
                            <div class="aspect-[2/3] relative">
                                <img
                                    :src="getImageUrl(item.poster_path)"
                                    :alt="item.title"
                                    class="w-full h-full object-cover"
                                    @error="handleImageError"
                                />
                                <div class="absolute top-2 right-2">
                                    <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                                        <Clock class="w-3 h-3 text-white" />
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <h4 class="text-white font-medium text-xs truncate">{{ item.title }}</h4>
                                <p class="text-purple-200 text-xs mt-1">{{ item.status }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ended Content -->
                <div v-if="endedContent.length > 0" class="mb-12">
                    <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                        <Play class="w-6 h-6 mr-2 text-gray-400" />
                        Ended
                    </h2>
                    <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        <div v-for="item in endedContent" :key="item.id"
                             class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl overflow-hidden hover:bg-white/20 transition-all duration-300">
                            <div class="aspect-[2/3] relative">
                                <img
                                    :src="getImageUrl(item.poster_path)"
                                    :alt="item.title"
                                    class="w-full h-full object-cover"
                                    @error="handleImageError"
                                />
                                <div class="absolute inset-0 bg-black/40"></div>
                                <div class="absolute top-2 right-2">
                                    <div class="w-6 h-6 bg-gray-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs">✓</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <h4 class="text-white font-medium text-xs truncate">{{ item.title }}</h4>
                                <p class="text-gray-400 text-xs mt-1">{{ item.status }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="sortedUpcoming.length === 0 && waitingForReleaseDate.length === 0 && endedContent.length === 0" 
                     class="text-center py-16">
                    <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <CalendarIcon class="w-12 h-12 text-white/60" />
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">No calendar content</h3>
                    <p class="text-white/60 mb-6">Add shows and movies to your watchlist to see their release information</p>
                </div>
            </div>
        </div>
    </WatcherLayout>
</template>

<script setup lang="ts">
import WatcherLayout from '@/layouts/WatcherLayout.vue';
import { Calendar as CalendarIcon, Clock, Star, Play } from 'lucide-vue-next';
import { computed } from 'vue';

interface ContentItem {
    id: number;
    title: string;
    poster_path: string;
    backdrop_path: string;
    type: 'tv' | 'movie';
    tmdb_id: number;
    overview: string;
    vote_average: number;
    status: string;
    genres: Array<{ id: number; name: string }>;
    release_date?: string;
    days_until?: number;
    episode_info?: {
        season: number;
        episode: number;
        name: string;
    };
    runtime?: number;
    networks?: Array<{ id: number; name: string }>;
    first_air_date?: string;
    last_air_date?: string;
}

interface UpcomingReleases {
    today: ContentItem[];
    this_week: ContentItem[];
    this_month: ContentItem[];
    later: ContentItem[];
}

const props = defineProps<{
    upcomingReleases: UpcomingReleases;
    waitingForReleaseDate: ContentItem[];
    endedContent: ContentItem[];
}>();

// Flatten and sort all upcoming releases by release date
const sortedUpcoming = computed(() => {
    const allUpcoming = [
        ...props.upcomingReleases.today,
        ...props.upcomingReleases.this_week,
        ...props.upcomingReleases.this_month,
        ...props.upcomingReleases.later
    ];
    
    // Sort by days until release (ascending)
    return allUpcoming.sort((a, b) => {
        const daysA = a.days_until ?? 999999;
        const daysB = b.days_until ?? 999999;
        return daysA - daysB;
    });
});

const getImageUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return 'https://image.tmdb.org/t/p/w500' + path;
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (target) {
        target.src = 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    }
};

const formatDate = (dateString: string | undefined) => {
    if (!dateString) return 'TBA';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        weekday: 'short', 
        month: 'short', 
        day: 'numeric' 
    });
};

const getDaysNumber = (days: number | undefined) => {
    if (days === undefined || days === null) return '?';
    if (days === 0) return 'Today';
    if (days === 1) return '1';
    if (days < 7) return days.toString();
    if (days < 30) return days.toString();
    
    const months = Math.floor(days / 30);
    return months.toString();
};

const getDaysUnit = (days: number | undefined) => {
    if (days === undefined || days === null) return '';
    if (days === 0) return '';
    if (days === 1) return 'day';
    if (days < 7) return 'days';
    if (days < 30) return 'days';
    
    const months = Math.floor(days / 30);
    return months === 1 ? 'month' : 'months';
};

const getTimelineItemClass = (days: number | undefined) => {
    if (days === undefined || days === null) return 'bg-slate-500';
    if (days === 0) return 'bg-red-500';
    if (days <= 3) return 'bg-orange-500';
    if (days <= 7) return 'bg-yellow-500';
    if (days <= 30) return 'bg-green-500';
    return 'bg-purple-500';
};

const getUrgencyBadgeClass = (days: number | undefined) => {
    if (days === undefined || days === null) return 'bg-blue-600/60 text-blue-300';
    if (days === 0) return 'bg-red-600/60 text-red-300';
    if (days <= 3) return 'bg-orange-600/60 text-orange-300';
    if (days <= 7) return 'bg-yellow-600/60 text-yellow-300';
    if (days <= 30) return 'bg-green-600/60 text-green-300';
    return 'bg-purple-600/60 text-purple-300';
};

const getUrgencyText = (days: number | undefined) => {
    if (days === undefined || days === null) return 'Unknown';
    if (days === 0) return 'Today!';
    if (days === 1) return 'Tomorrow';
    if (days <= 3) return 'Soon';
    if (days <= 7) return 'This week';
    if (days <= 30) return 'This month';
    return 'Later';
};
</script>

<style scoped>
/* Custom Timeline Styling */
.timeline-container {
    max-width: 100%;
    padding: 0;
}
</style>
