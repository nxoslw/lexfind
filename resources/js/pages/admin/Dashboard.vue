<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    Users,
    Briefcase,
    ShieldAlert,
    CheckCircle,
    Clock,
    ChevronDown,
    ChevronUp,
    FileText,
    ArrowRight,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface AuditLog {
    id: number;
    user_id: number;
    user_name: string;
    lawyer_id: number;
    lawyer_name: string;
    action: string;
    details: string | Record<string, any>;
    created_at: string;
    lawyer?: { id: number; slug: string } | null;
}

interface Stats {
    total_lawyers: number;
    assigned_lawyers: number;
    unassigned_lawyers: number;
    total_users: number;
    privileged_users: number;
    banned_users: number;
}

const props = defineProps<{
    stats: Stats;
    auditLogs: AuditLog[];
}>();

const expandedLogs = ref<Record<number, boolean>>({});

const toggleLog = (id: number) => {
    expandedLogs.value[id] = !expandedLogs.value[id];
};

const formatDetails = (details: string | Record<string, any>) => {
    try {
        const parsed =
            typeof details === 'string' ? JSON.parse(details) : details;

        return parsed;
    } catch (e) {
        return details;
    }
};

const getActionColor = (action: string) => {
    switch (action) {
        case 'create':
            return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400';
        case 'update':
            return 'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400';
        case 'delete':
            return 'bg-red-500/10 border-red-500/20 text-red-600 dark:text-red-400';
        case 'assign':
            return 'bg-indigo-500/10 border-indigo-500/20 text-indigo-600 dark:text-indigo-400';
        case 'unassign':
            return 'bg-purple-500/10 border-purple-500/20 text-purple-600 dark:text-purple-400';
        default:
            return 'bg-neutral-500/10 border-neutral-500/20 text-neutral-600 dark:text-neutral-400';
    }
};

const formatDate = (dateStr: string) => {
    const d = new Date(dateStr);

    return d.toLocaleString(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="System Dashboard" />

    <div class="space-y-8">
        <!-- Dashboard Welcome Summary -->
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0A1929] to-[#1E3A54] p-6 text-white shadow-xl lg:p-8"
        >
            <div
                class="bg-grid-white/[0.02] absolute inset-0 bg-[size:20px_20px]"
            ></div>
            <div class="relative z-10 space-y-2">
                <span
                    class="rounded border border-[#C8961E]/30 bg-[#C8961E]/20 px-2 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                >
                    LexFind Command Center
                </span>
                <h1 class="font-serif text-3xl font-bold tracking-tight">
                    System Performance & Audit
                </h1>
                <p class="max-w-2xl text-sm text-white/60">
                    Real-time statistics monitoring attorney profiles, user
                    directory accounts, and security-relevant change tracking.
                </p>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Lawyers KPI -->
            <div
                class="flex flex-col justify-between space-y-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <div class="rounded-xl bg-[#0A1929]/5 p-3 text-[#0A1929]">
                        <Briefcase class="h-6 w-6" />
                    </div>
                    <span
                        class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                    >
                        Attorneys
                    </span>
                </div>
                <div>
                    <h3 class="font-serif text-3xl font-bold text-neutral-800">
                        {{ stats.total_lawyers }}
                    </h3>
                    <p class="mt-1 text-xs text-neutral-400">
                        Total attorney profiles indexed
                    </p>
                </div>
                <div
                    class="flex items-center justify-between border-t border-neutral-100 pt-4 text-xs text-neutral-500"
                >
                    <span class="flex items-center gap-1">
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        ></span>
                        {{ stats.assigned_lawyers }} Assigned
                    </span>
                    <span class="flex items-center gap-1">
                        <span
                            class="h-2 w-2 rounded-full bg-neutral-300"
                        ></span>
                        {{ stats.unassigned_lawyers }} Unassigned
                    </span>
                </div>
            </div>

            <!-- Users KPI -->
            <div
                class="flex flex-col justify-between space-y-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <div class="rounded-xl bg-[#0A1929]/5 p-3 text-[#0A1929]">
                        <Users class="h-6 w-6" />
                    </div>
                    <span
                        class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                    >
                        User Accounts
                    </span>
                </div>
                <div>
                    <h3 class="font-serif text-3xl font-bold text-neutral-800">
                        {{ stats.total_users }}
                    </h3>
                    <p class="mt-1 text-xs text-neutral-400">
                        Registered application accounts
                    </p>
                </div>
                <div
                    class="flex items-center justify-between border-t border-neutral-100 pt-4 text-xs text-neutral-500"
                >
                    <span class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        {{ stats.privileged_users }} Staff/Admins
                    </span>
                </div>
            </div>

            <!-- Security / Bans KPI -->
            <div
                class="flex flex-col justify-between space-y-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <div class="rounded-xl bg-[#0A1929]/5 p-3 text-[#0A1929]">
                        <ShieldAlert class="h-6 w-6" />
                    </div>
                    <span
                        class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                    >
                        Integrity Monitoring
                    </span>
                </div>
                <div>
                    <h3 class="font-serif text-3xl font-bold text-neutral-800">
                        {{ stats.banned_users }}
                    </h3>
                    <p class="mt-1 text-xs text-neutral-400">
                        Currently banned user profiles
                    </p>
                </div>
                <div
                    class="flex items-center gap-2 border-t border-neutral-100 pt-4 text-xs"
                >
                    <span
                        v-if="stats.banned_users > 0"
                        class="flex items-center gap-1 font-medium text-red-500"
                    >
                        ⚠️ Action Required: Ban list active
                    </span>
                    <span
                        v-else
                        class="flex items-center gap-1 font-medium text-emerald-600"
                    >
                        <CheckCircle class="h-4 w-4" /> All users clean
                    </span>
                </div>
            </div>
        </div>

        <!-- Audit Trail Activity (Only if auth user has access) -->
        <div
            v-if="
                $page.props.auth.user?.system === 'ghost' ||
                $page.props.auth.user?.system === 'simp'
            "
            class="overflow-hidden rounded-2xl border border-black/5 bg-white text-left shadow-sm"
        >
            <div
                class="flex flex-col justify-between gap-4 border-b border-neutral-100 px-6 py-5 sm:flex-row sm:items-center"
            >
                <div>
                    <h2
                        class="flex items-center gap-2 font-serif text-lg font-bold text-neutral-800"
                    >
                        <Clock class="h-5 w-5 text-[#C8961E]" />
                        Recent System Activity Audit Trail
                    </h2>
                    <p class="mt-0.5 text-xs text-neutral-400">
                        Chronological record of lawyer profile mutations,
                        assignments, and database updates.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/audit-logs"
                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-[#C8961E]/30 bg-[#C8961E]/10 px-3.5 py-1.5 text-xs font-semibold text-[#C8961E] transition-all hover:bg-[#C8961E]/20 hover:text-[#DBA93E]"
                    >
                        <span>View Full Trail</span>
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 bg-neutral-50/50 text-[9px] tracking-wider text-neutral-400 uppercase"
                        >
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">Operator</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Subject Lawyer</th>
                            <th class="px-4 py-3 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr
                            v-if="auditLogs.length === 0"
                            class="text-center text-neutral-400"
                        >
                            <td colspan="5" class="py-12">
                                No audit logs have been recorded in this
                                session.
                            </td>
                        </tr>
                        <template v-for="log in auditLogs" :key="log.id">
                            <tr class="transition-all hover:bg-neutral-50/40">
                                <td
                                    class="px-4 py-3.5 whitespace-nowrap text-neutral-500"
                                >
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td
                                    class="px-4 py-3.5 font-semibold text-neutral-700"
                                >
                                    {{ log.user_name }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span
                                        :class="[
                                            'inline-block rounded border border-current px-2 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                            getActionColor(log.action),
                                        ]"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-neutral-800 font-semibold">
                                    <Link
                                        v-if="log.lawyer"
                                        :href="`/lawyers/${log.lawyer.slug}`"
                                        class="text-neutral-800 hover:text-[#DBA93E] hover:underline"
                                    >
                                        {{ log.lawyer_name }}
                                    </Link>
                                    <span v-else class="text-neutral-800">
                                        {{ log.lawyer_name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <button
                                        @click="toggleLog(log.id)"
                                        class="inline-flex cursor-pointer items-center gap-1 text-xs font-semibold text-[#C8961E] transition-all hover:text-[#DBA93E]"
                                    >
                                        <span>{{
                                            expandedLogs[log.id]
                                                ? 'Hide'
                                                : 'Inspect'
                                        }}</span>
                                        <ChevronDown
                                            v-if="!expandedLogs[log.id]"
                                            class="h-3.5 w-3.5"
                                        />
                                        <ChevronUp v-else class="h-3.5 w-3.5" />
                                    </button>
                                </td>
                            </tr>
                            <!-- Details Drawer Block -->
                            <tr v-if="expandedLogs[log.id]">
                                <td
                                    colspan="5"
                                    class="border-y border-neutral-100 bg-neutral-50/70 p-6 text-left"
                                >
                                    <div
                                        class="max-w-3xl space-y-4 rounded-xl border border-black/5 bg-white p-4 shadow-inner"
                                    >
                                        <div
                                            class="flex items-center gap-2 border-b pb-2 text-xs font-semibold text-neutral-500"
                                        >
                                            <FileText
                                                class="h-4 w-4 text-[#C8961E]"
                                            />
                                            <span
                                                >Raw Payload Inspection (Log #{{
                                                    log.id
                                                }})</span
                                            >
                                        </div>

                                        <div
                                            class="space-y-3 overflow-x-auto font-mono text-[11px] text-neutral-700"
                                        >
                                            <div
                                                v-if="
                                                    formatDetails(log.details)
                                                        ?.fields
                                                "
                                                class="space-y-1"
                                            >
                                                <div
                                                    class="text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                                                >
                                                    Initial Record Payload
                                                </div>
                                                <pre
                                                    class="max-h-60 overflow-y-auto rounded-lg bg-neutral-50 p-3 text-neutral-600"
                                                    >{{
                                                        JSON.stringify(
                                                            formatDetails(
                                                                log.details,
                                                            ).fields,
                                                            null,
                                                            2,
                                                        )
                                                    }}</pre
                                                >
                                            </div>

                                            <div
                                                v-else-if="
                                                    formatDetails(log.details)
                                                        ?.new
                                                "
                                                class="space-y-2"
                                            >
                                                <div
                                                    class="text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                                                >
                                                    Changed Values
                                                </div>
                                                <div
                                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                                >
                                                    <div
                                                        class="rounded-lg border border-red-100 bg-red-50/50 p-3"
                                                    >
                                                        <div
                                                            class="mb-1 text-[9px] font-bold text-red-500 uppercase"
                                                        >
                                                            Previous Values
                                                        </div>
                                                        <pre
                                                            class="text-red-700"
                                                            >{{
                                                                JSON.stringify(
                                                                    formatDetails(
                                                                        log.details,
                                                                    ).old,
                                                                    null,
                                                                    2,
                                                                )
                                                            }}</pre
                                                        >
                                                    </div>
                                                    <div
                                                        class="rounded-lg border border-emerald-100 bg-emerald-50/50 p-3"
                                                    >
                                                        <div
                                                            class="mb-1 text-[9px] font-bold text-emerald-600 uppercase"
                                                        >
                                                            Updated Values
                                                        </div>
                                                        <pre
                                                            class="text-emerald-700"
                                                            >{{
                                                                JSON.stringify(
                                                                    formatDetails(
                                                                        log.details,
                                                                    ).new,
                                                                    null,
                                                                    2,
                                                                )
                                                            }}</pre
                                                        >
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-else class="space-y-1">
                                                <pre
                                                    class="rounded-lg bg-neutral-50 p-3 text-neutral-600"
                                                    >{{
                                                        JSON.stringify(
                                                            formatDetails(
                                                                log.details,
                                                            ),
                                                            null,
                                                            2,
                                                        )
                                                    }}</pre
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
