<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Scale,
    Search,
    Globe,
    Lock,
    Shield,
    Activity,
    Briefcase,
    ShieldAlert,
    CheckCircle2,
    Star,
    BadgeCheck,
    Mail,
    Phone,
    SlidersHorizontal,
    LayoutDashboard,
    UserCheck,
    FileText,
    ArrowRight,
    MapPin,
    AlertCircle,
    User
} from 'lucide-vue-next';
import { dashboard } from '@/routes';

interface UserInfo {
    id: number;
    name: string;
    email: string;
    system: string;
}

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
    user?: UserInfo | null;
}

interface LawyerCase {
    id: number;
    lawyer_id: number;
    name: string;
    slug: string;
    case_number: string;
    jurisdiction: string;
    type: string;
    type_label: string;
    status: string;
    year: number;
    court: string;
    summary: string | null;
    lawyer?: Lawyer;
}

const props = defineProps<{
    role: 'bip' | 'god';
    lawyer: Lawyer | null;
    stats: Record<string, any>;
    lawyers?: Lawyer[];
    featuredLawyers?: Lawyer[];
    recentCases?: LawyerCase[];
    assignedLawyers?: Lawyer[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// Regular User Dashboard - Search States
const searchName = ref('');

const handleUserSearch = () => {
    if (searchName.value.trim()) {
        router.get('/browse', { name: searchName.value });
    } else {
        router.get('/browse');
    }
};

// Front Mod Dashboard - Search States
const modSearchQuery = ref('');
const filteredLawyers = computed(() => {
    if (!props.lawyers) return [];
    if (!modSearchQuery.value.trim()) return props.lawyers;
    const q = modSearchQuery.value.toLowerCase();
    return props.lawyers.filter(
        (l) =>
            l.name.toLowerCase().includes(q) ||
            l.specialty.toLowerCase().includes(q) ||
            l.firm.toLowerCase().includes(q) ||
            l.city.toLowerCase().includes(q)
    );
});

// Front Mod - Toggle Certified Status
const togglingId = ref<number | null>(null);
const toggleCertifiedStatus = (lawyerId: number) => {
    togglingId.value = lawyerId;
    router.post(
        `/moderator/lawyers/${lawyerId}/toggle-certified`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                togglingId.value = null;
            },
        }
    );
};


</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 bg-[#F8F6F0] min-h-screen text-[#16161A]">
        <!-- SUCCESS MESSAGES -->
            <div
                v-if="$page.props.flash?.success"
                class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 animate-[fadeUp_0.15s_ease-out]"
            >
                <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-600" />
                <div>{{ $page.props.flash.success }}</div>
            </div>

            <!-- ROLE: FRONT MODERATOR (bip) -->
            <div v-if="props.role === 'bip'" class="space-y-8">
                <!-- Welcome Moderator Banner -->
                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1929] to-[#1E3A54] p-6 text-white shadow-xl lg:p-8"
                >
                    <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:20px_20px]"></div>
                    <div class="relative z-10 space-y-2">
                        <span
                            class="rounded border border-[#C8961E]/30 bg-[#C8961E]/20 px-2.5 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                        >
                            Front Mod Command
                        </span>
                        <h1 class="font-serif text-3xl font-bold tracking-tight">
                            Directory Moderation Queue
                        </h1>
                        <p class="max-w-xl text-xs text-white/60">
                            Oversee lawyer certifications, manage network availability, and review directory statistics to maintain public system integrity.
                        </p>
                    </div>
                </div>

                <!-- Moderator Stats Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-xs text-left">
                        <span class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase">Directory Listings</span>
                        <h3 class="mt-2 font-serif text-3xl font-bold text-neutral-800">{{ stats.total_lawyers }}</h3>
                        <p class="mt-1 text-[10px] text-neutral-400">Total profiles registered</p>
                    </div>
                    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-xs text-left">
                        <span class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase">Board Certified</span>
                        <h3 class="mt-2 font-serif text-3xl font-bold text-[#C8961E]">{{ stats.certified_count }}</h3>
                        <p class="mt-1 text-[10px] text-neutral-400">
                            {{ Math.round((stats.certified_count / (stats.total_lawyers || 1)) * 100) }}% certification rate
                        </p>
                    </div>
                    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-xs text-left">
                        <span class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase">Active Available</span>
                        <h3 class="mt-2 font-serif text-3xl font-bold text-emerald-600">{{ stats.available_count }}</h3>
                        <p class="mt-1 text-[10px] text-neutral-400">Attorneys accepting clients</p>
                    </div>
                    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-xs text-left">
                        <span class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase">Average Rating</span>
                        <h3 class="mt-2 font-serif text-3xl font-bold text-sky-600">{{ stats.average_rating }} ★</h3>
                        <p class="mt-1 text-[10px] text-neutral-400">Overall client rating score</p>
                    </div>
                </div>

                <!-- Moderation Directory Queue Table -->
                <div class="overflow-hidden rounded-2xl border border-black/5 bg-white shadow-xs">
                    <div class="flex flex-col gap-4 border-b border-neutral-100 bg-neutral-50/50 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-left">
                            <h3 class="font-serif text-base font-bold text-neutral-800">Attorney Listings Queue</h3>
                            <p class="text-[10px] text-neutral-400">Search and audit certified statuses for network profiles.</p>
                        </div>
                        <div class="relative min-w-[280px]">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                                <Search class="h-4 w-4" />
                            </span>
                            <input
                                v-model="modSearchQuery"
                                type="text"
                                placeholder="Search by name, specialty, or firm..."
                                class="w-full rounded-xl border border-black/10 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E]"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-neutral-100 bg-neutral-50/30 text-[9px] tracking-wider text-neutral-400 uppercase">
                                    <th class="px-6 py-4">Attorney Info</th>
                                    <th class="px-4 py-4">Specialty & City</th>
                                    <th class="px-4 py-4">Rating & Experience</th>
                                    <th class="px-4 py-4">Board Certified</th>
                                    <th class="px-4 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr v-if="filteredLawyers.length === 0" class="text-center text-neutral-400">
                                    <td colspan="6" class="py-12">No attorney profiles found.</td>
                                </tr>
                                <tr
                                    v-for="l in filteredLawyers"
                                    :key="l.id"
                                    class="transition-all hover:bg-neutral-50/30"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg font-serif text-xs font-bold text-white shadow-inner"
                                                :style="{ backgroundColor: l.avatar_color }"
                                            >
                                                {{ l.initials }}
                                            </div>
                                            <div class="text-left">
                                                <div class="font-semibold text-neutral-800">{{ l.name }}</div>
                                                <div class="text-[10px] text-neutral-400">{{ l.firm }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-left">
                                        <div class="font-medium text-neutral-700">{{ l.specialty }}</div>
                                        <div class="text-[10px] text-neutral-400">{{ l.city }}, {{ l.state }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-left">
                                        <div class="flex items-center gap-1 font-semibold text-neutral-700">
                                            <Star class="h-3.5 w-3.5 fill-[#C8961E] text-[#C8961E]" />
                                            {{ l.rating.toFixed(1) }}
                                        </div>
                                        <div class="text-[10px] text-neutral-400">{{ l.years_experience }} Years Exp.</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <button
                                            @click="toggleCertifiedStatus(l.id)"
                                            :disabled="togglingId === l.id"
                                            :class="[
                                                'inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[9px] font-bold tracking-wider uppercase transition-all duration-200 cursor-pointer disabled:opacity-50',
                                                l.is_certified
                                                    ? 'bg-amber-500/10 border-amber-500/20 text-[#7A4F00]'
                                                    : 'bg-neutral-100 border-black/5 text-neutral-400 hover:bg-neutral-200',
                                            ]"
                                        >
                                            <BadgeCheck class="h-3.5 w-3.5" />
                                            {{ l.is_certified ? 'Certified' : 'Uncertified' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            v-if="l.availability === 'available'"
                                            class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-emerald-700 uppercase"
                                        >
                                            Available
                                        </span>
                                        <span
                                            v-else-if="l.availability === 'busy'"
                                            class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-amber-700 uppercase"
                                        >
                                            Busy
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-red-700 uppercase"
                                        >
                                            Unavailable
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="`/lawyers/${l.slug}`"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-[#C8961E] hover:text-[#DBA93E]"
                                        >
                                            <span>View Public</span>
                                            <ArrowRight class="h-3.5 w-3.5" />
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ROLE: REGULAR USER (god) -->
            <div v-else class="space-y-8">
                <!-- Welcome Banner -->
                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1929] to-[#1E3A54] p-6 text-white shadow-xl lg:p-8"
                >
                    <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:20px_20px]"></div>
                    <div class="relative z-10 space-y-3">
                        <span
                            class="rounded border border-[#C8961E]/30 bg-[#C8961E]/20 px-2.5 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                        >
                            LexFind Client Space
                        </span>
                        <h1 class="font-serif text-3xl font-bold tracking-tight">
                            Welcome back, {{ $page.props.auth.user.name }}
                        </h1>
                        <p class="max-w-xl text-xs text-white/60">
                            Search for top-rated, certified attorneys, browse specialties, and manage your account options.
                        </p>

                        <!-- Quick Search Box -->
                        <div class="flex max-w-lg items-center gap-2 rounded-xl border border-white/12 bg-white/8 px-4 py-2 mt-4">
                            <Search class="h-4 w-4 text-white/30" />
                            <input
                                v-model="searchName"
                                type="text"
                                @keyup.enter="handleUserSearch"
                                placeholder="Search by lawyer name or specialty..."
                                class="w-full border-none bg-transparent text-xs text-white placeholder-white/30 outline-none"
                            />
                            <button
                                @click="handleUserSearch"
                                class="rounded-lg bg-[#C8961E] px-4 py-1.5 text-xs font-bold text-[#0A1929] hover:bg-[#DBA93E] cursor-pointer"
                            >
                                Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Practice Chips -->
                <div class="text-left space-y-2">
                    <span class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase">Browse by Practice Area</span>
                    <div class="flex flex-wrap gap-2">
                        <Link
                            v-for="area in [
                                'Personal Injury',
                                'Corporate Law',
                                'Criminal Defense',
                                'Family Law',
                                'Business Litigation',
                                'Immigration'
                            ]"
                            :key="area"
                            :href="`/browse?practice=${area}`"
                            class="rounded-full border border-black/5 bg-white px-4 py-2 text-xs font-semibold text-neutral-600 transition-all hover:border-[#C8961E] hover:text-[#0A1929]"
                        >
                            {{ area }}
                        </Link>
                        <Link
                            href="/browse"
                            class="rounded-full border border-[#C8961E] bg-[#C8961E]/10 px-4 py-2 text-xs font-bold text-[#0A1929] transition-all hover:bg-[#C8961E] hover:text-white"
                        >
                            Browse All
                        </Link>
                    </div>
                </div>

                <!-- Featured Top-Rated Attorneys Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-2 text-left">
                        <div>
                            <h3 class="font-serif text-base font-bold text-neutral-800">Featured Top-Rated Attorneys</h3>
                            <p class="text-[10px] text-neutral-400">Verified attorneys in active practice with top credentials.</p>
                        </div>
                        <span class="rounded-lg border border-black/5 bg-white px-2.5 py-1 text-[10px] font-bold text-neutral-400 uppercase">
                            {{ stats.total_certified }} certified listed
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            v-for="attorney in featuredLawyers"
                            :key="attorney.id"
                            class="flex flex-col justify-between rounded-2xl border border-black/5 bg-white p-5 text-left shadow-xs transition-all hover:-translate-y-1 hover:shadow-md duration-200"
                        >
                            <div class="space-y-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl font-serif text-sm font-bold text-white shadow-inner"
                                        :style="{ backgroundColor: attorney.avatar_color }"
                                    >
                                        {{ attorney.initials }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-neutral-800 text-sm flex items-center gap-1">
                                            {{ attorney.name }}
                                            <BadgeCheck v-if="attorney.is_certified" class="h-4.5 w-4.5 text-[#C8961E]" />
                                        </h4>
                                        <p class="text-[10px] text-neutral-400">{{ attorney.title }}</p>
                                    </div>
                                </div>
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-neutral-400 text-[10px]">Firm:</span>
                                        <span class="font-medium text-neutral-700 truncate max-w-[150px]">{{ attorney.firm }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-400 text-[10px]">Location:</span>
                                        <span class="font-medium text-neutral-700">{{ attorney.city }}, {{ attorney.state }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-neutral-400 text-[10px]">Specialty:</span>
                                        <span class="font-medium text-neutral-700">{{ attorney.specialty }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-neutral-100 pt-4 mt-4 flex items-center justify-between">
                                <div class="flex items-center gap-1 font-bold text-neutral-700 text-xs">
                                    <Star class="h-4 w-4 fill-[#C8961E] text-[#C8961E]" />
                                    {{ attorney.rating.toFixed(1) }}
                                </div>
                                <Link
                                    :href="`/lawyers/${attorney.slug}`"
                                    class="rounded-lg border border-[#C8961E]/30 bg-[#C8961E]/10 px-3 py-1.5 text-[10px] font-bold text-[#0A1929] hover:bg-[#C8961E]"
                                >
                                    View Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Legal Directory Cases feed -->
                <div class="space-y-4">
                    <div class="text-left border-b pb-2">
                        <h3 class="font-serif text-base font-bold text-neutral-800">Recent Directory Case Updates</h3>
                        <p class="text-[10px] text-neutral-400">Latest resolved and active case files across network firms.</p>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="c in recentCases"
                            :key="c.id"
                            class="flex flex-col justify-between gap-3 rounded-2xl border border-black/5 bg-white p-4 text-left transition-all hover:bg-neutral-50/50 sm:flex-row sm:items-center"
                        >
                            <div class="flex items-start gap-3">
                                <div class="rounded-xl bg-[#0A1929]/5 p-2.5 text-[#0A1929]">
                                    <Scale class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-neutral-800 text-xs">{{ c.name }}</h4>
                                    <p class="text-[10px] text-neutral-400">
                                        Case: {{ c.case_number }} • {{ c.court }} ({{ c.year }})
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 justify-between sm:justify-end">
                                <div class="text-right text-xs">
                                    <div class="font-semibold text-neutral-700">{{ c.lawyer?.name }}</div>
                                    <div class="text-[9px] text-neutral-400">{{ c.lawyer?.firm }}</div>
                                </div>
                                <span
                                    :class="[
                                        'rounded border px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                        c.status === 'decided'
                                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600'
                                            : c.status === 'active'
                                            ? 'bg-sky-500/10 border-sky-500/20 text-sky-600'
                                            : 'bg-amber-500/10 border-amber-500/20 text-amber-600',
                                    ]"
                                >
                                    {{ c.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
