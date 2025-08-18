<template>
    <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="closeModal">
        <div class="relative w-full max-w-4xl max-h-[90vh] bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
            <!-- Close Button -->
            <button 
                @click="closeModal"
                class="absolute top-4 right-4 z-10 p-2 rounded-full bg-black/50 hover:bg-black/70 transition-colors"
            >
                <X class="w-6 h-6 text-white" />
            </button>

            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center h-96">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>

            <!-- Content -->
            <div v-else-if="mediaData" class="overflow-y-auto max-h-[90vh]">
                <!-- Header Section -->
                <div class="relative">
                    <div 
                        class="h-64 bg-cover bg-center bg-no-repeat"
                        :style="{ backgroundImage: `url(${getBackdropUrl(mediaData.backdrop_path)})` }"
                    >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-transparent"></div>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="flex items-end space-x-6">
                            <img 
                                :src="getPosterUrl(mediaData.poster_path)"
                                :alt="mediaData.name || mediaData.title"
                                class="w-32 h-48 object-cover rounded-lg shadow-lg"
                            />
                            <div class="flex-1 text-white">
                                <h1 class="text-3xl font-bold mb-2">{{ mediaData.name || mediaData.title }}</h1>
                                <div class="flex items-center space-x-4 text-sm">
                                    <div class="flex items-center">
                                        <Star class="w-4 h-4 mr-1 text-yellow-400" />
                                        <span>{{ mediaData.vote_average?.toFixed(1) }}</span>
                                    </div>
                                    <span v-if="mediaData.first_air_date || mediaData.release_date">
                                        {{ formatYear(mediaData.first_air_date || mediaData.release_date) }}
                                    </span>
                                    <span v-if="mediaData.number_of_seasons" class="bg-blue-600 px-2 py-1 rounded">
                                        {{ mediaData.number_of_seasons }} Season{{ mediaData.number_of_seasons > 1 ? 's' : '' }}
                                    </span>
                                    <span v-if="mediaData.runtime" class="bg-green-600 px-2 py-1 rounded">
                                        {{ mediaData.runtime }}m
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-6">
                    <!-- Overview -->
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-white mb-3">Overview</h2>
                        <p class="text-gray-300 leading-relaxed">{{ mediaData.overview }}</p>
                    </div>

                    <!-- Seasons (TV Shows only) -->
                    <div v-if="type === 'tv' && mediaData.seasons && !selectedSeason" class="mb-6">
                        <h2 class="text-xl font-semibold text-white mb-4">Seasons</h2>
                        <div class="space-y-3">
                            <div 
                                v-for="season in mediaData.seasons.filter(s => s.season_number > 0)"
                                :key="season.id"
                                class="flex items-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-colors cursor-pointer"
                                @click="selectSeason(season)"
                            >
                                <img 
                                    :src="getPosterUrl(season.poster_path)"
                                    :alt="season.name"
                                    class="w-16 h-24 object-cover rounded mr-4"
                                />
                                <div class="flex-1">
                                    <h3 class="text-white font-medium">{{ season.name }}</h3>
                                    <p class="text-gray-400 text-sm">{{ season.episode_count }} episodes</p>
                                    <p class="text-gray-500 text-sm">{{ formatYear(season.air_date) }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <label class="flex items-center cursor-pointer" @click.stop>
                                        <input 
                                            type="checkbox"
                                            :checked="isSeasonComplete(season.season_number)"
                                            @change="(e) => markSeasonComplete(season.season_number, (e.target as HTMLInputElement).checked)"
                                            class="sr-only"
                                        />
                                        <div class="relative">
                                            <div :class="[
                                                'w-5 h-5 rounded-full border-2 transition-all duration-200',
                                                isSeasonComplete(season.season_number) 
                                                    ? 'bg-green-500 border-green-500' 
                                                    : 'border-gray-400 hover:border-green-400'
                                            ]"></div>
                                            <div v-if="isSeasonComplete(season.season_number)" 
                                                 class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                    <ChevronRight class="w-5 h-5 text-gray-400" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Episodes (when season is selected) -->
                    <div v-if="selectedSeason && episodes.length > 0 && !selectedEpisode" class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-white">
                                {{ selectedSeason.name }} Episodes
                            </h2>
                            <button 
                                @click="selectedSeason = null; episodes = []"
                                class="text-blue-400 hover:text-blue-300 text-sm"
                            >
                                ← Back to Seasons
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div 
                                v-for="episode in episodes"
                                :key="episode.id"
                                class="flex items-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-colors cursor-pointer"
                                @click="openEpisodeDetails(episode)"
                            >
                                <div class="flex-1 min-w-0 mr-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-white font-medium">
                                                {{ episode.episode_number }}. {{ episode.name }}
                                            </h4>
                                            <p class="text-gray-400 text-sm mb-1">{{ formatDate(episode.air_date) }}</p>
                                            <p class="text-gray-300 text-sm line-clamp-2">{{ episode.overview }}</p>
                                        </div>
                                    </div>
                                </div>
                                <label class="flex items-center cursor-pointer" @click.stop>
                                    <input 
                                        type="checkbox"
                                        :checked="isEpisodeWatched(episode)"
                                        @change="(e) => toggleEpisodeWatched(episode, (e.target as HTMLInputElement).checked)"
                                        class="sr-only"
                                    />
                                    <div class="relative">
                                        <div :class="[
                                            'w-5 h-5 rounded-full border-2 transition-all duration-200',
                                            isEpisodeWatched(episode) 
                                                ? 'bg-green-500 border-green-500' 
                                                : 'border-gray-400 hover:border-green-400'
                                        ]"></div>
                                        <div v-if="isEpisodeWatched(episode)" 
                                             class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Episode Details (when episode is selected) -->
                    <div v-if="selectedEpisode" class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-white">Episode Details</h2>
                            <button 
                                @click="selectedEpisode = null"
                                class="text-blue-400 hover:text-blue-300 text-sm"
                            >
                                ← Back to Episodes
                            </button>
                        </div>

                        <div v-if="episodeDetailsLoading" class="flex justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        </div>

                        <div v-else class="space-y-6">
                            <!-- Episode Header -->
                            <div class="flex flex-col md:flex-row gap-4">
                                <img 
                                    :src="getStillUrl(selectedEpisode.still_path)"
                                    :alt="selectedEpisode.name"
                                    class="w-full md:w-80 h-48 object-cover rounded-lg"
                                />
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-white mb-2">
                                        {{ selectedEpisode.episode_number }}. {{ selectedEpisode.name }}
                                    </h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-400 mb-4">
                                        <span>{{ formatDate(selectedEpisode.air_date) }}</span>
                                        <span v-if="selectedEpisode.runtime">{{ selectedEpisode.runtime }}min</span>
                                        <span v-if="selectedEpisode.vote_average" class="flex items-center">
                                            <Star class="w-4 h-4 mr-1 text-yellow-400" />
                                            {{ selectedEpisode.vote_average.toFixed(1) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-300 leading-relaxed">{{ selectedEpisode.overview }}</p>
                                </div>
                            </div>

                            <!-- Watch Providers -->
                            <div v-if="selectedEpisode.watch_providers?.US" class="bg-white/5 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-white mb-3">Where to Watch</h4>
                                
                                <div v-if="selectedEpisode.watch_providers.US.flatrate?.length" class="mb-4">
                                    <p class="text-sm text-gray-400 mb-2">Streaming</p>
                                    <div class="flex flex-wrap gap-2">
                                        <div 
                                            v-for="provider in selectedEpisode.watch_providers.US.flatrate"
                                            :key="provider.provider_id"
                                            class="flex items-center space-x-2 bg-white/10 rounded-lg p-2"
                                        >
                                            <img 
                                                :src="`https://image.tmdb.org/t/p/w45${provider.logo_path}`"
                                                :alt="provider.provider_name"
                                                class="w-6 h-6 rounded"
                                            />
                                            <span class="text-white text-sm">{{ provider.provider_name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="selectedEpisode.watch_providers.US.buy?.length" class="mb-4">
                                    <p class="text-sm text-gray-400 mb-2">Buy</p>
                                    <div class="flex flex-wrap gap-2">
                                        <div 
                                            v-for="provider in selectedEpisode.watch_providers.US.buy"
                                            :key="provider.provider_id"
                                            class="flex items-center space-x-2 bg-white/10 rounded-lg p-2"
                                        >
                                            <img 
                                                :src="`https://image.tmdb.org/t/p/w45${provider.logo_path}`"
                                                :alt="provider.provider_name"
                                                class="w-6 h-6 rounded"
                                            />
                                            <span class="text-white text-sm">{{ provider.provider_name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="selectedEpisode.watch_providers.US.rent?.length">
                                    <p class="text-sm text-gray-400 mb-2">Rent</p>
                                    <div class="flex flex-wrap gap-2">
                                        <div 
                                            v-for="provider in selectedEpisode.watch_providers.US.rent"
                                            :key="provider.provider_id"
                                            class="flex items-center space-x-2 bg-white/10 rounded-lg p-2"
                                        >
                                            <img 
                                                :src="`https://image.tmdb.org/t/p/w45${provider.logo_path}`"
                                                :alt="provider.provider_name"
                                                class="w-6 h-6 rounded"
                                            />
                                            <span class="text-white text-sm">{{ provider.provider_name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="!selectedEpisode.watch_providers.US.flatrate?.length && !selectedEpisode.watch_providers.US.buy?.length && !selectedEpisode.watch_providers.US.rent?.length">
                                    <p class="text-gray-400 text-sm">No streaming providers available in your region.</p>
                                </div>
                            </div>

                            <!-- Watch Status Toggle -->
                            <div class="flex justify-center">
                                <label class="flex items-center cursor-pointer bg-white/10 rounded-lg p-4 hover:bg-white/20 transition-colors">
                                    <span class="text-white mr-3">Mark as watched</span>
                                    <input 
                                        type="checkbox"
                                        :checked="selectedEpisode ? isEpisodeWatched(selectedEpisode) : false"
                                        @change="selectedEpisode && ((e: Event) => toggleEpisodeWatched(selectedEpisode!, (e.target as HTMLInputElement).checked))"
                                        class="sr-only"
                                    />
                                    <div class="relative">
                                        <div :class="[
                                            'w-6 h-6 rounded-full border-2 transition-all duration-200',
                                            isEpisodeWatched(selectedEpisode) 
                                                ? 'bg-green-500 border-green-500' 
                                                : 'border-gray-400 hover:border-green-400'
                                        ]"></div>
                                        <div v-if="isEpisodeWatched(selectedEpisode)" 
                                             class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { X, Star, ChevronRight } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

interface Episode {
    id: number;
    episode_number: number;
    name: string;
    overview: string;
    still_path: string;
    air_date: string;
    runtime?: number;
    vote_average?: number;
}

interface EpisodeDetails extends Episode {
    watch_providers?: {
        [country: string]: {
            flatrate?: Array<{
                logo_path: string;
                provider_name: string;
                provider_id: number;
            }>;
            buy?: Array<{
                logo_path: string;
                provider_name: string;
                provider_id: number;
            }>;
            rent?: Array<{
                logo_path: string;
                provider_name: string;
                provider_id: number;
            }>;
        };
    };
}

interface Season {
    id: number;
    season_number: number;
    name: string;
    overview: string;
    poster_path: string;
    air_date: string;
    episode_count: number;
}

interface MediaData {
    id: number;
    name?: string;
    title?: string;
    overview: string;
    poster_path: string;
    backdrop_path: string;
    vote_average: number;
    first_air_date?: string;
    release_date?: string;
    number_of_seasons?: number;
    runtime?: number;
    seasons?: Season[];
}

const props = defineProps<{
    isVisible: boolean;
    itemId?: number;
    type: 'tv' | 'movie';
}>();

const emit = defineEmits<{
    close: [];
    episodesUpdated: [];
}>();

const { success, error } = useToast();

// State
const loading = ref(false);
const mediaData = ref<MediaData | null>(null);
const selectedSeason = ref<Season | null>(null);
const episodes = ref<Episode[]>([]);
const watchedEpisodes = ref<Set<number>>(new Set());
const completedSeasons = ref<Set<number>>(new Set());
const episodesWereUpdated = ref(false);
const selectedEpisode = ref<EpisodeDetails | null>(null);
const episodeDetailsLoading = ref(false);

// Image URLs
const tmdbImageBaseUrl = 'https://image.tmdb.org/t/p/w500';
const tmdbBackdropBaseUrl = 'https://image.tmdb.org/t/p/w1280';
const tmdbStillBaseUrl = 'https://image.tmdb.org/t/p/w300';

const getPosterUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w500/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return tmdbImageBaseUrl + path;
};

const getBackdropUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w1280/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return tmdbBackdropBaseUrl + path;
};

const getStillUrl = (path: string | null | undefined) => {
    if (!path) return 'https://image.tmdb.org/t/p/w300/w6KapI2JvrCkOPmQhkwYPJNjqeo.jpg';
    return tmdbStillBaseUrl + path;
};

const formatYear = (dateString: string | null | undefined) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).getFullYear();
};

const formatDate = (dateString: string | null | undefined) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Methods
const closeModal = () => {
    if (episodesWereUpdated.value) {
        emit('episodesUpdated');
        episodesWereUpdated.value = false;
    }
    emit('close');
    selectedSeason.value = null;
    episodes.value = [];
    mediaData.value = null;
    watchedEpisodes.value.clear();
    completedSeasons.value.clear();
    selectedEpisode.value = null;
    episodeDetailsLoading.value = false;
};

const fetchMediaData = async () => {
    if (!props.itemId) return;
    
    loading.value = true;
    try {
        const response = await fetch(`/api/media/${props.type}/${props.itemId}`);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Failed to fetch media data: ${response.status} ${errorText}`);
        }
        
        const data = await response.json();
        mediaData.value = data;
        
        // Load watched episodes for TV shows
        if (props.type === 'tv') {
            await loadWatchedEpisodes();
        }
    } catch (err) {
        console.error('Failed to fetch media data:', err);
        error('Error', 'Failed to load media details. Please try again.');
    } finally {
        loading.value = false;
    }
};

const selectSeason = async (season: Season) => {
    selectedSeason.value = season;
    loading.value = true;
    
    try {
        const response = await fetch(`/api/tv/${props.itemId}/season/${season.season_number}`);
        if (!response.ok) throw new Error('Failed to fetch season data');
        
        const data = await response.json();
        episodes.value = data.episodes || [];
    } catch (err) {
        console.error('Failed to fetch season data:', err);
        error('Error', 'Failed to load season episodes');
    } finally {
        loading.value = false;
    }
};

const loadWatchedEpisodes = async () => {
    try {
        const response = await fetch(`/api/watch-progress/show/${props.itemId}`);
        if (!response.ok) return;
        
        const data = await response.json();
        watchedEpisodes.value = new Set(data);
        
        // Calculate completed seasons based on watched episodes
        if (mediaData.value?.seasons) {
            completedSeasons.value.clear();
            for (const season of mediaData.value.seasons) {
                if (season.season_number > 0) {
                    // Fetch season data to check if all episodes are watched
                    try {
                        const seasonResponse = await fetch(`/api/tv/${props.itemId}/season/${season.season_number}`);
                        if (seasonResponse.ok) {
                            const seasonData = await seasonResponse.json();
                            const allEpisodesWatched = seasonData.episodes?.every((ep: any) => watchedEpisodes.value.has(ep.id));
                            if (allEpisodesWatched && seasonData.episodes?.length > 0) {
                                completedSeasons.value.add(season.season_number);
                            }
                        }
                    } catch (err) {
                        console.error(`Failed to check season ${season.season_number} completion:`, err);
                    }
                }
            }
        }
    } catch (err) {
        console.error('Failed to load watch progress:', err);
    }
};

const isEpisodeWatched = (episode: Episode) => {
    return watchedEpisodes.value.has(episode.id);
};

const toggleEpisodeWatched = async (episode: Episode | EpisodeDetails, checked?: boolean) => {
    const isWatched = checked !== undefined ? checked : !isEpisodeWatched(episode);
    
    try {
        const response = await fetch('/api/watch-progress/episode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                episode_id: episode.id,
                show_id: props.itemId,
                watched: isWatched
            })
        });

        if (!response.ok) throw new Error('Failed to update episode status');

        if (isWatched) {
            watchedEpisodes.value.add(episode.id);
            success('Episode Updated', `Marked "${episode.name}" as watched`);
        } else {
            watchedEpisodes.value.delete(episode.id);
            success('Episode Updated', `Marked "${episode.name}" as unwatched`);
        }
        
        // Mark that episodes were updated
        episodesWereUpdated.value = true;
    } catch (err) {
        console.error('Failed to update episode status:', err);
        error('Error', 'Failed to update episode status');
    }
};

const markSeasonComplete = async (seasonNumber: number, checked?: boolean) => {
    const watched = checked !== undefined ? checked : true;
    
    try {
        const response = await fetch('/api/watch-progress/season', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                show_id: props.itemId,
                season_number: seasonNumber,
                watched: watched
            })
        });

        if (!response.ok) throw new Error('Failed to mark season complete');

        // Update local state
        if (watched) {
            completedSeasons.value.add(seasonNumber);
        } else {
            completedSeasons.value.delete(seasonNumber);
        }

        success('Season Updated', `Season ${seasonNumber} ${watched ? 'marked as complete' : 'unmarked'}`);
        
        // Mark that episodes were updated
        episodesWereUpdated.value = true;
        
        // Reload watched episodes to update the UI
        await loadWatchedEpisodes();
    } catch (err) {
        console.error('Failed to mark season complete:', err);
        error('Error', 'Failed to update season status');
    }
};

const isSeasonComplete = (seasonNumber: number): boolean => {
    return completedSeasons.value.has(seasonNumber);
};

const openEpisodeDetails = async (episode: Episode) => {
    episodeDetailsLoading.value = true;
    
    try {
        // Fetch episode details including watch providers
        const response = await fetch(`/api/tv/${props.itemId}/season/${selectedSeason.value?.season_number}/episode/${episode.episode_number}`);
        if (!response.ok) throw new Error('Failed to fetch episode details');
        
        const episodeData = await response.json();
        selectedEpisode.value = { ...episode, ...episodeData };
    } catch (err) {
        console.error('Failed to fetch episode details:', err);
        // Fall back to basic episode data
        selectedEpisode.value = episode as EpisodeDetails;
    } finally {
        episodeDetailsLoading.value = false;
    }
};

// Watch for modal visibility changes
watch(() => props.isVisible, (newValue) => {
    if (newValue && props.itemId) {
        fetchMediaData();
    } else if (!newValue) {
        selectedSeason.value = null;
        episodes.value = [];
        mediaData.value = null;
        watchedEpisodes.value.clear();
        completedSeasons.value.clear();
        episodesWereUpdated.value = false;
        selectedEpisode.value = null;
        episodeDetailsLoading.value = false;
    }
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
:deep(.overflow-y-auto) {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.5) rgba(255, 255, 255, 0.1);
}

:deep(.overflow-y-auto::-webkit-scrollbar) {
    width: 8px;
}

:deep(.overflow-y-auto::-webkit-scrollbar-track) {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}

:deep(.overflow-y-auto::-webkit-scrollbar-thumb) {
    background: rgba(59, 130, 246, 0.6);
    border-radius: 4px;
    transition: background 0.2s ease;
}

:deep(.overflow-y-auto::-webkit-scrollbar-thumb:hover) {
    background: rgba(59, 130, 246, 0.8);
}

:deep(.overflow-y-auto::-webkit-scrollbar-corner) {
    background: rgba(255, 255, 255, 0.05);
}

/* Smooth scrolling */
:deep(.overflow-y-auto) {
    scroll-behavior: smooth;
}
</style>
