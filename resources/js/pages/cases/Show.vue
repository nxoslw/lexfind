<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Scale,
    ArrowRight,
    User,
    Clock,
    FileText,
    Gavel,
    Users,
    Activity,
} from 'lucide-vue-next';

interface TimelineEvent {
    date: string;
    badge: string;
    badgeLabel: string;
    title: string;
    action: string;
    status: string;
    context?: string;
    contextClass?: string;
}

interface MotionRecord {
    date: string;
    motion: string;
    details: string;
    result: string;
    resultLabel: string;
}

interface PartyRecord {
    role: string;
    name: string;
    status: string;
    isWinner: boolean;
}

interface LegalIssue {
    num: number;
    title: string;
    desc: string;
}

interface DocumentRecord {
    name: string;
    date: string;
    type: string;
    typeLabel: string;
}

interface NextStepRecord {
    icon: string;
    title: string;
    desc: string;
}

interface Lawyer {
    id: number;
    slug: string;
    name: string;
    title: string;
    firm: string;
    city: string;
    state: string;
    avatar_color: string;
    initials: string;
    years_experience: number;
    cases_count: number;
    cases_won: number;
    rating: number;
}

interface CaseDetails {
    id: number;
    slug: string;
    lawyer_id: number;
    name: string;
    case_number: string;
    jurisdiction: string;
    type: string;
    type_label: string;
    status: string;
    year: number;
    court: string;
    won_party: string | null;
    motions_count: number;
    motion_success_rate: string | null;
    timeline: TimelineEvent[] | null;
    motions: MotionRecord[] | null;
    parties: PartyRecord[] | null;
    issues: LegalIssue[] | null;
    documents: DocumentRecord[] | null;
    summary: string;
    key_finding: string | null;
    rate_boxes: {
        fail?: string;
        win?: string;
        failSub?: string;
        winSub?: string;
    } | null;
    next_steps: NextStepRecord[] | null;
    lawyer?: Lawyer;
    lawyers?: (Lawyer & { pivot?: { outcome: string } })[] | null;
}

const props = defineProps<{
    case: CaseDetails;
}>();

const getTimelineBadgeClass = (status: string) => {
    switch (status) {
        case 'initiated':
            return 'bg-blue-50 border-blue-200 text-blue-700';
        case 'denied':
            return 'bg-red-50 border-red-200 text-red-700';
        case 'procedural':
            return 'bg-amber-50 border-amber-200 text-amber-700';
        case 'brief':
            return 'bg-purple-50 border-purple-200 text-purple-700';
        case 'oral':
            return 'bg-slate-50 border-slate-200 text-slate-700';
        case 'victory':
        case 'granted':
        case 'won':
            return 'bg-emerald-50 border-emerald-200 text-emerald-700';
        default:
            return 'bg-neutral-50 border-neutral-200 text-neutral-700';
    }
};

const getTimelineDotClass = (status: string) => {
    switch (status) {
        case 'initiated':
            return 'bg-blue-600';
        case 'denied':
            return 'bg-red-600';
        case 'procedural':
            return 'bg-amber-500';
        case 'brief':
            return 'bg-purple-600';
        case 'oral':
            return 'bg-slate-600';
        case 'victory':
        case 'granted':
        case 'won':
            return 'bg-emerald-600';
        default:
            return 'bg-neutral-400';
    }
};

const getMotionResultClass = (result: string) => {
    switch (result) {
        case 'granted':
        case 'accepted':
            return 'bg-emerald-50 border-emerald-100 text-emerald-700';
        case 'denied':
            return 'bg-red-50 border-red-100 text-red-700';
        case 'filed':
            return 'bg-blue-50 border-blue-100 text-blue-700';
        case 'argued':
            return 'bg-purple-50 border-purple-100 text-purple-700';
        default:
            return 'bg-neutral-50 border-neutral-100 text-neutral-600';
    }
};

const getDocIcon = (type: string) => {
    switch (type) {
        case 'motion':
            return '📋';
        case 'order':
            return '📜';
        case 'procedural':
            return '🔔';
        case 'brief':
            return '📑';
        case 'judgment':
            return '✅';
        default:
            return '📄';
    }
};
</script>

<template>
    <Head :title="`${props.case.name} — Case Detail`" />

    <div class="flex flex-1 flex-col bg-[#F3EFE8] font-sans text-[#16161A]">
        <!-- Case Hero Header -->
        <div
            class="relative overflow-hidden bg-[#0A1929] px-6 pt-12 pb-0 text-white md:px-12"
        >
            <div
                class="pointer-events-none absolute top-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-[#C8961E]/5 blur-3xl"
            ></div>

            <div class="relative z-10 mx-auto max-w-5xl text-left">
                <div class="pb-8">
                    <span
                        class="mb-3 inline-flex items-center rounded border border-[#C8961E]/22 bg-[#C8961E]/12 px-2.5 py-0.5 font-mono text-[10px] font-bold tracking-wider text-[#F0C96A] uppercase"
                    >
                        {{ props.case.case_number }}
                    </span>
                    <h1
                        class="mb-3 max-w-3xl font-serif text-2xl leading-tight font-bold text-white sm:text-4xl"
                    >
                        {{ props.case.name }}
                    </h1>
                    <p
                        class="mb-5 max-w-2xl text-xs leading-relaxed text-white/45 sm:text-sm"
                    >
                        {{ props.case.jurisdiction }} ·
                        {{ props.case.type_label }}
                    </p>

                    <div
                        class="flex flex-wrap gap-2.5 text-[10px] font-bold tracking-wider uppercase"
                    >
                        <span
                            :class="[
                                'flex items-center gap-1.5 rounded border px-2.5 py-1',
                                props.case.status === 'decided'
                                    ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                                    : props.case.status === 'active'
                                        ? 'border-sky-500/20 bg-sky-500/10 text-sky-400'
                                        : 'border-amber-500/20 bg-amber-500/10 text-amber-400',
                            ]"
                        >
                            <span
                                :class="[
                                    'h-1.5 w-1.5 rounded-full',
                                    props.case.status === 'decided'
                                        ? 'bg-emerald-400'
                                        : props.case.status === 'active'
                                            ? 'bg-sky-400'
                                            : 'bg-amber-400',
                                ]"
                            ></span>
                            Status: {{ props.case.status }}
                        </span>
                        <span
                            v-if="props.case.won_party"
                            class="rounded border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[#86EFAC]"
                        >
                            ✓ Victory
                        </span>
                        <span
                            class="rounded border border-white/8 bg-white/5 px-2.5 py-1 text-white/60"
                        >
                            🏛 {{ props.case.court }}
                        </span>
                        <span
                            class="rounded border border-white/8 bg-white/5 px-2.5 py-1 text-white/60"
                        >
                            {{ props.case.year }} Record
                        </span>
                    </div>
                </div>

                <!-- KPI Statistics strip -->
                <div
                    class="grid grid-cols-2 gap-px overflow-hidden rounded-t-xl border-t border-white/8 bg-white/5 sm:grid-cols-5"
                >
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p
                            class="font-serif text-lg font-bold text-emerald-400"
                        >
                            {{ props.case.status.toUpperCase() }}
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Final Outcome
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p
                            class="truncate px-1 font-serif text-sm font-bold text-white"
                            :title="props.case.won_party || 'Undetermined'"
                        >
                            {{
                                props.case.won_party
                                    ? props.case.won_party.includes('Client')
                                        ? 'Our Client'
                                        : 'Opposing Party'
                                    : 'N/A'
                            }}
                        </p>
                        <p
                            class="mt-1.5 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Winning side
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p class="font-serif text-lg font-bold text-white">
                            {{ props.case.motions_count }}
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Tracked Motions
                        </p>
                    </div>
                    <div class="bg-[#0A1929]/70 px-3 py-4 text-center">
                        <p class="font-serif text-lg font-bold text-[#F0C96A]">
                            {{ props.case.motion_success_rate || '100%' }}
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Motion Success Rate
                        </p>
                    </div>
                    <div
                        class="col-span-2 bg-[#0A1929]/70 px-3 py-4 text-center sm:col-span-1"
                    >
                        <p class="font-serif text-lg font-bold text-white">
                            {{
                                props.case.timeline
                                    ? props.case.timeline.length
                                    : 0
                            }}
                        </p>
                        <p
                            class="mt-1 text-[9px] tracking-wider text-white/30 uppercase"
                        >
                            Docket events
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Body -->
        <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-8 md:px-12">
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <!-- Left panel: Case Content -->
                <div class="space-y-6 lg:col-span-8">
                    <!-- Case Summary Card -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-3.5 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <FileText class="h-4 w-4 text-neutral-400" /> Case
                            Summary
                        </h4>
                        <p
                            class="font-sans text-sm leading-relaxed text-neutral-600"
                        >
                            {{ props.case.summary }}
                        </p>

                        <div
                            v-if="props.case.key_finding"
                            class="key-finding mt-4 rounded-xl border border-[#C8961E]/20 bg-[#FBF5E0] px-4 py-3 font-sans text-xs leading-relaxed text-neutral-700"
                        >
                            <strong class="font-bold text-[#7A4F00]"
                                >Key finding:</strong
                            >
                            {{ props.case.key_finding }}
                        </div>
                    </div>

                    <!-- Parties Grid -->
                    <div
                        v-if="
                            props.case.parties && props.case.parties.length > 0
                        "
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Users class="h-4 w-4 text-neutral-400" />
                            Litigation Parties
                        </h4>

                        <div
                            class="flex flex-col items-center gap-4 md:flex-row"
                        >
                            <!-- Party 1 -->
                            <div
                                :class="[
                                    'w-full flex-1 rounded-xl border p-4',
                                    props.case.parties[0].isWinner
                                        ? 'border-emerald-200 bg-emerald-50/50'
                                        : 'border-black/5 bg-neutral-50/30',
                                ]"
                            >
                                <div
                                    class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    {{ props.case.parties[0].role }}
                                </div>
                                <div
                                    class="mt-1 truncate text-sm font-bold text-neutral-800"
                                >
                                    {{ props.case.parties[0].name }}
                                </div>
                                <div class="mt-1 text-[10px] text-neutral-400">
                                    {{
                                        props.case.parties[0].role.includes(
                                            'Appellant',
                                        ) ||
                                        props.case.parties[0].role.includes(
                                            'Defendant',
                                        )
                                            ? 'Defended by Counsel'
                                            : 'Opposing party'
                                    }}
                                </div>
                                <span
                                    :class="[
                                        'mt-3 inline-block rounded-full border px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                        props.case.parties[0].isWinner
                                            ? 'border-emerald-200 bg-emerald-100/65 text-emerald-800'
                                            : 'border-red-100 bg-red-50 text-red-700',
                                    ]"
                                >
                                    {{ props.case.parties[0].status }}
                                </span>
                            </div>

                            <div
                                class="shrink-0 rounded-full border border-black/5 bg-neutral-100 px-3 py-1 text-xs font-bold text-neutral-300"
                            >
                                VS
                            </div>

                            <!-- Party 2 -->
                            <div
                                v-if="props.case.parties[1]"
                                :class="[
                                    'w-full flex-1 rounded-xl border p-4',
                                    props.case.parties[1].isWinner
                                        ? 'border-emerald-200 bg-emerald-50/50'
                                        : 'border-black/5 bg-neutral-50/30',
                                ]"
                            >
                                <div
                                    class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                                >
                                    {{ props.case.parties[1].role }}
                                </div>
                                <div
                                    class="mt-1 truncate text-sm font-bold text-neutral-800"
                                >
                                    {{ props.case.parties[1].name }}
                                </div>
                                <div class="mt-1 text-[10px] text-neutral-400">
                                    <template v-if="props.case.lawyers && props.case.lawyers.length > 0">
                                        Represented by
                                        <span class="font-semibold text-[#0F3E8F]">
                                            {{ props.case.lawyers.map(l => l.name).join(', ') }}
                                        </span>
                                    </template>
                                    <template v-else-if="props.case.lawyer">
                                        Represented by
                                        <span
                                            class="font-semibold text-[#0F3E8F]"
                                            >{{ props.case.lawyer.name }}</span
                                        >
                                    </template>
                                    <template v-else>
                                        Represented by Counsel
                                    </template>
                                </div>
                                <span
                                    :class="[
                                        'mt-3 inline-block rounded-full border px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                        props.case.parties[1].isWinner
                                            ? 'border-emerald-200 bg-emerald-100/65 text-emerald-800'
                                            : 'border-red-100 bg-red-50 text-red-700',
                                    ]"
                                >
                                    {{ props.case.parties[1].status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Docket Timeline History -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-5 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Clock class="h-4 w-4 text-neutral-400" />
                            Chronological Docket History
                        </h4>

                        <div
                            v-if="
                                !props.case.timeline ||
                                props.case.timeline.length === 0
                            "
                            class="py-8 text-center text-xs text-neutral-400"
                        >
                            Archived docket timeline history is currently
                            unavailable.
                        </div>

                        <div
                            v-else
                            class="relative ml-3.5 space-y-7 border-l-2 border-neutral-100 pl-5"
                        >
                            <div
                                v-for="(event, index) in props.case.timeline"
                                :key="index"
                                class="relative"
                            >
                                <!-- Dot -->
                                <div
                                    :class="[
                                        'absolute top-1.5 -left-[27px] h-3 w-3 rounded-full border border-white ring-4 ring-[#F3EFE8]',
                                        getTimelineDotClass(event.status),
                                    ]"
                                ></div>

                                <!-- Content -->
                                <div class="space-y-1">
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="text-xs font-bold text-neutral-400"
                                            >{{ event.date }}</span
                                        >
                                        <span
                                            :class="[
                                                'rounded-full border px-2 py-0.5 text-[8px] font-bold tracking-wider uppercase',
                                                getTimelineBadgeClass(
                                                    event.status,
                                                ),
                                            ]"
                                        >
                                            {{ event.badgeLabel }}
                                        </span>
                                    </div>
                                    <h5
                                        class="text-sm font-bold text-neutral-800"
                                    >
                                        {{ event.title }}
                                    </h5>
                                    <p
                                        class="font-sans text-xs leading-relaxed text-neutral-500"
                                    >
                                        {{ event.action }}
                                    </p>

                                    <div
                                        v-if="event.context"
                                        :class="[
                                            'mt-1 inline-block rounded-lg border px-2.5 py-1 text-[10px] font-semibold italic',
                                            event.status === 'denied'
                                                ? 'border-red-100 bg-red-50 text-red-600'
                                                : 'border-emerald-100 bg-emerald-50 text-emerald-600',
                                        ]"
                                    >
                                        ↳ {{ event.context }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motion Tracker -->
                    <div
                        v-if="
                            props.case.motions && props.case.motions.length > 0
                        "
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Gavel class="h-4 w-4 text-neutral-400" /> Motion
                            Tracker
                        </h4>

                        <!-- Rate box grids if present -->
                        <div
                            v-if="props.case.rate_boxes"
                            class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2"
                        >
                            <div
                                class="flex items-center justify-between rounded-xl border border-red-100 bg-red-50/50 p-3.5"
                            >
                                <div class="text-left">
                                    <div
                                        class="text-[9px] font-bold tracking-wider text-red-500 uppercase"
                                    >
                                        Trial Result
                                    </div>
                                    <div class="mt-0.5 text-xs text-red-700/60">
                                        {{
                                            props.case.rate_boxes.failSub ||
                                            'at circuit level'
                                        }}
                                    </div>
                                </div>
                                <div
                                    class="font-serif text-2xl font-bold text-red-700"
                                >
                                    {{ props.case.rate_boxes.fail || '0%' }}
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50/50 p-3.5"
                            >
                                <div class="text-left">
                                    <div
                                        class="text-[9px] font-bold tracking-wider text-emerald-600 uppercase"
                                    >
                                        Appellate Result
                                    </div>
                                    <div
                                        class="mt-0.5 text-xs text-emerald-700/60"
                                    >
                                        {{
                                            props.case.rate_boxes.winSub ||
                                            'on appeal'
                                        }}
                                    </div>
                                </div>
                                <div
                                    class="font-serif text-2xl font-bold text-emerald-700"
                                >
                                    {{ props.case.rate_boxes.win || '100%' }}
                                </div>
                            </div>
                        </div>

                        <!-- Motions Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr
                                        class="border-b border-neutral-100 text-[9px] tracking-wider text-neutral-400 uppercase"
                                    >
                                        <th class="w-1/2 py-2.5">
                                            Motion / Filing Action
                                        </th>
                                        <th class="py-2.5">Filed Date</th>
                                        <th class="py-2.5 text-right">
                                            Judicial Ruling
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-50">
                                    <tr
                                        v-for="(m, mi) in props.case.motions"
                                        :key="mi"
                                        class="hover:bg-neutral-50/50"
                                    >
                                        <td class="py-3 pr-4">
                                            <div
                                                class="leading-snug font-bold text-neutral-800"
                                            >
                                                {{ m.motion }}
                                            </div>
                                            <div
                                                class="mt-0.5 text-[10px] leading-snug text-neutral-400"
                                            >
                                                {{ m.details }}
                                            </div>
                                        </td>
                                        <td
                                            class="py-3 font-mono text-neutral-500"
                                        >
                                            {{ m.date }}
                                        </td>
                                        <td class="py-3 text-right">
                                            <span
                                                :class="[
                                                    'inline-block rounded border px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                                    getMotionResultClass(
                                                        m.result,
                                                    ),
                                                ]"
                                            >
                                                {{ m.resultLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Core Legal Issues -->
                    <div
                        v-if="props.case.issues && props.case.issues.length > 0"
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Scale class="h-4 w-4 text-neutral-400" /> Core
                            Legal Issues
                        </h4>

                        <div class="space-y-4">
                            <div
                                v-for="issue in props.case.issues"
                                :key="issue.num"
                                class="flex items-start gap-4 rounded-xl border border-black/5 bg-[#F3EFE8]/40 p-4"
                            >
                                <div
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#0A1929] text-xs font-bold text-[#DBA93E]"
                                >
                                    {{ issue.num }}
                                </div>
                                <div class="min-w-0 text-left">
                                    <h5
                                        class="text-xs font-bold text-neutral-800"
                                    >
                                        {{ issue.title }}
                                    </h5>
                                    <p
                                        class="mt-1 text-xs leading-relaxed text-neutral-500"
                                    >
                                        {{ issue.desc }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents on Record -->
                    <div
                        v-if="
                            props.case.documents &&
                            props.case.documents.length > 0
                        "
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-3.5 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <FileText class="h-4 w-4 text-neutral-400" /> Court
                            Documents on Record
                        </h4>

                        <div class="divide-y divide-neutral-50">
                            <div
                                v-for="(doc, di) in props.case.documents"
                                :key="di"
                                class="flex cursor-pointer items-center justify-between rounded-xl px-2 py-3 transition-all hover:bg-neutral-50/50"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-black/5 bg-neutral-100 text-sm"
                                    >
                                        {{ getDocIcon(doc.type) }}
                                    </span>
                                    <div class="min-w-0 text-left">
                                        <div
                                            class="truncate text-xs leading-snug font-bold text-neutral-800"
                                        >
                                            {{ doc.name }}
                                        </div>
                                        <div
                                            class="mt-0.5 text-[10px] leading-snug text-neutral-400"
                                        >
                                            Filed {{ doc.date }}
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="rounded border border-black/5 bg-neutral-100 px-2 py-0.5 font-mono text-[9px] font-bold tracking-wider text-neutral-500 uppercase"
                                >
                                    {{ doc.typeLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Next steps if active -->
                    <div
                        v-if="
                            props.case.next_steps &&
                            props.case.next_steps.length > 0
                        "
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Activity class="h-4 w-4 text-neutral-400" />
                            Post-Opinion Actions & Next Steps
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                v-for="(step, si) in props.case.next_steps"
                                :key="si"
                                class="flex items-start gap-3 rounded-xl border border-indigo-100/50 bg-indigo-50/30 p-4"
                            >
                                <span
                                    class="shrink-0 rounded-lg border border-indigo-100 bg-white p-1.5 text-lg shadow-sm"
                                    >{{ step.icon }}</span
                                >
                                <div class="min-w-0 text-left">
                                    <h5
                                        class="text-xs font-bold text-[#0F3E8F]"
                                    >
                                        {{ step.title }}
                                    </h5>
                                    <p
                                        class="mt-1 text-xs leading-relaxed text-neutral-500"
                                    >
                                        {{ step.desc }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right sidebar: Case Details and Attorney Card -->
                <div class="space-y-6 text-left text-sm lg:col-span-4">
                    <!-- Outcome Box -->
                    <div
                        class="rounded-2xl border border-[#C8961E]/30 bg-gradient-to-br from-[#0A1929] to-[#0F2234] p-5 text-white shadow-lg"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl border border-[#C8961E]/22 bg-[#C8961E]/12 text-2xl"
                        >
                            🏆
                        </div>
                        <h4
                            class="mb-2 font-serif text-sm font-bold text-white"
                        >
                            {{
                                props.case.status === 'decided'
                                    ? 'Case Decided'
                                    : props.case.status === 'settled'
                                      ? 'Case Settled'
                                      : 'Case Active'
                            }}
                        </h4>
                        <p class="mb-4 text-xs leading-relaxed text-white/60">
                            {{
                                props.case.status === 'decided'
                                    ? (props.case.won_party ? `Case has been decided. Winning party: ${props.case.won_party}.` : 'Case has been decided.')
                                    : props.case.status === 'settled'
                                      ? 'Case has been settled by mutual agreement.'
                                      : 'Case is currently actively litigated and awaiting scheduling or final disposition.'
                            }}
                        </p>
                        <span
                            class="inline-block rounded-full bg-[#C8961E] px-3 py-1 text-[9px] font-bold tracking-wider text-[#0A1929] uppercase"
                        >
                            {{
                                props.case.status === 'decided'
                                    ? 'Resolved'
                                    : props.case.status === 'settled'
                                      ? 'Settled'
                                      : 'Active'
                            }}
                        </span>
                    </div>

                    <!-- Facts checklist Card -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Scale class="h-4 w-4 text-neutral-400" /> Case
                            Details
                        </h4>

                        <div class="space-y-3.5">
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Docket Number</span
                                >
                                <span
                                    class="font-mono font-bold text-[#16161A]"
                                    >{{ props.case.case_number }}</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Court Venue</span
                                >
                                <span
                                    class="text-right font-semibold text-neutral-800"
                                    >{{ props.case.court }}</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Litigation Type</span
                                >
                                <span
                                    class="max-w-[170px] truncate text-right font-semibold text-neutral-800"
                                    :title="props.case.type_label"
                                    >{{ props.case.type_label }}</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Final Decision Year</span
                                >
                                <span class="font-semibold text-neutral-800">{{
                                    props.case.year
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Motions success</span
                                >
                                <span class="font-bold text-emerald-600">{{
                                    props.case.motion_success_rate || '100%'
                                }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400"
                                    >Case Status</span
                                >
                                <span
                                    :class="[
                                        'rounded-full border px-2 py-0.5 text-[10px] font-semibold tracking-wider uppercase',
                                        props.case.status === 'decided'
                                            ? 'border-emerald-200 bg-emerald-100/70 text-emerald-800'
                                            : props.case.status === 'active'
                                                ? 'border-sky-200 bg-sky-100/70 text-sky-800'
                                                : 'border-amber-200 bg-amber-100/70 text-amber-800',
                                    ]"
                                >
                                    {{ props.case.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Attorneys Card -->
                    <div
                        v-if="(props.case.lawyers && props.case.lawyers.length > 0) || props.case.lawyer"
                        class="rounded-2xl border border-black/5 bg-white p-5 text-left shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <User class="h-4 w-4 text-neutral-400" /> Attorneys of Record
                        </h4>

                        <div class="flex flex-col gap-3">
                            <template v-if="props.case.lawyers && props.case.lawyers.length > 0">
                                <Link
                                    v-for="l in props.case.lawyers"
                                    :key="l.id"
                                    :href="`/lawyers/${l.slug}`"
                                    class="group block rounded-xl border border-black/5 bg-neutral-50/50 p-4 transition-all hover:border-[#C8961E]/30 hover:bg-[#F3EFE8]/30"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg font-serif text-base font-bold text-white shadow-sm"
                                            :style="{
                                                backgroundColor: l.avatar_color,
                                            }"
                                        >
                                            {{ l.initials }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h5
                                                class="truncate text-xs leading-snug font-bold text-neutral-800 transition-all group-hover:text-[#DBA93E] flex items-center gap-1.5"
                                            >
                                                {{ l.name }}
                                                <span class="rounded bg-neutral-100 px-1 py-0.2 font-mono text-[8px] font-semibold text-neutral-600 uppercase">
                                                    {{ l.pivot?.outcome ?? props.case.status }}
                                                </span>
                                            </h5>
                                            <p
                                                class="mt-0.5 truncate text-[10px] leading-snug text-neutral-400"
                                            >
                                                {{ l.firm }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-4 grid grid-cols-2 gap-2 border-t border-neutral-100 pt-3.5 text-center"
                                    >
                                        <div
                                            class="rounded-lg border border-black/5 bg-white px-1.5 py-1"
                                        >
                                            <div
                                                class="font-serif text-xs font-bold text-emerald-600"
                                            >
                                                {{
                                                    l.cases_count > 0
                                                        ? Math.round(
                                                              (l.cases_won /
                                                                  l.cases_count) *
                                                                  100,
                                                          )
                                                        : 0
                                                }}%
                                            </div>
                                            <div
                                                class="text-[8px] tracking-wider text-neutral-400 uppercase"
                                            >
                                                Win Rate
                                            </div>
                                        </div>
                                        <div
                                            class="rounded-lg border border-black/5 bg-white px-1.5 py-1"
                                        >
                                            <div
                                                class="font-serif text-xs font-bold text-neutral-800"
                                            >
                                                {{ l.years_experience }} Yrs
                                            </div>
                                            <div
                                                class="text-[8px] tracking-wider text-neutral-400 uppercase"
                                            >
                                                Experience
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-3 flex items-center justify-center gap-1 text-center text-[10px] font-bold text-[#0F3E8F] transition-all group-hover:translate-x-1"
                                    >
                                        View Attorney Profile
                                        <ArrowRight class="h-3 w-3" />
                                    </div>
                                </Link>
                            </template>
                            <template v-else-if="props.case.lawyer">
                                <Link
                                    :href="`/lawyers/${props.case.lawyer.slug}`"
                                    class="group block rounded-xl border border-black/5 bg-neutral-50/50 p-4 transition-all hover:border-[#C8961E]/30 hover:bg-[#F3EFE8]/30"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg font-serif text-base font-bold text-white shadow-sm"
                                            :style="{
                                                backgroundColor:
                                                    props.case.lawyer.avatar_color,
                                            }"
                                        >
                                            {{ props.case.lawyer.initials }}
                                        </div>
                                        <div class="min-w-0">
                                            <h5
                                                class="truncate text-xs leading-snug font-bold text-neutral-800 transition-all group-hover:text-[#DBA93E]"
                                            >
                                                {{ props.case.lawyer.name }}
                                            </h5>
                                            <p
                                                class="mt-0.5 truncate text-[10px] leading-snug text-neutral-400"
                                            >
                                                {{ props.case.lawyer.firm }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-4 grid grid-cols-2 gap-2 border-t border-neutral-100 pt-3.5 text-center"
                                    >
                                        <div
                                            class="rounded-lg border border-black/5 bg-white px-1.5 py-1"
                                        >
                                            <div
                                                class="font-serif text-xs font-bold text-emerald-600"
                                            >
                                                {{
                                                    props.case.lawyer.cases_count > 0
                                                        ? Math.round(
                                                              (props.case.lawyer
                                                                  .cases_won /
                                                                  props.case.lawyer
                                                                      .cases_count) *
                                                                  100,
                                                          )
                                                        : 0
                                                }}%
                                            </div>
                                            <div
                                                class="text-[8px] tracking-wider text-neutral-400 uppercase"
                                            >
                                                Win Rate
                                            </div>
                                        </div>
                                        <div
                                            class="rounded-lg border border-black/5 bg-white px-1.5 py-1"
                                        >
                                            <div
                                                class="font-serif text-xs font-bold text-neutral-800"
                                            >
                                                {{ props.case.lawyer.years_experience }} Yrs
                                            </div>
                                            <div
                                                class="text-[8px] tracking-wider text-neutral-400 uppercase"
                                            >
                                                Experience
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-3 flex items-center justify-center gap-1 text-center text-[10px] font-bold text-[#0F3E8F] transition-all group-hover:translate-x-1"
                                    >
                                        View Attorney Profile
                                        <ArrowRight class="h-3 w-3" />
                                    </div>
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
