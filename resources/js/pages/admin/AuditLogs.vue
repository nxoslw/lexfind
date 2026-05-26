<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Clock,
    ChevronDown,
    ChevronUp,
    FileText,
    Search,
    X,
    LayoutDashboard,
    SlidersHorizontal,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface AuditLog {
    id: number;
    user_id: number;
    user_name: string;
    lawyer_id: number;
    lawyer_name: string;
    action: string;
    details: string | Record<string, any>;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedAuditLogs {
    current_page: number;
    data: AuditLog[];
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

interface Filters {
    search: string | null;
    time: string;
    per_page: number;
}

const props = defineProps<{
    auditLogs: PaginatedAuditLogs;
    filters: Filters;
}>();

const expandedLogs = ref<Record<number, boolean>>({});
const searchQuery = ref(props.filters.search || '');
const timeFilter = ref(props.filters.time || 'all');
const perPageFilter = ref(props.filters.per_page || 25);

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
        case 'create_case':
            return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400';
        case 'update':
        case 'update_case':
            return 'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400';
        case 'delete':
        case 'delete_case':
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
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};

// Custom simple debounce implementation
function debounce<T extends (...args: any[]) => void>(fn: T, delay: number): (...args: Parameters<T>) => void {
    let timeoutId: ReturnType<typeof setTimeout> | null = null;
    return (...args: Parameters<T>) => {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}

const updateFilters = () => {
    router.get('/admin/audit-logs', {
        search: searchQuery.value || undefined,
        time: timeFilter.value !== 'all' ? timeFilter.value : undefined,
        per_page: perPageFilter.value !== 25 ? perPageFilter.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const debouncedUpdate = debounce(updateFilters, 400);

watch(searchQuery, () => {
    debouncedUpdate();
});

watch([timeFilter, perPageFilter], () => {
    updateFilters();
});

const clearSearch = () => {
    searchQuery.value = '';
};

const cleanLabel = (label: string) => {
    if (label.includes('Previous')) {
        return '« Prev';
    }
    if (label.includes('Next')) {
        return 'Next »';
    }
    return label;
};
</script>

<template>
    <Head title="System Activity Audit Trail" />

    <div class="space-y-6">
        <!-- Page Header Action Control -->
        <div
            class="flex flex-col justify-between gap-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs sm:flex-row sm:items-center"
        >
            <div class="text-left">
                <div class="flex items-center gap-2">
                    <span
                        class="rounded border border-[#C8961E]/30 bg-[#C8961E]/20 px-2 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                    >
                        Security & Integrity
                    </span>
                </div>
                <h1 class="mt-1 font-serif text-xl font-bold text-neutral-800">
                    System Activity Audit Trail
                </h1>
                <p class="mt-1 text-xs font-medium text-neutral-400">
                    Comprehensive, searchable log of administrator mutations, assignments, and structural profile updates.
                </p>
            </div>
            <div>
                <Link
                    href="/admin/dashboard"
                    class="flex cursor-pointer items-center gap-1.5 rounded-xl border border-black/5 bg-white px-4 py-2 text-xs font-semibold text-neutral-600 transition-all hover:bg-neutral-50 hover:text-neutral-800"
                >
                    <LayoutDashboard class="h-4 w-4" /> Return to Dashboard
                </Link>
            </div>
        </div>

        <!-- Filter and Search controls bar -->
        <div
            class="overflow-hidden rounded-2xl border border-black/5 bg-white text-left shadow-xs"
        >
            <!-- Search & Filters Header -->
            <div
                class="flex flex-col gap-4 border-b border-neutral-100 bg-neutral-50/50 p-5 lg:flex-row lg:items-center lg:justify-between"
            >
                <!-- Search Input Group -->
                <div class="relative min-w-[280px] flex-1">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400"
                    >
                        <Search class="h-4 w-4" />
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by operator name, action, or subject lawyer..."
                        class="w-full rounded-lg border border-black/8 bg-white py-2 pr-10 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-neutral-600"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <!-- Select Filters Group -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <SlidersHorizontal class="h-3.5 w-3.5 text-neutral-400" />
                        <span class="text-xs font-semibold text-neutral-500">Filters:</span>
                    </div>

                    <!-- Time Filter Selector -->
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase">Period</label>
                        <select
                            v-model="timeFilter"
                            class="min-w-[120px] cursor-pointer rounded-lg border border-black/8 bg-white p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                        >
                            <option value="all">All Logs</option>
                            <option value="today">Today</option>
                            <option value="3_days">Last 3 Days</option>
                            <option value="7_days">Last 7 Days</option>
                            <option value="15_days">Last 15 Days</option>
                            <option value="30_days">Last 30 Days</option>
                            <option value="60_days">Last 60 Days</option>
                            <option value="90_days">Last 90 Days</option>
                            <option value="365_days">Last Year</option>
                        </select>
                    </div>

                    <!-- Per Page Rows Selector -->
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase">Rows</label>
                        <select
                            v-model="perPageFilter"
                            class="min-w-[80px] cursor-pointer rounded-lg border border-black/8 bg-white p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                        >
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                            <option :value="200">200</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 bg-neutral-50/30 text-[9px] tracking-wider text-neutral-400 uppercase"
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
                            v-if="auditLogs.data.length === 0"
                            class="text-center text-neutral-400"
                        >
                            <td colspan="5" class="py-16 text-xs text-neutral-500">
                                No activity audit logs match the selected search or filter parameters.
                            </td>
                        </tr>
                        <template v-for="log in auditLogs.data" :key="log.id">
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

            <!-- Pagination Footer Section -->
            <div
                v-if="auditLogs.total > 0"
                class="flex flex-col justify-between gap-4 border-t border-neutral-100 px-6 py-4 sm:flex-row sm:items-center"
            >
                <div class="text-xs font-semibold text-neutral-400">
                    Showing {{ auditLogs.from }} to {{ auditLogs.to }} of
                    {{ auditLogs.total }} logged events
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto py-1">
                    <template v-for="(link, i) in auditLogs.links" :key="i">
                        <span
                            v-if="!link.url"
                            class="inline-block rounded-lg px-3 py-1.5 text-xs text-neutral-300 select-none cursor-not-allowed"
                            v-html="cleanLabel(link.label)"
                        ></span>
                        <Link
                            v-else
                            :href="link.url"
                            :class="[
                                'inline-block rounded-lg px-3 py-1.5 text-xs font-bold transition-all duration-200',
                                link.active
                                    ? 'bg-[#0A1929] border border-[#0A1929] text-white shadow-sm'
                                    : 'bg-white border border-black/8 text-neutral-600 hover:border-[#C8961E]/40 hover:text-[#C8961E] hover:bg-[#C8961E]/5'
                            ]"
                            v-html="cleanLabel(link.label)"
                        ></Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
