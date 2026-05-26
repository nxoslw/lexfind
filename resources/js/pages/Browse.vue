<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Scale, Search, X, SlidersHorizontal } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AttorneyCard from '@/components/AttorneyCard.vue';

interface Lawyer {
    id: number;
    slug: string;
    name: string;
    title: string;
    firm: string;
    city: string;
    state: string;
    specialty: string;
    bio: string;
    avatar_color: string;
    initials: string;
    email: string;
    phone: string;
    website: string;
    linkedin: string | null;
    years_experience: number;
    cases_count: number;
    cases_won: number;
    cases_lost: number;
    cases_settled: number;
    cases_active: number;
    financial_recovery: string | null;
    fee_structure: string | null;
    is_certified: boolean;
    rating: number;
    availability: string;
    criminal_record: string;
    bar_discipline: string;
    practice_areas: string[];
}

const props = defineProps<{
    lawyers: Lawyer[];
    filters: {
        name?: string;
        practice?: string;
        city?: string;
        state?: string;
        exp?: string;
        win?: string;
        rating?: string;
        avail?: string;
        cert?: string;
        sort?: string;
    };
}>();

const page = usePage();

const validSorts = ['rating', 'exp', 'win', 'cases'];
const getValidSort = (val: any): string => {
    return validSorts.includes(val) ? val : 'rating';
};

// Filter States
const searchName = ref(props.filters.name || '');
const searchPractice = ref(props.filters.practice || '');
const searchCity = ref(props.filters.city || '');
const filterState = ref(props.filters.state || '');
const filterExp = ref(props.filters.exp || '0');
const filterWin = ref(props.filters.win || '0');
const filterRating = ref(props.filters.rating || '0');
const filterAvail = ref(props.filters.avail || '');
const filterCert = ref(props.filters.cert || '');
const sortBy = ref(getValidSort(props.filters.sort));

// Advanced filter panel toggle
const isAdvancedOpen = ref(false);
const toggleAdvanced = () => {
    isAdvancedOpen.value = !isAdvancedOpen.value;
};

// Quick status filter ('all' vs 'available')
const activeStatusFilter = ref(
    props.filters.avail === 'available' ? 'available' : 'all',
);

const updateFilters = () => {
    router.get(
        '/browse',
        {
            name: searchName.value || undefined,
            practice: searchPractice.value || undefined,
            city: searchCity.value || undefined,
            state: filterState.value || undefined,
            exp: filterExp.value !== '0' ? filterExp.value : undefined,
            win: filterWin.value !== '0' ? filterWin.value : undefined,
            rating: filterRating.value !== '0' ? filterRating.value : undefined,
            avail: filterAvail.value || undefined,
            cert: filterCert.value || undefined,
            sort: sortBy.value !== 'rating' ? sortBy.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

// Keep local filter state synced with props (handles back/forward navigation and link clicks)
watch(
    () => props.filters,
    (newFilters) => {
        if (newFilters) {
            searchName.value = newFilters.name || '';
            searchPractice.value = newFilters.practice || '';
            searchCity.value = newFilters.city || '';
            filterState.value = newFilters.state || '';
            filterExp.value = newFilters.exp || '0';
            filterWin.value = newFilters.win || '0';
            filterRating.value = newFilters.rating || '0';
            filterAvail.value = newFilters.avail || '';
            filterCert.value = newFilters.cert || '';
            sortBy.value = getValidSort(newFilters.sort);
        }
    },
    { deep: true },
);

// Watch triggers for select dropdowns & toggles
watch(
    [
        searchPractice,
        filterState,
        filterExp,
        filterWin,
        filterRating,
        filterAvail,
        filterCert,
        sortBy,
    ],
    () => {
        // Only update if local state differs from current URL props to prevent loops
        const hasChanges =
            searchPractice.value !== (props.filters.practice || '') ||
            filterState.value !== (props.filters.state || '') ||
            filterExp.value !== (props.filters.exp || '0') ||
            filterWin.value !== (props.filters.win || '0') ||
            filterRating.value !== (props.filters.rating || '0') ||
            filterAvail.value !== (props.filters.avail || '') ||
            filterCert.value !== (props.filters.cert || '') ||
            sortBy.value !== getValidSort(props.filters.sort);

        if (hasChanges) {
            updateFilters();
        }
    },
);

// Typing inputs debounced check (or search click)
const triggerSearch = () => {
    updateFilters();
};

const setPracticeChip = (practiceArea: string) => {
    searchPractice.value = practiceArea;
};

const setQuickStatus = (status: 'all' | 'available') => {
    activeStatusFilter.value = status;
    filterAvail.value = status === 'available' ? 'available' : '';
};

const clearFilters = () => {
    searchName.value = '';
    searchPractice.value = '';
    searchCity.value = '';
    filterState.value = '';
    filterExp.value = '0';
    filterWin.value = '0';
    filterRating.value = '0';
    filterAvail.value = '';
    filterCert.value = '';
    activeStatusFilter.value = 'all';
    sortBy.value = 'rating';
    updateFilters();
};

// Dropdown state and logout handlers removed (handled by PublicLayout)
</script>

<template>
    <Head title="Browse Directory — LexFind" />

    <div class="flex flex-1 flex-col bg-[#F3EFE8] font-sans text-[#16161A]">
        <!-- Search Hero Header -->
        <div
            class="relative overflow-hidden bg-[#0A1929] px-6 pt-16 pb-8 text-white md:px-12"
        >
            <div
                class="pointer-events-none absolute top-[-100px] right-[-100px] h-[500px] w-[500px] rounded-full bg-gradient-to-br from-[#C8961E]/5 to-transparent blur-3xl"
            ></div>

            <div class="relative z-10 mx-auto max-w-4xl text-center">
                <div
                    class="mb-6 inline-flex items-center gap-1.5 rounded-full border border-[#C8961E]/20 bg-[#C8961E]/12 px-3.5 py-1 text-xs font-semibold tracking-wider text-[#F0C96A] uppercase"
                >
                    <Scale class="h-3.5 w-3.5" /> Verified Attorney Network
                </div>
                <h1
                    class="mb-4 font-serif text-3xl leading-tight font-bold text-white md:text-5xl"
                >
                    Find the
                    <span class="font-semibold text-[#DBA93E] italic"
                        >Right Lawyer</span
                    ><br />for Your Case
                </h1>
                <p
                    class="mx-auto mb-8 max-w-xl text-sm leading-relaxed text-white/55 md:text-base"
                >
                    Search verified attorneys across all 50 states — filtered by
                    practice area, win rate, experience, and availability.
                </p>

                <!-- Search Box Controls -->
                <div
                    class="flex flex-col items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-md md:flex-row"
                >
                    <!-- Search Input -->
                    <div
                        class="flex w-full items-center gap-2 rounded-xl border border-white/12 bg-white/8 px-4 py-3 md:flex-[2]"
                    >
                        <Search class="h-4 w-4 text-white/30" />
                        <input
                            type="text"
                            v-model="searchName"
                            @keyup.enter="triggerSearch"
                            placeholder="Attorney name or firm..."
                            class="w-full border-none bg-transparent text-sm text-white placeholder-white/30 outline-none"
                        />
                    </div>
                    <div class="hidden h-8 w-px bg-white/10 md:block"></div>

                    <!-- Practice Select -->
                    <div
                        class="flex w-full items-center gap-2 rounded-xl border border-white/12 bg-white/8 px-4 py-3 md:flex-1"
                    >
                        <Briefcase class="h-4 w-4 text-white/30" />
                        <select
                            v-model="searchPractice"
                            class="w-full cursor-pointer appearance-none border-none bg-transparent text-sm text-white outline-none focus:outline-none"
                        >
                            <option value="" class="bg-[#0F2234] text-white">
                                All practice areas
                            </option>
                            <option
                                value="Business Litigation"
                                class="bg-[#0F2234] text-white"
                            >
                                Business Litigation
                            </option>
                            <option
                                value="Personal Injury"
                                class="bg-[#0F2234] text-white"
                            >
                                Personal Injury
                            </option>
                            <option
                                value="Corporate Law"
                                class="bg-[#0F2234] text-white"
                            >
                                Corporate Law
                            </option>
                            <option
                                value="Criminal Defense"
                                class="bg-[#0F2234] text-white"
                            >
                                Criminal Defense
                            </option>
                            <option
                                value="Family Law"
                                class="bg-[#0F2234] text-white"
                            >
                                Family Law
                            </option>
                            <option
                                value="Civil Rights"
                                class="bg-[#0F2234] text-white"
                            >
                                Civil Rights
                            </option>
                            <option
                                value="Immigration"
                                class="bg-[#0F2234] text-white"
                            >
                                Immigration
                            </option>
                            <option
                                value="Tax Law"
                                class="bg-[#0F2234] text-white"
                            >
                                Tax Law
                            </option>
                            <option
                                value="Real Estate"
                                class="bg-[#0F2234] text-white"
                            >
                                Real Estate
                            </option>
                            <option
                                value="Employment"
                                class="bg-[#0F2234] text-white"
                            >
                                Employment
                            </option>
                        </select>
                    </div>
                    <div class="hidden h-8 w-px bg-white/10 md:block"></div>

                    <!-- Location Input -->
                    <div
                        class="flex w-full items-center gap-2 rounded-xl border border-white/12 bg-white/8 px-4 py-3 md:flex-1"
                    >
                        <MapPin class="h-4 w-4 text-white/30" />
                        <input
                            type="text"
                            v-model="searchCity"
                            @keyup.enter="triggerSearch"
                            placeholder="City..."
                            class="w-full border-none bg-transparent text-sm text-white placeholder-white/30 outline-none"
                        />
                    </div>

                    <button
                        @click="triggerSearch"
                        class="w-full cursor-pointer rounded-xl bg-[#C8961E] px-6 py-3 text-sm font-bold text-[#0A1929] transition-all hover:bg-[#DBA93E] md:w-auto"
                    >
                        Search
                    </button>
                </div>

                <!-- Quick Practice Chips -->
                <div class="mt-6 flex flex-wrap justify-center gap-2.5">
                    <button
                        @click="setPracticeChip('')"
                        :class="[
                            'cursor-pointer rounded-full border px-4 py-1.5 text-xs font-semibold transition-all',
                            searchPractice === ''
                                ? 'border-[#C8961E] bg-[#C8961E] text-[#0A1929]'
                                : 'border-white/10 bg-white/5 text-white/60 hover:text-white',
                        ]"
                    >
                        All
                    </button>
                    <button
                        v-for="area in [
                            'Personal Injury',
                            'Criminal Defense',
                            'Corporate Law',
                            'Family Law',
                            'Business Litigation',
                            'Immigration',
                            'Tax Law',
                            'Civil Rights',
                        ]"
                        :key="area"
                        @click="setPracticeChip(area)"
                        :class="[
                            'cursor-pointer rounded-full border px-4 py-1.5 text-xs font-semibold transition-all',
                            searchPractice === area
                                ? 'border-[#C8961E] bg-[#C8961E] text-[#0A1929]'
                                : 'border-white/10 bg-white/5 text-white/60 hover:text-white',
                        ]"
                    >
                        {{ area }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter controls section -->
        <section class="mx-auto w-full max-w-7xl px-6 py-8 md:px-12">
            <!-- Filter Bar -->
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span
                    class="text-xs font-bold tracking-wider text-neutral-400 uppercase"
                    >Filter by:</span
                >
                <button
                    @click="setQuickStatus('all')"
                    :class="[
                        'flex cursor-pointer items-center gap-1.5 rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        activeStatusFilter === 'all'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    All Attorneys
                </button>
                <button
                    @click="setQuickStatus('available')"
                    :class="[
                        'flex cursor-pointer items-center gap-1.5 rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        activeStatusFilter === 'available'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    <span
                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                    ></span>
                    Available Now
                </button>
                <button
                    @click="filterCert = filterCert === 'yes' ? '' : 'yes'"
                    :class="[
                        'cursor-pointer rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        filterCert === 'yes'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    🏅 Board Certified
                </button>
                <button
                    @click="filterRating = filterRating === '4.8' ? '0' : '4.8'"
                    :class="[
                        'cursor-pointer rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        filterRating === '4.8'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    ⭐ Top Rated (4.8+)
                </button>
                <button
                    @click="filterExp = filterExp === '20' ? '0' : '20'"
                    :class="[
                        'cursor-pointer rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        filterExp === '20'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    🏛 20+ Years Exp
                </button>
                <button
                    @click="filterWin = filterWin === '90' ? '0' : '90'"
                    :class="[
                        'cursor-pointer rounded-full border px-4 py-2 text-xs font-bold transition-all',
                        filterWin === '90'
                            ? 'border-[#0A1929] bg-[#0A1929] text-white'
                            : 'border-black/10 bg-white text-neutral-600 hover:border-black/30',
                    ]"
                >
                    📈 High Win Rate (90%+)
                </button>

                <button
                    @click="toggleAdvanced"
                    class="ml-auto flex cursor-pointer items-center gap-1.5 rounded-full border border-black/10 bg-white px-4 py-2 text-xs font-bold transition-all hover:border-black/30"
                >
                    <SlidersHorizontal class="h-3.5 w-3.5 text-neutral-500" />
                    Advanced Filters
                </button>
            </div>

            <!-- Advanced Filters Drawer -->
            <div
                v-if="isAdvancedOpen"
                class="mb-6 animate-[fadeUp_0.15s_ease-out] rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
            >
                <div
                    class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6"
                >
                    <!-- State -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >State</label
                        >
                        <select
                            v-model="filterState"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="">Any state</option>
                            <option value="FL">Florida</option>
                            <option value="CA">California</option>
                            <option value="NY">New York</option>
                            <option value="TX">Texas</option>
                            <option value="IL">Illinois</option>
                            <option value="DC">Washington D.C.</option>
                        </select>
                    </div>
                    <!-- Min Exp -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >Min. Experience</label
                        >
                        <select
                            v-model="filterExp"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="0">Any</option>
                            <option value="5">5+ years</option>
                            <option value="10">10+ years</option>
                            <option value="20">20+ years</option>
                            <option value="30">30+ years</option>
                        </select>
                    </div>
                    <!-- Min Win Rate -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >Min. Win Rate</label
                        >
                        <select
                            v-model="filterWin"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="0">Any</option>
                            <option value="70">70%+</option>
                            <option value="80">80%+</option>
                            <option value="90">90%+</option>
                            <option value="95">95%+</option>
                        </select>
                    </div>
                    <!-- Min Rating -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >Min. Rating</label
                        >
                        <select
                            v-model="filterRating"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="0">Any</option>
                            <option value="4">4.0+</option>
                            <option value="4.5">4.5+</option>
                            <option value="4.8">4.8+</option>
                        </select>
                    </div>
                    <!-- Availability -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >Availability</label
                        >
                        <select
                            v-model="filterAvail"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="">Any</option>
                            <option value="available">Available Now</option>
                            <option value="busy">Busy (waitlist)</option>
                        </select>
                    </div>
                    <!-- Board Certified -->
                    <div class="flex flex-col">
                        <label
                            class="mb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >Board Certified</label
                        >
                        <select
                            v-model="filterCert"
                            class="w-full rounded-lg border border-black/5 bg-[#F3EFE8] px-3 py-2 text-xs transition-all outline-none focus:border-[#0A1929]"
                        >
                            <option value="">Any</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Count and Sort -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <span class="text-sm font-semibold text-neutral-500"
                    >Showing {{ lawyers.length }} attorney{{
                        lawyers.length !== 1 ? 's' : ''
                    }}</span
                >
                <select
                    v-model="sortBy"
                    class="cursor-pointer rounded-lg border border-black/10 bg-white px-3 py-2 text-xs text-neutral-700 focus:outline-none"
                >
                    <option value="rating">Sort: Top Rated</option>
                    <option value="exp">Sort: Most Experienced</option>
                    <option value="win">Sort: Highest Win Rate</option>
                    <option value="cases">Sort: Most Cases</option>
                </select>
            </div>

            <!-- Lawyer Cards Grid -->
            <div
                v-if="lawyers.length > 0"
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <AttorneyCard
                    v-for="lawyer in lawyers"
                    :key="lawyer.id"
                    :lawyer="lawyer"
                />
            </div>

            <!-- No results view -->
            <div
                v-else
                class="rounded-2xl border border-black/5 bg-white py-20 text-center shadow-sm"
            >
                <Search class="mx-auto mb-4 h-12 w-12 text-neutral-300" />
                <h3 class="mb-1 font-serif text-lg font-bold text-[#0A1929]">
                    No attorneys match your filters
                </h3>
                <p class="text-sm text-neutral-400">
                    Try adjusting your search criteria or clearing filters.
                </p>
                <button
                    @click="clearFilters"
                    class="mt-6 cursor-pointer rounded-xl bg-[#C8961E] px-6 py-2.5 text-xs font-bold tracking-wider text-[#0A1929] uppercase transition-all hover:bg-[#DBA93E]"
                >
                    Clear all filters
                </button>
            </div>
        </section>
    </div>
</template>

<style scoped>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
