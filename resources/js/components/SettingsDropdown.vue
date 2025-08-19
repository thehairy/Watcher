

<template>
    <DropdownMenu @update:open="onOpenChange">
        <DropdownMenuTrigger as-child>
            <button 
                class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:scale-105 flex items-center justify-center"
                :disabled="loading"
                :title="`Country: ${countries.find(c => c.iso_3166_1 === selectedCountry)?.english_name || selectedCountry}`"
            >
                <span 
                    v-if="!loading" 
                    :class="selectedCountryFlag"
                    class="w-6 h-4"
                ></span>
                <Settings 
                    v-else 
                    class="w-5 h-5 animate-spin" 
                />
            </button>
        </DropdownMenuTrigger>
        <DropdownMenuContent 
            align="end" 
            class="w-80 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl overflow-hidden"
            :sideOffset="8"
        >
            <DropdownMenuLabel class="flex items-center gap-2 font-semibold text-white/90 px-4 py-3 border-b border-white/10">
                <Globe class="w-4 h-4 text-blue-400" />
                Settings
            </DropdownMenuLabel>
            
            <DropdownMenuLabel class="text-sm text-white/60 font-normal px-4 py-2 bg-white/5">
                Country/Region
            </DropdownMenuLabel>
            
            <!-- Search Input -->
            <div class="px-4 py-2 border-b border-white/10">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50" />
                    <input
                        ref="searchInput"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search countries..."
                        class="w-full pl-10 pr-10 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-all duration-200"
                        @keydown.stop
                        @keyup.stop
                        @click.stop
                        @input.stop
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/50 hover:text-white/80 transition-colors"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>
            
            <div>
                <div v-if="loading" class="flex items-center justify-center py-8">
                    <div class="w-6 h-6 border-2 border-white/20 border-t-blue-400 rounded-full animate-spin"></div>
                </div>
                <div v-else-if="filteredCountries.length === 0 && searchQuery" class="px-4 py-8 text-center text-white/50">
                    No countries found matching "{{ searchQuery }}"
                </div>
                <div v-else-if="countries.length === 0" class="px-4 py-8 text-center text-white/50">
                    Failed to load countries
                </div>
                <div v-else class="max-h-64 overflow-y-auto custom-scrollbar">
                    <div
                        v-for="country in filteredCountries"
                        :key="country.iso_3166_1"
                        class="flex items-center justify-between cursor-pointer hover:bg-white/10 transition-all duration-200 px-4 py-3 text-white/80 hover:text-white border-b border-white/5 last:border-b-0"
                        :class="{ 'bg-white/5': selectedCountry === country.iso_3166_1 }"
                        @click="updateCountry(country.iso_3166_1)"
                    >
                        <span class="flex items-center gap-3 flex-1 min-w-0">
                            <span 
                                :class="getCountryFlag(country.iso_3166_1)"
                                class="w-6 h-4"
                            ></span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-medium">{{ country.english_name }}</span>
                                <span v-if="country.native_name !== country.english_name" 
                                      class="text-sm text-white/50 truncate">
                                    {{ country.native_name }}
                                </span>
                            </div>
                        </span>
                        <Check 
                            v-if="selectedCountry === country.iso_3166_1" 
                            class="w-4 h-4 text-blue-400 flex-shrink-0 ml-2" 
                        />
                    </div>
                </div>
            </div>
            
            <div class="px-4 py-3 text-xs text-white/50 bg-white/5 border-t border-white/10">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    Used for release dates and streaming availability
                </div>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Settings, Globe, Check, Search, X } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuLabel,
} from '@/components/ui/dropdown-menu';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import { useCountryFlag } from '@/composables/useCountryFlag';
import axios from 'axios';

interface Country {
    iso_3166_1: string;
    english_name: string;
    native_name: string;
}

const page = usePage();
const user = page.props.auth?.user;
const { success, error } = useToast();
const { getCountryFlag } = useCountryFlag();

const countries = ref<Country[]>([]);
const selectedCountry = ref<string>(user?.country || 'US');
const loading = ref(true);
const searchQuery = ref<string>('');
const searchInput = ref<HTMLInputElement>();

// Computed property for the flag emoji
const selectedCountryFlag = computed(() => getCountryFlag(selectedCountry.value));

const filteredCountries = computed(() => {
    if (!searchQuery.value.trim()) {
        return countries.value;
    }
    
    const query = searchQuery.value.toLowerCase().trim();
    return countries.value.filter(country => 
        country.english_name.toLowerCase().includes(query) ||
        country.native_name.toLowerCase().includes(query) ||
        country.iso_3166_1.toLowerCase().includes(query)
    );
});

const loadCountries = async () => {
    try {
        const response = await axios.get('/api/settings/countries');
        countries.value = response.data;
    } catch (err) {
        console.error('Failed to load countries:', err);
        error('Failed to load countries', 'Please try again later');
    } finally {
        loading.value = false;
    }
};

const updateCountry = async (countryCode: string) => {
    console.log('Updating country to:', countryCode, loading.value, selectedCountry.value);
    if (loading.value || countryCode === selectedCountry.value) return;
    
    loading.value = true;
    try {
        await axios.patch('/api/settings/country', {
            country: countryCode
        });
        
        const country = countries.value.find(c => c.iso_3166_1 === countryCode);
        selectedCountry.value = countryCode;
        
        success(
            'Country updated', 
            `Changed to ${country?.english_name || countryCode}. Release dates and streaming availability will now be shown for this region.`
        );
    } catch (err) {
        console.error('Failed to update country:', err);
        error('Failed to update country', 'Please try again later');
    } finally {
        loading.value = false;
    }
};

const onOpenChange = (open: boolean) => {
    if (open) {
        // Focus the search input when dropdown opens
        setTimeout(() => {
            searchInput.value?.focus();
        }, 100);
    } else {
        // Clear search when dropdown closes
        searchQuery.value = '';
    }
};

onMounted(() => {
    loadCountries();
});
</script>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.5) rgba(255, 255, 255, 0.1);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.5);
    border-radius: 3px;
    transition: background 0.2s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.7);
}
</style>