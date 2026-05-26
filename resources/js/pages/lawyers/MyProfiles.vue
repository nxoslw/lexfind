<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Search,
    Globe,
    Lock,
    Shield,
    Activity,
    Star,
    BadgeCheck,
    Mail,
    Phone,
    User
} from 'lucide-vue-next';

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

const props = defineProps<{
    role: string;
    lawyer: Lawyer | null;
    assignedLawyers: Lawyer[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'My Attorney Profile',
                href: '/profile/lawyer',
            },
        ],
    },
});

// Search State for Assigned Lawyers
const assignedSearchQuery = ref('');
const filteredAssignedLawyers = computed(() => {
    const list = props.assignedLawyers ?? [];
    if (!assignedSearchQuery.value.trim()) return list;
    const q = assignedSearchQuery.value.toLowerCase();
    return list.filter(
        (l) =>
            l.name.toLowerCase().includes(q) ||
            l.specialty.toLowerCase().includes(q) ||
            l.firm.toLowerCase().includes(q) ||
            l.city.toLowerCase().includes(q) ||
            l.state.toLowerCase().includes(q)
    );
});

// Inline Lawyer Profile Update Form
const form = useForm({
    bio: props.lawyer?.bio || '',
    email: props.lawyer?.email || '',
    phone: props.lawyer?.phone || '',
    website: props.lawyer?.website || '',
    linkedin: props.lawyer?.linkedin || '',
    availability: props.lawyer?.availability || 'available',
});

const submitProfileForm = () => {
    if (!props.lawyer) return;
    form.post(`/profile/lawyer/${props.lawyer.slug}/update`, {
        preserveScroll: true,
        onSuccess: () => {
            // Success alert / notification is handled by the flash message component
        },
    });
};
</script>

<template>
    <Head title="My Attorney Profile" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 bg-[#F8F6F0] min-h-screen text-[#16161A]">
        <!-- Success/Flash Messages -->
        <div
            v-if="$page.props.flash?.success"
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 animate-[fadeUp_0.15s_ease-out]"
        >
            <CheckCircle class="h-5 w-5 shrink-0 text-emerald-600" />
            <div>{{ $page.props.flash.success }}</div>
        </div>

        <!-- CASE 1: Multiple profiles assigned -->
        <div v-if="props.assignedLawyers.length > 1" class="space-y-6">
            <div class="flex flex-col gap-4 border-b border-neutral-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-left">
                    <h2 class="font-serif text-xl font-bold text-[#0A1929]">My Assigned Attorney Profiles</h2>
                    <p class="text-xs text-neutral-400">Select any profile below to edit its biographical records and contact info.</p>
                </div>
                <div class="relative min-w-[280px]">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                        <Search class="h-4 w-4" />
                    </span>
                    <input
                        v-model="assignedSearchQuery"
                        type="text"
                        placeholder="Search your profiles..."
                        class="w-full rounded-xl border border-black/10 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E]"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="assigned in filteredAssignedLawyers"
                    :key="assigned.id"
                    class="flex flex-col justify-between rounded-2xl border border-black/5 bg-white p-5 text-left shadow-xs transition-all hover:-translate-y-1 hover:shadow-md duration-200"
                >
                    <div class="space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl font-serif text-sm font-bold text-white shadow-inner"
                                :style="{ backgroundColor: assigned.avatar_color }"
                            >
                                {{ assigned.initials }}
                            </div>
                            <div>
                                <h4 class="font-bold text-neutral-800 text-sm flex items-center gap-1">
                                    {{ assigned.name }}
                                    <BadgeCheck v-if="assigned.is_certified" class="h-4.5 w-4.5 text-[#C8961E]" />
                                </h4>
                                <p class="text-[10px] text-neutral-400">{{ assigned.title }}</p>
                            </div>
                        </div>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span class="text-neutral-400 text-[10px]">Firm:</span>
                                <span class="font-medium text-neutral-700 truncate max-w-[150px]">{{ assigned.firm }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-400 text-[10px]">Location:</span>
                                <span class="font-medium text-neutral-700">{{ assigned.city }}, {{ assigned.state }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-400 text-[10px]">Specialty:</span>
                                <span class="font-medium text-neutral-700">{{ assigned.specialty }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-neutral-100 pt-4 mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-1 font-bold text-neutral-700 text-xs">
                            <Star class="h-4 w-4 fill-[#C8961E] text-[#C8961E]" />
                            {{ assigned.rating.toFixed(1) }}
                        </div>
                        <Link
                            :href="`/profile/lawyer/${assigned.slug}`"
                            class="rounded-lg bg-[#0A1929] px-3.5 py-1.5 text-[10px] font-bold tracking-wider text-white uppercase hover:bg-[#1E3A54]"
                        >
                            Edit Profile
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- CASE 2: Single profile assigned -->
        <div v-else-if="props.lawyer" class="space-y-6">
            <div class="text-left border-b pb-2">
                <h2 class="font-serif text-xl font-bold text-[#0A1929]">Manage Attorney Profile Settings</h2>
                <p class="text-xs text-neutral-400">Modify your biographical records, availability state, and telephone coordinates.</p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Left panel: Updatable Profile Info Form -->
                <div class="space-y-6 lg:col-span-8 text-left">
                    <div class="rounded-2xl border border-black/5 bg-white p-6 shadow-xs">
                        <div class="flex items-center gap-3 border-b pb-3 mb-5">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl font-serif text-sm font-bold text-white shadow-inner"
                                :style="{ backgroundColor: props.lawyer.avatar_color }"
                            >
                                {{ props.lawyer.initials }}
                            </div>
                            <div>
                                <h3 class="font-bold text-neutral-800">{{ props.lawyer.name }}</h3>
                                <p class="text-[10px] text-neutral-400">{{ props.lawyer.title }} • {{ props.lawyer.firm }}</p>
                            </div>
                        </div>

                        <form @submit.prevent="submitProfileForm" class="space-y-5">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-neutral-700">Availability Status *</label>
                                <select
                                    v-model="form.availability"
                                    class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2.5 text-xs outline-none focus:border-[#C8961E]"
                                >
                                    <option value="available">Available (Accepting new clients)</option>
                                    <option value="busy">Busy (Limited availability)</option>
                                    <option value="unavailable">Unavailable (Not accepting cases)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold text-neutral-700">Public Email Address *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E]"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-[10px] text-red-500">
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold text-neutral-700">Telephone Number *</label>
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E]"
                                        required
                                    />
                                    <div v-if="form.errors.phone" class="mt-1 text-[10px] text-red-500">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold text-neutral-700">Website URL *</label>
                                    <input
                                        v-model="form.website"
                                        type="text"
                                        placeholder="e.g. b2b.legal"
                                        class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E]"
                                        required
                                    />
                                    <div v-if="form.errors.website" class="mt-1 text-[10px] text-red-500">
                                        {{ form.errors.website }}
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold text-neutral-700">LinkedIn Profile Link</label>
                                    <input
                                        v-model="form.linkedin"
                                        type="url"
                                        placeholder="e.g. https://linkedin.com/in/username"
                                        class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E]"
                                    />
                                    <div v-if="form.errors.linkedin" class="mt-1 text-[10px] text-red-500">
                                        {{ form.errors.linkedin }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-neutral-700">Professional Biography *</label>
                                <textarea
                                    v-model="form.bio"
                                    rows="8"
                                    class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E] font-sans leading-relaxed"
                                    required
                                ></textarea>
                                <div v-if="form.errors.bio" class="mt-1 text-[10px] text-red-500">
                                    {{ form.errors.bio }}
                                </div>
                            </div>

                            <div class="flex items-center justify-end border-t border-neutral-100 pt-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="cursor-pointer rounded-lg bg-[#0A1929] px-6 py-2.5 text-xs font-bold tracking-wider text-white uppercase transition-all hover:bg-[#1E3A54] disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Saving...' : 'Update My Profile' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right panel: Locked credentials panel -->
                <div class="space-y-6 text-left text-sm lg:col-span-4">
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-neutral-800">
                        <div class="flex items-start gap-2.5">
                            <Lock class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
                            <div>
                                <h4 class="text-xs font-bold tracking-wider text-[#7A4F00] uppercase">Locked Credentials</h4>
                                <p class="mt-1 text-[10px] leading-relaxed text-neutral-500">
                                    Performance analytics, ratings, board certifications, and legal clearances are verified by the registry administrator. These values are read-only and cannot be manually modified.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Performance Metrics -->
                    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                        <h4 class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase">
                            <Activity class="h-4 w-4 text-neutral-400" /> Performance Metrics
                        </h4>

                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Martindale Rating</span>
                                <span class="font-bold text-[#16161A]">{{ props.lawyer.rating.toFixed(1) }} ★</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Total Cases Handled</span>
                                <span class="font-bold text-neutral-800">{{ props.lawyer.cases_count }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Cases Won / Success</span>
                                <span class="font-semibold text-emerald-600">
                                    {{ props.lawyer.cases_won }} ({{
                                        props.lawyer.cases_count > 0
                                            ? Math.round((props.lawyer.cases_won / props.lawyer.cases_count) * 100)
                                            : 0
                                    }}%)
                                </span>
                            </div>
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Active Case Load</span>
                                <span class="font-semibold text-neutral-800">{{ props.lawyer.cases_active }} active</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Board Certification</span>
                                <span class="font-semibold text-neutral-800">
                                    {{ props.lawyer.is_certified ? 'Certified Specialist' : 'None' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400">Years Active</span>
                                <span class="font-semibold text-neutral-800">{{ props.lawyer.years_experience }} Years</span>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Clearances -->
                    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                        <h4 class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase">
                            <Shield class="h-4 w-4 text-neutral-400" /> Background Clearances
                        </h4>

                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between border-b border-neutral-100 pb-2">
                                <span class="text-xs text-neutral-400">Criminal Background</span>
                                <span class="text-xs font-bold text-emerald-600 uppercase">{{ props.lawyer.criminal_record }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400">Ethics Bar Record</span>
                                <span class="text-xs font-bold text-emerald-600 uppercase">{{ props.lawyer.bar_discipline }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Practice Areas -->
                    <div class="rounded-2xl border border-black/5 bg-white p-5 shadow-xs">
                        <h4 class="mb-3 flex items-center gap-1.5 text-xs font-bold tracking-wider text-[#16161A] uppercase">
                            <Globe class="h-4 w-4 text-neutral-400" /> Practice Areas
                        </h4>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span
                                v-for="area in props.lawyer.practice_areas"
                                :key="area"
                                class="rounded border border-black/5 bg-[#F3EFE8] px-2.5 py-1 text-[10px] font-semibold text-[#16161A]"
                            >
                                {{ area }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-2xl border border-dashed border-neutral-200 bg-white p-12 text-center">
            <User class="mx-auto h-12 w-12 text-neutral-300" />
            <h3 class="mt-4 text-sm font-bold text-neutral-700">No Assigned Lawyer Profile</h3>
            <p class="mt-1 text-xs text-neutral-400">
                You do not have any lawyer directory profiles linked to your user account yet.
            </p>
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
