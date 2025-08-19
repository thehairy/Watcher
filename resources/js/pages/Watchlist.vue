<template>
    <WatcherLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-white mb-2">My Watchlist</h1>
                    <p class="text-blue-200">Track your favorite shows and movies</p>
                </div>

                <!-- Unified Watchlist -->
                <div v-if="sortedWatchlist.length > 0" class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div
                            v-for="item in sortedWatchlist"
                            :key="item.id"
                            class="relative group"
                        >
                            <div 
                                class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl overflow-hidden hover:bg-white/20 transition-all duration-300 hover:scale-105 cursor-pointer"
                                @click="openMediaModal(item)"
                            >
                                <div class="relative">
                                    <img
                                        :src="getImageUrl(item.content?.poster_path)"
                                        :alt="item.content?.title"
                                        class="w-full aspect-[2/3] object-cover"
                                        @error="handleImageError"
                                    />
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-2 left-2">
                                        <div :class="getStatusBadgeStyle(item.status)" class="px-2 py-1 rounded-2xl text-xs font-semibold">
                                            {{ getStatusText(item.status) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Actions Dropdown -->
                                    <div class="absolute top-2 right-2">
                                        <div class="dropdown">
                                            <button class="bg-black/50 p-2 rounded-full hover:bg-black/70 transition-colors cursor-pointer" @click.stop>
                                                <MoreVertical class="w-4 h-4 text-white" />
                                            </button>
                                            <div class="dropdown-content" @click.stop>
                                                <button v-if="item.status === 'plan_to_watch' || item.status === 'completed'" @click="updateStatus(item.id, 'watching')" class="dropdown-item">
                                                    {{ item.status === 'completed' ? 'Rewatch' : 'Start Watching' }}
                                                </button>
                                                <button v-if="item.status === 'watching'" @click="updateStatus(item.id, 'completed')" class="dropdown-item">
                                                    Mark Complete
                                                </button>
                                                <button v-if="item.status === 'watching'" @click="updateStatus(item.id, 'on_hold')" class="dropdown-item">
                                                    Put on Hold
                                                </button>
                                                <button v-if="item.status === 'on_hold'" @click="updateStatus(item.id, 'watching')" class="dropdown-item">
                                                    Resume Watching
                                                </button>
                                                <button v-if="item.status === 'on_hold'" @click="updateStatus(item.id, 'dropped')" class="dropdown-item">
                                                    Drop
                                                </button>
                                                <button @click="removeFromWatchlist(item.id)" class="dropdown-item text-red-400">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar for Currently Watching TV Shows Only -->
                                    <div v-if="item.status === 'watching' && item.type === 'tv'" class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/90 to-transparent">
                                        <div class="flex items-center justify-between text-white mb-2">
                                            <span class="text-sm font-medium">{{ item.progress }}% Complete</span>
                                            <Star class="w-4 h-4 text-yellow-400" />
                                        </div>
                                        <div class="w-full bg-white/20 rounded-full h-2">
                                            <div
                                                class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                                :style="{ width: item.progress + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="text-white font-semibold text-lg mb-2 truncate">
                                        {{ item.content?.title }}
                                    </h3>
                                    
                                    <div class="flex items-center justify-between text-sm text-blue-200 mb-2">
                                        <div class="flex items-center">
                                            <Star class="w-4 h-4 mr-1 text-yellow-400" />
                                            <span>{{ item.content?.vote_average?.toFixed(1) || 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <Clock class="w-4 h-4 mr-1" />
                                            <span v-if="item.type === 'tv' && getEpisodeProgress(item)" class="whitespace-nowrap">
                                                {{ getEpisodeProgress(item) }}
                                            </span>
                                            <span v-else-if="item.type === 'movie' && item.content?.runtime" class="whitespace-nowrap">
                                                {{ item.content.runtime }}m
                                            </span>
                                            <span v-else class="whitespace-nowrap text-gray-400">
                                                No data
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status and completion info -->
                                    <div v-if="item.status === 'completed'" class="text-green-400 text-sm mb-2">
                                        Completed {{ formatDate(item.updated_at) }}
                                    </div>
                                    
                                    <p class="text-gray-300 text-sm mt-2 line-clamp-2">
                                        {{ item.content?.overview }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="props.watchlist.length === 0" class="text-center py-20">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-12 max-w-md mx-auto">
                        <Plus class="w-16 h-16 text-blue-400 mx-auto mb-4" />
                        <h3 class="text-2xl font-semibold text-white mb-2">Your watchlist is empty</h3>
                        <p class="text-blue-200 mb-6">Start building your watchlist by discovering new content</p>
                        <a
                            href="/discover"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <Plus class="w-5 h-5 mr-2" />
                            Discover Content
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Modal -->
        <MediaModal 
            :is-visible="isModalVisible"
            :item-id="selectedItemId"
            :type="selectedItemType"
            @close="closeModal"
            @episodes-updated="refreshWatchlist"
        />
    </WatcherLayout>
</template>

<script setup lang="ts">
import WatcherLayout from '@/layouts/WatcherLayout.vue';
import MediaModal from '@/components/MediaModal.vue';
import { Play, Star, Clock, MoreVertical, Plus, Trash2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

interface WatchlistItem {
    id: number;
    status: 'watching' | 'completed' | 'plan_to_watch' | 'on_hold' | 'dropped';
    progress: number;
    rating?: number;
    updated_at: string;
    type: 'tv' | 'movie';
    episodes_watched: number;
    total_episodes: number;
    content: {
        id: number;
        title: string;
        overview: string;
        poster_path: string;
        backdrop_path: string;
        vote_average: number;
        first_air_date?: string;
        release_date?: string;
        number_of_seasons?: number;
        number_of_episodes?: number;
        runtime?: number;
        status?: string;
        genres: Array<{ id: number; name: string }>;
    } | null;
}

const props = defineProps<{
    watchlist: WatchlistItem[];
}>();

const { success, error } = useToast();
const page = usePage();
const user = computed(() => page.props.auth?.user);
const tmdbImageBaseUrl = 'https://image.tmdb.org/t/p/w500';

// Modal state
const isModalVisible = ref(false);
const selectedItemId = ref<number | undefined>(undefined);
const selectedItemType = ref<'tv' | 'movie'>('tv');

// Define status order for sorting
const statusOrder = {
    'watching': 1,
    'plan_to_watch': 2,
    'on_hold': 3,
    'completed': 4,
    'dropped': 5
};

const sortedWatchlist = computed(() => 
    props.watchlist.sort((a, b) => {
        const statusOrderA = statusOrder[a.status] || 999;
        const statusOrderB = statusOrder[b.status] || 999;
        
        if (statusOrderA !== statusOrderB) {
            return statusOrderA - statusOrderB;
        }
        
        // If same status, sort by updated_at (most recent first)
        return new Date(b.updated_at).getTime() - new Date(a.updated_at).getTime();
    })
);

const watchingItems = computed(() => 
    props.watchlist.filter(item => item.status === 'watching')
);

const completedItems = computed(() => 
    props.watchlist.filter(item => item.status === 'completed')
);

const planToWatchItems = computed(() => 
    props.watchlist.filter(item => item.status === 'plan_to_watch')
);

const onHoldItems = computed(() => 
    props.watchlist.filter(item => item.status === 'on_hold')
);

const getImageUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return tmdbImageBaseUrl + path;
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (target) {
        target.src = 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    }
};

const updateStatus = async (itemId: number, newStatus: string) => {
    try {
        const response = await fetch(`/watchlist/${itemId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: newStatus
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            error('Failed to Update', errorData.message || 'Something went wrong. Please try again.');
            return;
        }

        const data = await response.json();
        success('Status Updated!', `Status has been updated to ${getStatusText(newStatus)}.`);
        
        // Refresh the page to show updated data
        router.reload({ only: ['watchlist'] });
    } catch (err: any) {
        console.error('Failed to update status:', err);
        error('Failed to Update', 'Something went wrong. Please try again.');
    }
};

const updateProgress = async (itemId: number, newProgress: number) => {
    try {
        const response = await fetch(`/watchlist/${itemId}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                progress: newProgress
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            error('Failed to Update', errorData.message || 'Something went wrong. Please try again.');
            return;
        }

        const data = await response.json();
        success('Progress Updated!', `Progress updated to ${newProgress}%.`);
        
        // Refresh the page to show updated data
        router.reload({ only: ['watchlist'] });
    } catch (err: any) {
        console.error('Failed to update progress:', err);
        error('Failed to Update', 'Something went wrong. Please try again.');
    }
};

const removeFromWatchlist = async (itemId: number) => {
    if (confirm('Are you sure you want to remove this from your watchlist?')) {
        try {
            const response = await fetch(`/watchlist/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                error('Failed to Remove', errorData.message || 'Something went wrong. Please try again.');
                return;
            }

            const data = await response.json();
            success('Removed!', 'Item has been removed from your watchlist.');
            
            // Refresh the page to show updated data
            router.reload({ only: ['watchlist'] });
        } catch (err: any) {
            console.error('Failed to remove from watchlist:', err);
            error('Failed to Remove', 'Something went wrong. Please try again.');
        }
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'watching': return 'text-blue-400';
        case 'completed': return 'text-green-400';
        case 'plan_to_watch': return 'text-yellow-400';
        case 'on_hold': return 'text-orange-400';
        case 'dropped': return 'text-red-400';
        default: return 'text-gray-400';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'watching': return 'Watching';
        case 'completed': return 'Completed';
        case 'plan_to_watch': return 'Plan to Watch';
        case 'on_hold': return 'On Hold';
        case 'dropped': return 'Dropped';
        default: return status;
    }
};

const getEpisodeProgress = (item: WatchlistItem) => {
    if (item.type === 'tv') {
        if (item.total_episodes && item.total_episodes > 0) {
            const watched = item.episodes_watched || 0;
            return `${watched}/${item.total_episodes} episodes`;
        } else if (item.content?.number_of_episodes && item.content.number_of_episodes > 0) {
            const watched = item.episodes_watched || 0;
            return `${watched}/${item.content.number_of_episodes} episodes`;
        }
    }
    return null;
};

const getStatusBadgeStyle = (status: string) => {
    switch (status) {
        case 'watching': return 'bg-blue-500 text-white';
        case 'completed': return 'bg-green-500 text-white';
        case 'plan_to_watch': return 'bg-yellow-500 text-white';
        case 'on_hold': return 'bg-orange-500 text-white';
        case 'dropped': return 'bg-red-500 text-white';
        default: return 'bg-gray-500 text-white';
    }
};

// Modal methods
const openMediaModal = (item: WatchlistItem) => {
    if (item.content) {
        selectedItemId.value = item.content.id;
        selectedItemType.value = item.type;
        isModalVisible.value = true;
    }
};

const closeModal = () => {
    isModalVisible.value = false;
    selectedItemId.value = undefined;
};

const refreshWatchlist = () => {
    // Refresh the watchlist data to update episode counts
    router.reload({ only: ['watchlist'] });
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: rgba(0, 0, 0, 0.9);
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 8px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-item {
    color: white;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
    border: none;
    background: none;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>
