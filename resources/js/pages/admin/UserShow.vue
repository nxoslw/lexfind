<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Mail,
    Calendar,
    Shield,
    Lock,
    Eye,
    Activity,
    AlertCircle,
    UserCheck,
    UserX,
    KeyRound,
    Check,
    Terminal,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Lawyer {
    id: number;
    slug: string;
    name: string;
    firm: string;
    city: string;
    state: string;
    specialty: string;
    avatar_color: string;
    initials: string;
}

interface UserDetail {
    id: number;
    name: string;
    email: string;
    system: string;
    banned: string | null;
    created_at: string;
    updated_at: string;
    email_verified_at: string | null;
    two_factor_confirmed_at: string | null;
    lawyers: Lawyer[];
}

interface SessionLog {
    ip_address: string;
    user_agent: string;
    last_activity: number;
    is_current: boolean;
}

const props = defineProps<{
    user: UserDetail;
    sessions: SessionLog[];
}>();

const page = usePage();

// Password reset form
const resetForm = useForm({
    password: '',
    password_confirmation: '',
});

const submitPasswordReset = () => {
    resetForm.post(`/admin/users/${props.user.id}/reset-password`, {
        onSuccess: () => {
            resetForm.reset();
            alert('Password has been successfully updated.');
        },
    });
};

const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'ghost':
        case 'simp':
            return 'bg-slate-900 border-slate-700 text-slate-200';
        case 'bat':
            return 'bg-blue-500/10 border-blue-500/20 text-blue-600';
        case 'bip':
            return 'bg-sky-500/10 border-sky-500/20 text-sky-600';
        case 'god':
        default:
            return 'bg-neutral-100 border-black/5 text-neutral-600';
    }
};

const getRoleName = (role: string) => {
    switch (role) {
        case 'ghost':
            return 'Admin';
        case 'simp':
            return 'Admin';
        case 'bat':
            return 'Mod';
        case 'bip':
            return 'Front Mod';
        case 'god':
        default:
            return 'Standard User';
    }
};

const formatDate = (dateStr: string) => {
    if (!dateStr) {
        return 'N/A';
    }

    const date = new Date(dateStr);

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`User Account Details: ${user.name}`" />

    <div class="space-y-6">
        <!-- Toast Flash Messages -->
        <div
            v-if="$page.props.flash?.success"
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 shadow-xs"
        >
            <Check class="h-5 w-5 shrink-0 text-emerald-600" />
            <div>{{ $page.props.flash.success }}</div>
        </div>

        <!-- Back Button & Header -->
        <div
            class="flex items-center gap-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs"
        >
            <Link
                href="/admin/users"
                class="shrink-0 cursor-pointer rounded-xl border border-black/5 bg-neutral-50 p-2.5 text-neutral-700 shadow-xs transition-all hover:bg-neutral-100"
            >
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div class="overflow-hidden text-left">
                <div class="flex flex-wrap items-center gap-2">
                    <h1
                        class="truncate font-serif text-xl font-bold text-neutral-800"
                    >
                        {{ user.name }}
                    </h1>
                    <span
                        :class="[
                            'inline-block shrink-0 rounded border px-2 py-0.5 text-[8px] font-bold tracking-wider uppercase',
                            getRoleBadgeClass(user.system),
                        ]"
                    >
                        {{ getRoleName(user.system) }}
                    </span>
                    <span
                        v-if="user.banned"
                        class="inline-flex shrink-0 items-center gap-0.5 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[8px] font-bold tracking-wider text-red-700 uppercase"
                    >
                        <UserX class="h-3.5 w-3.5" /> Banned
                    </span>
                    <span
                        v-else
                        class="inline-flex shrink-0 items-center gap-0.5 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[8px] font-bold tracking-wider text-emerald-700 uppercase"
                    >
                        <UserCheck class="h-3.5 w-3.5" /> Active
                    </span>
                </div>
                <p class="mt-1 truncate text-xs text-neutral-400">
                    ID: {{ user.id }} &bull; {{ user.email }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Left panel: Account details metadata -->
            <div class="space-y-6">
                <!-- Account Details Card -->
                <div
                    class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-xs"
                >
                    <h3
                        class="mb-4 flex items-center gap-2 border-b pb-3 font-serif text-sm font-bold text-neutral-800"
                    >
                        <Shield class="h-4.5 w-4.5 text-[#C8961E]" /> Account &
                        Login Metadata
                    </h3>

                    <div class="divide-y divide-neutral-100 text-xs">
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >User Account Name</span
                            >
                            <span
                                class="max-w-xs font-bold break-words text-neutral-800 sm:text-right"
                                >{{ user.name }}</span
                            >
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Email Address</span
                            >
                            <span
                                class="font-semibold break-all text-neutral-700 select-all sm:text-right"
                                >{{ user.email }}</span
                            >
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Permission Level</span
                            >
                            <span class="font-bold sm:text-right">
                                <span
                                    :class="[
                                        'inline-block rounded border px-2 py-0.5 text-[8px] font-bold tracking-wider uppercase',
                                        getRoleBadgeClass(user.system),
                                    ]"
                                >
                                    {{ getRoleName(user.system) }}
                                </span>
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Email Verification</span
                            >
                            <span
                                class="font-medium text-neutral-700 sm:text-right"
                            >
                                <span
                                    v-if="user.email_verified_at"
                                    class="inline-flex items-center gap-1 rounded border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[10px] text-emerald-700"
                                >
                                    ✓ Verified on
                                    {{ formatDate(user.email_verified_at) }}
                                </span>
                                <span
                                    v-else
                                    class="inline-block rounded bg-neutral-50 px-2 py-0.5 text-[10px] text-neutral-400 italic"
                                    >Unverified</span
                                >
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Two-Factor Authentication</span
                            >
                            <span
                                class="font-medium text-neutral-700 sm:text-right"
                            >
                                <span
                                    v-if="user.two_factor_confirmed_at"
                                    class="inline-flex items-center gap-1 rounded border border-purple-100 bg-purple-50 px-2 py-0.5 text-[10px] text-purple-700"
                                >
                                    ✓ Active since
                                    {{
                                        formatDate(user.two_factor_confirmed_at)
                                    }}
                                </span>
                                <span
                                    v-else
                                    class="inline-block rounded bg-neutral-50 px-2 py-0.5 text-[10px] text-neutral-400 italic"
                                    >Not Setup</span
                                >
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Ban Restriction Detail</span
                            >
                            <span
                                class="max-w-xs font-medium break-words text-neutral-700 sm:text-right"
                            >
                                <span
                                    v-if="user.banned"
                                    class="inline-block rounded border border-red-100 bg-red-50 px-2.5 py-0.5 text-[10px] text-red-700"
                                >
                                    Banned: {{ user.banned }}
                                </span>
                                <span
                                    v-else
                                    class="inline-block rounded border border-emerald-100 bg-emerald-50 px-2.5 py-0.5 text-[10px] text-emerald-700"
                                >
                                    None (Active)
                                </span>
                            </span>
                        </div>
                        <div
                            class="flex flex-col justify-between gap-1 py-3 sm:flex-row sm:items-center"
                        >
                            <span
                                class="text-[8px] font-semibold tracking-wider text-neutral-400 uppercase sm:w-44"
                                >Date Registered</span
                            >
                            <span
                                class="flex items-center gap-1.5 font-medium text-neutral-600 sm:justify-end sm:text-right"
                            >
                                <Calendar
                                    class="h-3.5 w-3.5 text-neutral-400"
                                />
                                {{ formatDate(user.created_at) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Assigned Profiles Card -->
                <div
                    class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-xs"
                >
                    <h3
                        class="mb-4 flex items-center justify-between border-b pb-3 font-serif text-sm font-bold text-neutral-800"
                    >
                        <span class="flex items-center gap-2">
                            <UserCheck class="h-4.5 w-4.5 text-[#C8961E]" />
                            Assigned Directory Profiles
                        </span>
                        <span
                            class="shrink-0 rounded-full bg-[#C8961E]/10 px-2 py-0.5 text-[10px] font-bold text-[#7A5400]"
                        >
                            {{ user.lawyers.length }} linked
                        </span>
                    </h3>

                    <div
                        v-if="user.lawyers.length === 0"
                        class="space-y-2 py-6 text-center text-neutral-400"
                    >
                        <p class="text-xs">
                            No attorney directory records are linked to this
                            account.
                        </p>
                        <p class="text-[10px] text-neutral-400">
                            Admins can link lawyers to standard accounts via the
                            Attorneys Directory.
                        </p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="lawyer in user.lawyers"
                            :key="lawyer.id"
                            class="flex flex-col justify-between gap-3 rounded-xl border border-black/5 bg-neutral-50/50 p-4 transition-all hover:bg-neutral-50 sm:flex-row sm:items-center"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg font-serif text-sm font-bold text-white shadow-inner"
                                    :style="{
                                        backgroundColor: lawyer.avatar_color,
                                    }"
                                >
                                    {{ lawyer.initials }}
                                </div>
                                <div class="min-w-0 text-left">
                                    <div
                                        class="truncate text-xs font-bold text-neutral-800"
                                    >
                                        {{ lawyer.name }}
                                    </div>
                                    <div
                                        class="truncate text-[10px] font-medium text-neutral-500"
                                    >
                                        {{ lawyer.firm }}
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <Link
                                    :href="`/lawyers/${lawyer.slug}`"
                                    class="inline-flex items-center gap-1 rounded-lg border border-[#C8961E]/30 bg-[#C8961E]/10 px-2.5 py-1.5 text-[10px] font-bold text-[#0A1929] transition-all hover:bg-[#C8961E]/20"
                                >
                                    <Eye class="h-3.5 w-3.5 text-[#C8961E]" />
                                    View Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right panel: Admin Password Reset and current operator log -->
            <div class="space-y-6">
                <!-- Password Reset Card -->
                <div
                    class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-xs"
                >
                    <h3
                        class="mb-4 flex items-center gap-2 border-b pb-3 font-serif text-sm font-bold text-neutral-800"
                    >
                        <KeyRound class="h-4.5 w-4.5 text-amber-600" /> Override
                        Credentials
                    </h3>
                    <p
                        class="mb-4 text-[10px] leading-relaxed text-neutral-400"
                    >
                        Direct password overwrite. Ensure you coordinate with
                        the user before committing this action.
                    </p>

                    <form
                        @submit.prevent="submitPasswordReset"
                        class="space-y-4"
                    >
                        <div class="space-y-3">
                            <div>
                                <label
                                    class="mb-1 block text-[9px] font-bold tracking-wider text-neutral-500 uppercase"
                                    >New password *</label
                                >
                                <input
                                    v-model="resetForm.password"
                                    type="password"
                                    class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                    required
                                    placeholder="Minimum 8 characters"
                                />
                                <div
                                    v-if="resetForm.errors.password"
                                    class="mt-1 text-[10px] text-red-500"
                                >
                                    {{ resetForm.errors.password }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-1 block text-[9px] font-bold tracking-wider text-neutral-500 uppercase"
                                    >Confirm password *</label
                                >
                                <input
                                    v-model="resetForm.password_confirmation"
                                    type="password"
                                    class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                    required
                                    placeholder="Confirm new password"
                                />
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="resetForm.processing"
                            class="flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-lg bg-[#0A1929] px-4 py-2.5 text-xs font-bold tracking-wider text-white uppercase shadow-sm transition-all hover:bg-[#1E3A54] disabled:opacity-50"
                        >
                            <Lock class="h-4 w-4" /> Reset Password
                        </button>
                    </form>
                </div>

                <!-- Current Operator Metadata -->
                <div
                    class="space-y-3 rounded-2xl border border-white/5 bg-gradient-to-r from-neutral-800 to-slate-900 p-5 text-left text-white shadow-xs"
                >
                    <h4
                        class="flex items-center gap-2 text-xs font-bold tracking-wider text-[#F0C96A] uppercase"
                    >
                        <Terminal class="h-4.5 w-4.5" />
                        Session Operator
                    </h4>
                    <p class="text-[10px] leading-normal text-white/60">
                        You are currently viewing this user record under the
                        following administrative login context:
                    </p>
                    <div
                        class="space-y-1.5 rounded-xl border border-white/5 bg-black/20 p-3 font-mono text-[11px] text-white/80"
                    >
                        <div>
                            <span class="text-white/40">NAME:</span>
                            {{ $page.props.auth.user?.name }}
                        </div>
                        <div>
                            <span class="text-white/40">EMAIL:</span>
                            {{ $page.props.auth.user?.email }}
                        </div>
                        <div>
                            <span class="text-white/40">ROLE:</span>
                            {{
                                getRoleName($page.props.auth.user?.system || '')
                            }}
                        </div>
                    </div>
                </div>

                <!-- Login Sessions Card -->
                <div
                    class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-xs"
                >
                    <h3
                        class="mb-4 flex items-center gap-2 border-b pb-3 font-serif text-sm font-bold text-neutral-800"
                    >
                        <Activity class="h-4.5 w-4.5 text-[#C8961E]" /> Recent
                        Login History
                    </h3>

                    <div
                        v-if="sessions.length === 0"
                        class="py-4 text-center text-xs text-neutral-400"
                    >
                        No recent active sessions recorded.
                    </div>
                    <div v-else class="max-h-72 space-y-3 overflow-y-auto pr-1">
                        <div
                            v-for="session in sessions"
                            :key="session.ip_address + session.last_activity"
                            class="space-y-2 rounded-xl border border-black/5 bg-neutral-50/50 p-3.5 text-[11px]"
                        >
                            <div
                                class="flex items-center justify-between font-mono font-semibold"
                            >
                                <span class="text-neutral-700">{{
                                    session.ip_address
                                }}</span>
                                <span
                                    v-if="session.is_current"
                                    class="rounded-full border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[8px] font-bold tracking-wider text-emerald-800 uppercase"
                                >
                                    Current
                                </span>
                            </div>
                            <div
                                class="line-clamp-2 text-[10px] leading-normal text-neutral-400"
                                :title="session.user_agent"
                            >
                                {{ session.user_agent }}
                            </div>
                            <div
                                class="text-[9px] font-medium text-neutral-400"
                            >
                                Last Active:
                                {{
                                    formatDate(
                                        new Date(
                                            session.last_activity * 1000,
                                        ).toISOString(),
                                    )
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
