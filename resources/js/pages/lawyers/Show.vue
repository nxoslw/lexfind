<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Scale,
    Search,
    Shield,
    Star,
    Calendar,
    MessageSquare,
    ArrowRight,
    Check,
    X,
    User,
    ChevronDown,
    LogOut,
    Sliders,
    Award,
    Mail,
    Globe,
    Phone,
    Linkedin,
    Plus,
    Lock,
    Unlock,
    Activity,
    SlidersHorizontal,
    Briefcase,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface LawyerCase {
    id: number;
    slug: string;
    name: string;
    case_number: string;
    jurisdiction: string;
    type: string;
    type_label: string;
    status: string;
    year: number;
    court: string;
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
    trial_style: string | null;
    peer_reviews: {
        rating: string;
        source: string;
        quote: string;
        author: string;
    } | null;
    recent_activity: Array<{
        date: string;
        title: string;
        desc: string;
    }> | null;
    practice_areas: string[];
    trial_style_details: {
        approach?: string;
        forensics?: string;
        global?: string;
    } | null;
    cases: LawyerCase[];
}

const props = defineProps<{
    lawyer: Lawyer;
}>();

const page = usePage();

// Tabs state
const activeTab = ref<
    'overview' | 'cases' | 'performance' | 'conduct' | 'style' | 'reviews'
>('overview');

// Cases filters
const caseTypeFilter = ref('all');
const caseStatusFilter = ref('all');

// Pro mode state
const isProUnlocked = ref(false);
const togglePro = () => {
    isProUnlocked.value = !isProUnlocked.value;
};

// Filter cases computed-like function
const getFilteredCases = () => {
    return props.lawyer.cases.filter((c) => {
        if (caseTypeFilter.value !== 'all' && c.type !== caseTypeFilter.value) {
            return false;
        }

        if (
            caseStatusFilter.value !== 'all' &&
            c.status !== caseStatusFilter.value
        ) {
            return false;
        }

        return true;
    });
};

// Navigation and dropdown states removed (handled by PublicLayout)

const saveToWatchlist = () => {
    // Client mock interaction
    alert('Attorney saved to watchlist!');
};
</script>

<template>
    <Head :title="`${lawyer.name} — Profile Detail`" />

    <div class="flex flex-1 flex-col bg-[#F3EFE8] font-sans text-[#16161A]">
        <!-- Profile Hero Header -->
        <div
            class="relative overflow-hidden bg-[#0A1929] px-6 pt-12 pb-0 text-white md:px-12"
        >
            <div
                class="pointer-events-none absolute top-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-[#C8961E]/5 blur-3xl"
            ></div>

            <div class="relative z-10 mx-auto max-w-5xl">
                <div
                    class="flex flex-col items-start justify-between gap-6 pb-8 md:flex-row"
                >
                    <!-- Left: Profile basics -->
                    <div class="flex flex-col items-start gap-5 sm:flex-row">
                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-[#C8961E]/20 font-serif text-3xl font-bold text-white shadow-lg"
                            :style="{ backgroundColor: lawyer.avatar_color }"
                        >
                            {{ lawyer.initials }}
                        </div>
                        <div class="min-w-0 text-left">
                            <span
                                v-if="lawyer.is_certified"
                                class="mb-2 inline-flex items-center gap-1 rounded-full border border-[#C8961E]/22 bg-[#C8961E]/12 px-2.5 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                            >
                                ✓ Board Certified — Business Litigation
                            </span>
                            <h1
                                class="mb-1.5 font-serif text-2xl leading-snug font-bold text-white sm:text-3xl"
                            >
                                {{ lawyer.name }}
                            </h1>
                            <p
                                class="mb-4 text-xs leading-relaxed text-white/45"
                            >
                                {{ lawyer.title }} · {{ lawyer.firm }} ·
                                {{ lawyer.city }}, {{ lawyer.state }}
                            </p>

                            <div
                                class="flex flex-wrap gap-2 text-[10px] font-bold tracking-wider text-white/50 uppercase"
                            >
                                <span
                                    class="rounded border border-white/8 bg-white/5 px-2.5 py-1"
                                    >Chambers USA Ranked</span
                                >
                                <span
                                    class="rounded border border-white/8 bg-white/5 px-2.5 py-1"
                                    >Legal 500 Listed</span
                                >
                                <span
                                    class="rounded border border-white/8 bg-white/5 px-2.5 py-1"
                                    >{{ lawyer.state }} Bar</span
                                >
                                <span
                                    class="rounded border border-white/8 bg-white/5 px-2.5 py-1"
                                    >{{ lawyer.years_experience }} yrs Exp</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Right Action buttons -->
                    <div
                        class="flex shrink-0 gap-2.5 self-stretch md:self-auto"
                    >
                        <a
                            :href="`mailto:${lawyer.email}`"
                            class="flex-1 rounded-xl bg-[#C8961E] px-5 py-2.5 text-center text-xs font-bold tracking-wider text-[#0A1929] uppercase shadow-md transition-all hover:bg-[#DBA93E] md:flex-initial"
                        >
                            Contact Attorney
                        </a>
                        <!-- <button
                            @click="saveToWatchlist"
                            class="rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-xs font-semibold tracking-wider uppercase transition-all hover:bg-white/10"
                        >
                            Save
                        </button> -->
                    </div>
                </div>

                <!-- KPI statistics strip -->
                <div
                    class="grid grid-cols-2 gap-px overflow-hidden rounded-t-xl border-t border-white/8 bg-white/5 sm:grid-cols-5"
                >
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p class="font-serif text-lg font-bold text-[#F0C96A]">
                            {{
                                lawyer.cases_count > 0
                                    ? Math.round(
                                          (lawyer.cases_won /
                                              lawyer.cases_count) *
                                              100,
                                      )
                                    : 0
                            }}%
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Win Rate
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p class="font-serif text-lg font-bold text-white">
                            {{ lawyer.cases_count.toLocaleString() }}+
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Cases Handled
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p
                            class="font-serif text-lg font-bold text-emerald-400"
                        >
                            {{ lawyer.cases_won.toLocaleString() }}+
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Cases Won
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p class="font-serif text-lg font-bold text-sky-300">
                            {{ lawyer.financial_recovery || '$50M+' }}
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Recovered
                        </p>
                    </div>
                    <div
                        class="col-span-2 bg-[#0A1929]/70 px-3 py-4 text-center sm:col-span-1"
                    >
                        <p class="font-serif text-lg font-bold text-white">
                            {{ lawyer.rating.toFixed(1) }} ★
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Martindale Rating
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Body -->
        <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-8 md:px-12">
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <!-- Left panel: Details content -->
                <div class="space-y-6 lg:col-span-8">
                    <!-- Pro Intelligence Toggle Card -->
                    <div
                        class="flex items-center justify-between rounded-2xl border border-[#C8961E]/20 bg-gradient-to-r from-[#0A1929] to-[#0F2234] p-5 text-white shadow-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl text-[#F0C96A]">⚡</span>
                            <div class="text-left">
                                <h4
                                    class="text-xs font-bold tracking-wider text-[#DBA93E] uppercase"
                                >
                                    Pro Intelligence Mode
                                </h4>
                                <p class="text-[11px] text-white/40">
                                    Unlock opponent analytics, judge patterns &
                                    motion history
                                </p>
                            </div>
                        </div>
                        <button
                            @click="togglePro"
                            :class="[
                                'cursor-pointer rounded-lg border px-4 py-2 text-xs font-bold transition-all',
                                isProUnlocked
                                    ? 'border-[#C8961E]/20 bg-[#C8961E]/15 text-[#DBA93E]'
                                    : 'border-[#C8961E] bg-[#C8961E] text-[#0A1929] hover:bg-[#DBA93E]',
                            ]"
                        >
                            {{
                                isProUnlocked
                                    ? '✓ Pro Mode Active'
                                    : 'Unlock Pro View'
                            }}
                        </button>
                    </div>

                    <!-- Unlocked Pro Sections -->
                    <div
                        v-if="isProUnlocked"
                        class="animate-[fadeUp_0.18s_ease-out] space-y-6"
                    >
                        <!-- Pro KPIs -->
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div
                                class="rounded-xl border border-white/5 bg-[#0A1929] p-4 text-center text-white"
                            >
                                <p
                                    class="font-serif text-xl font-bold text-[#86EFAC]"
                                >
                                    84%
                                </p>
                                <p
                                    class="mt-1 text-[9px] tracking-wider text-white/35 uppercase"
                                >
                                    Jury Trial Win %
                                </p>
                            </div>
                            <div
                                class="rounded-xl border border-white/5 bg-[#0A1929] p-4 text-center text-white"
                            >
                                <p
                                    class="font-serif text-xl font-bold text-[#DBA93E]"
                                >
                                    $2.1M
                                </p>
                                <p
                                    class="mt-1 text-[9px] tracking-wider text-white/35 uppercase"
                                >
                                    Avg. Settlement
                                </p>
                            </div>
                            <div
                                class="rounded-xl border border-white/5 bg-[#0A1929] p-4 text-center text-white"
                            >
                                <p
                                    class="font-serif text-xl font-bold text-[#93B8F8]"
                                >
                                    {{ lawyer.cases_active }}
                                </p>
                                <p
                                    class="mt-1 text-[9px] tracking-wider text-white/35 uppercase"
                                >
                                    Active Cases
                                </p>
                            </div>
                            <div
                                class="rounded-xl border border-white/5 bg-[#0A1929] p-4 text-center text-white"
                            >
                                <p
                                    class="font-serif text-xl font-bold text-white"
                                >
                                    92%
                                </p>
                                <p
                                    class="mt-1 text-[9px] tracking-wider text-white/35 uppercase"
                                >
                                    Brief Success
                                </p>
                            </div>
                        </div>

                        <!-- Case Type Distribution Chart -->
                        <div
                            class="rounded-2xl border border-black/5 bg-white p-5 text-left shadow-sm"
                        >
                            <h4
                                class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                            >
                                <Activity class="h-4 w-4 text-neutral-400" />
                                Case Type Distribution (Pro)
                            </h4>
                            <div class="space-y-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-28 text-right font-medium text-neutral-600"
                                        >Business Litigation</span
                                    >
                                    <div
                                        class="h-2 flex-1 overflow-hidden rounded-full bg-neutral-100"
                                    >
                                        <div
                                            class="h-full bg-gradient-to-r from-[#0A1929] to-[#1E3A54]"
                                            style="width: 72%"
                                        ></div>
                                    </div>
                                    <span class="w-8 font-mono text-neutral-500"
                                        >72%</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-28 text-right font-medium text-neutral-600"
                                        >Corporate / M&A</span
                                    >
                                    <div
                                        class="h-2 flex-1 overflow-hidden rounded-full bg-neutral-100"
                                    >
                                        <div
                                            class="h-full bg-gradient-to-r from-[#0A1929] to-[#1E3A54]"
                                            style="width: 55%"
                                        ></div>
                                    </div>
                                    <span class="w-8 font-mono text-neutral-500"
                                        >55%</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-28 text-right font-medium text-neutral-600"
                                        >Real Estate</span
                                    >
                                    <div
                                        class="h-2 flex-1 overflow-hidden rounded-full bg-neutral-100"
                                    >
                                        <div
                                            class="h-full bg-gradient-to-r from-[#0A1929] to-[#1E3A54]"
                                            style="width: 38%"
                                        ></div>
                                    </div>
                                    <span class="w-8 font-mono text-neutral-500"
                                        >38%</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Judge Win Rate Table -->
                        <div
                            class="rounded-2xl border border-black/5 bg-white p-5 text-left shadow-sm"
                        >
                            <h4
                                class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                            >
                                <Scale class="h-4 w-4 text-neutral-400" /> Judge
                                Frequency & Win Rate (Pro)
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr
                                            class="border-b border-neutral-100 text-[9px] tracking-wider text-neutral-400 uppercase"
                                        >
                                            <th class="py-2.5">Judge</th>
                                            <th class="py-2.5">Court</th>
                                            <th class="py-2.5 text-center">
                                                Cases
                                            </th>
                                            <th class="py-2.5 text-right">
                                                Win Rate
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-50">
                                        <tr class="hover:bg-neutral-50/50">
                                            <td
                                                class="py-3 font-semibold text-neutral-700"
                                            >
                                                Hon. Cecilia Altonaga
                                            </td>
                                            <td class="py-3 text-neutral-500">
                                                S.D. Fla.
                                            </td>
                                            <td
                                                class="py-3 text-center text-neutral-600"
                                            >
                                                14
                                            </td>
                                            <td class="py-3 text-right">
                                                <span
                                                    class="rounded-full bg-emerald-100 px-2.5 py-0.5 font-bold text-emerald-800"
                                                    >93%</span
                                                >
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-neutral-50/50">
                                            <td
                                                class="py-3 font-semibold text-neutral-700"
                                            >
                                                Hon. Robert Scola
                                            </td>
                                            <td class="py-3 text-neutral-500">
                                                S.D. Fla.
                                            </td>
                                            <td
                                                class="py-3 text-center text-neutral-600"
                                            >
                                                11
                                            </td>
                                            <td class="py-3 text-right">
                                                <span
                                                    class="rounded-full bg-emerald-100 px-2.5 py-0.5 font-bold text-emerald-800"
                                                    >91%</span
                                                >
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-neutral-50/50">
                                            <td
                                                class="py-3 font-semibold text-neutral-700"
                                            >
                                                Hon. Jennifer Bailey
                                            </td>
                                            <td class="py-3 text-neutral-500">
                                                Miami-Dade Cir.
                                            </td>
                                            <td
                                                class="py-3 text-center text-neutral-600"
                                            >
                                                9
                                            </td>
                                            <td class="py-3 text-right">
                                                <span
                                                    class="rounded-full bg-amber-100 px-2.5 py-0.5 font-bold text-amber-800"
                                                    >78%</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Buttons Navigation -->
                    <div
                        class="flex flex-wrap rounded-2xl border border-black/5 bg-white p-1 text-xs shadow-sm"
                    >
                        <button
                            @click="activeTab = 'overview'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'overview'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Overview
                        </button>
                        <button
                            @click="activeTab = 'cases'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'cases'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Cases
                        </button>
                        <button
                            @click="activeTab = 'performance'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'performance'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Performance
                        </button>
                        <button
                            @click="activeTab = 'conduct'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'conduct'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Conduct
                        </button>
                        <button
                            @click="activeTab = 'style'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'style'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Trial Style
                        </button>
                        <button
                            @click="activeTab = 'reviews'"
                            :class="[
                                'min-w-[70px] flex-1 cursor-pointer rounded-xl py-2.5 text-center font-bold transition-all',
                                activeTab === 'reviews'
                                    ? 'bg-[#0A1929] text-[#DBA93E]'
                                    : 'text-neutral-400 hover:bg-neutral-50 hover:text-neutral-800',
                            ]"
                        >
                            Reviews
                        </button>
                    </div>

                    <!-- Tab panels -->
                    <div class="space-y-6">
                        <!-- Overview Tab -->
                        <div
                            v-if="activeTab === 'overview'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-6"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <User class="h-4 w-4 text-neutral-400" />
                                    Biography
                                </h4>
                                <p
                                    class="font-sans text-sm leading-relaxed text-neutral-600"
                                >
                                    {{ lawyer.bio }}
                                </p>
                            </div>

                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <Scale class="h-4 w-4 text-neutral-400" />
                                    Trial Style & Approach
                                </h4>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div
                                        class="rounded-xl border border-black/5 bg-[#F3EFE8]/50 p-4"
                                    >
                                        <h5
                                            class="mb-1.5 text-xs font-bold text-neutral-800"
                                        >
                                            🎯 Core Approach
                                        </h5>
                                        <p
                                            class="text-xs leading-relaxed text-neutral-500"
                                        >
                                            {{
                                                lawyer.trial_style_details
                                                    ?.approach ||
                                                'Masterful, surgical case preparation focusing on technical trial mechanics and rules of evidence.'
                                            }}
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-xl border border-black/5 bg-[#F3EFE8]/50 p-4"
                                    >
                                        <h5
                                            class="mb-1.5 text-xs font-bold text-neutral-800"
                                        >
                                            🔬 Forensics & Evidence
                                        </h5>
                                        <p
                                            class="text-xs leading-relaxed text-neutral-500"
                                        >
                                            {{
                                                lawyer.trial_style_details
                                                    ?.forensics ||
                                                lawyer.trial_style_details
                                                    ?.global ||
                                                'Highly fluent in parsing commercial ledgers and cross-examining witness validation.'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cases Tab -->
                        <div
                            v-if="activeTab === 'cases'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-4 text-left"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                            >
                                <div
                                    class="mb-4 flex flex-wrap items-center justify-between gap-4 border-b border-neutral-100 pb-4"
                                >
                                    <!-- Practice Chips inside Tab -->
                                    <div class="flex flex-wrap gap-1.5">
                                        <button
                                            @click="caseTypeFilter = 'all'"
                                            :class="[
                                                'cursor-pointer rounded-full border px-3 py-1 text-[10px] font-bold transition-all',
                                                caseTypeFilter === 'all'
                                                    ? 'border-[#0A1929] bg-[#0A1929] text-[#DBA93E]'
                                                    : 'border-transparent bg-neutral-100 text-neutral-500 hover:bg-neutral-200',
                                            ]"
                                        >
                                            All Cases
                                        </button>
                                        <button
                                            @click="caseTypeFilter = 'civil'"
                                            :class="[
                                                'cursor-pointer rounded-full border px-3 py-1 text-[10px] font-bold transition-all',
                                                caseTypeFilter === 'civil'
                                                    ? 'border-[#0A1929] bg-[#0A1929] text-[#DBA93E]'
                                                    : 'border-transparent bg-neutral-100 text-neutral-500 hover:bg-neutral-200',
                                            ]"
                                        >
                                            Civil
                                        </button>
                                        <button
                                            @click="
                                                caseTypeFilter = 'corporate'
                                            "
                                            :class="[
                                                'cursor-pointer rounded-full border px-3 py-1 text-[10px] font-bold transition-all',
                                                caseTypeFilter === 'corporate'
                                                    ? 'border-[#0A1929] bg-[#0A1929] text-[#DBA93E]'
                                                    : 'border-transparent bg-neutral-100 text-neutral-500 hover:bg-neutral-200',
                                            ]"
                                        >
                                            Corporate
                                        </button>
                                    </div>

                                    <!-- Status filter drop down -->
                                    <select
                                        v-model="caseStatusFilter"
                                        class="cursor-pointer rounded-lg border border-black/5 bg-[#F3EFE8] px-2.5 py-1 text-xs text-neutral-700 outline-none"
                                    >
                                        <option value="all">Status: All</option>
                                        <option value="won">Won</option>
                                        <option value="lost">Lost</option>
                                        <option value="active">Active</option>
                                        <option value="settled">Settled</option>
                                    </select>
                                </div>

                                <!-- Cases Timeline Rows -->
                                <div class="divide-y divide-neutral-100">
                                    <div
                                        v-if="getFilteredCases().length === 0"
                                        class="py-12 text-center text-xs text-neutral-400"
                                    >
                                        No case files match this filter
                                        combination.
                                    </div>
                                    <Link
                                        v-for="c in getFilteredCases()"
                                        :key="c.id"
                                        :href="`/cases/${c.slug}`"
                                        class="flex cursor-pointer items-center gap-3 rounded-xl px-2 py-3 transition-all hover:bg-neutral-50/50"
                                    >
                                        <div
                                            :class="[
                                                'h-2.5 w-2.5 shrink-0 rounded-full',
                                                c.status === 'decided'
                                                    ? 'bg-emerald-600'
                                                    : c.status === 'active'
                                                        ? 'bg-sky-500'
                                                        : 'bg-amber-600',
                                            ]"
                                        ></div>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="truncate text-xs leading-snug font-bold text-neutral-800"
                                            >
                                                {{ c.name }}
                                            </p>
                                            <p
                                                class="mt-0.5 text-[10px] leading-snug text-neutral-400"
                                            >
                                                {{ c.court }} · {{ c.year }}
                                            </p>
                                        </div>
                                        <span
                                            class="rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-indigo-700 uppercase"
                                            >{{ c.type_label }}</span
                                        >
                                        <span
                                            :class="[
                                                'rounded-full border px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                                c.status === 'decided'
                                                    ? 'border-emerald-100 bg-emerald-50 text-emerald-700'
                                                    : c.status === 'active'
                                                        ? 'border-sky-100 bg-sky-50 text-sky-700'
                                                        : 'border-amber-100 bg-amber-50 text-amber-700',
                                            ]"
                                        >
                                            {{ c.status }}
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Tab -->
                        <div
                            v-if="activeTab === 'performance'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-6 text-left"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <Activity
                                        class="h-4 w-4 text-neutral-400"
                                    />
                                    Trial Win Statistics
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="rounded-xl border border-black/5 bg-[#F3EFE8]/50 p-4 text-center"
                                    >
                                        <p
                                            class="font-serif text-3xl font-bold text-emerald-600"
                                        >
                                            {{
                                                lawyer.cases_count > 0
                                                    ? Math.round(
                                                          (lawyer.cases_won /
                                                              lawyer.cases_count) *
                                                              100,
                                                      )
                                                    : 0
                                            }}%
                                        </p>
                                        <p
                                            class="mt-1 text-[10px] tracking-wider text-neutral-400 uppercase"
                                        >
                                            Win Success Rate
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-xl border border-black/5 bg-[#F3EFE8]/50 p-4 text-center"
                                    >
                                        <p
                                            class="font-serif text-3xl font-bold text-neutral-800"
                                        >
                                            {{
                                                lawyer.cases_won.toLocaleString()
                                            }}+
                                        </p>
                                        <p
                                            class="mt-1 text-[10px] tracking-wider text-neutral-400 uppercase"
                                        >
                                            Favourable Settlements
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conduct Tab -->
                        <div
                            v-if="activeTab === 'conduct'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-6 text-left"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <Shield class="h-4 w-4 text-neutral-400" />
                                    Conduct & Ethics Verification
                                </h4>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div
                                        class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"
                                    >
                                        <span class="text-xl">🛡️</span>
                                        <div>
                                            <h5
                                                class="text-xs font-bold tracking-wider uppercase"
                                            >
                                                Criminal Records Check
                                            </h5>
                                            <p
                                                class="mt-0.5 text-[10px] font-semibold text-emerald-600 uppercase"
                                            >
                                                Status:
                                                {{ lawyer.criminal_record }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"
                                    >
                                        <span class="text-xl">⚖️</span>
                                        <div>
                                            <h5
                                                class="text-xs font-bold tracking-wider uppercase"
                                            >
                                                Professional Bar Discipline
                                            </h5>
                                            <p
                                                class="mt-0.5 text-[10px] font-semibold text-emerald-600 uppercase"
                                            >
                                                Status:
                                                {{ lawyer.bar_discipline }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trial Style Tab -->
                        <div
                            v-if="activeTab === 'style'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-6 text-left"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <SlidersHorizontal
                                        class="h-4 w-4 text-neutral-400"
                                    />
                                    Court Comportment & Strategy
                                </h4>
                                <p
                                    class="text-sm leading-relaxed text-neutral-600"
                                >
                                    {{
                                        lawyer.trial_style ||
                                        'Details regarding courtroom behaviors and witness cross-examination templates.'
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div
                            v-if="activeTab === 'reviews'"
                            class="animate-[fadeUp_0.15s_ease-out] space-y-6 text-left"
                        >
                            <div
                                class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    <Star class="h-4 w-4 text-neutral-400" />
                                    Peer Reviews
                                </h4>
                                <div class="mb-2 flex items-baseline gap-2">
                                    <span
                                        class="font-serif text-3xl font-bold text-neutral-800"
                                        >{{
                                            lawyer.peer_reviews?.rating || '5.0'
                                        }}</span
                                    >
                                    <span
                                        class="text-xs font-medium text-neutral-400"
                                        >/ 5.0</span
                                    >
                                </div>
                                <div class="mb-3 text-sm text-[#C8961E]">
                                    ★★★★★
                                </div>
                                <p class="text-xs text-neutral-400">
                                    {{
                                        lawyer.peer_reviews?.source ||
                                        'Martindale-Hubbell Directory Registry'
                                    }}
                                </p>
                                <blockquote
                                    class="mt-4 rounded-r-xl border-l-3 border-[#C8961E] bg-[#F3EFE8]/40 p-4 text-xs leading-relaxed text-neutral-600 italic"
                                >
                                    "{{
                                        lawyer.peer_reviews?.quote ||
                                        'Brian is an exceptionally ethical advocate.'
                                    }}"
                                    <span
                                        class="mt-2 block text-[10px] font-bold text-neutral-400 not-italic"
                                        >—
                                        {{
                                            lawyer.peer_reviews?.author ||
                                            'Chambers USA Directory Review'
                                        }}</span
                                    >
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right sidebar: Contacts and details card -->
                <div class="space-y-6 text-left text-sm lg:col-span-4">
                    <!-- Contacts Card -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Globe class="h-4 w-4 text-neutral-400" /> Contact
                            details
                        </h4>

                        <div class="space-y-3.5">
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-xs text-neutral-400"
                                    ><Mail class="h-3.5 w-3.5" /> Email</span
                                >
                                <a
                                    :href="`mailto:${lawyer.email}`"
                                    class="max-w-[170px] truncate font-medium text-blue-600 hover:underline"
                                    >{{ lawyer.email }}</a
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-xs text-neutral-400"
                                    ><Globe class="h-3.5 w-3.5" /> Website</span
                                >
                                <a
                                    :href="`https://${lawyer.website}`"
                                    target="_blank"
                                    class="max-w-[170px] truncate font-medium text-blue-600 hover:underline"
                                    >{{ lawyer.website }}</a
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-xs text-neutral-400"
                                    ><Phone class="h-3.5 w-3.5" />
                                    Telephone</span
                                >
                                <a
                                    :href="`tel:${lawyer.phone}`"
                                    class="truncate font-semibold text-neutral-800"
                                    >{{ lawyer.phone }}</a
                                >
                            </div>
                            <div
                                v-if="lawyer.linkedin"
                                class="flex items-center justify-between"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-xs text-neutral-400"
                                    ><Linkedin class="h-3.5 w-3.5" />
                                    LinkedIn</span
                                >
                                <a
                                    :href="lawyer.linkedin"
                                    target="_blank"
                                    class="max-w-[170px] truncate font-medium text-blue-600 hover:underline"
                                    >LinkedIn Profile</a
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Career Details -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Sliders class="h-4 w-4 text-neutral-400" /> Career
                            Profile
                        </h4>

                        <div class="space-y-3.5">
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Years Active</span
                                >
                                <span class="font-bold text-[#16161A]"
                                    >{{ lawyer.years_experience }} Years</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Fee Structure</span
                                >
                                <span
                                    class="max-w-[170px] truncate text-right font-semibold text-neutral-800"
                                    :title="lawyer.fee_structure || ''"
                                    >{{
                                        lawyer.fee_structure ||
                                        'Contact for rates'
                                    }}</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Bar Admission</span
                                >
                                <span
                                    class="text-right font-semibold text-neutral-800"
                                    >{{ lawyer.state }} Bar</span
                                >
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400"
                                    >Availability</span
                                >
                                <span
                                    class="rounded-full border border-emerald-200 bg-emerald-100/70 px-2 py-0.5 text-[10px] font-semibold tracking-wider text-[#1A5C3A] uppercase"
                                >
                                    {{ lawyer.availability }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
