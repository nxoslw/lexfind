<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Plus,
    Edit,
    Trash2,
    Search,
    Check,
    X,
    AlertCircle,
    UserCheck,
    UserX,
    Lock,
    Eye,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface UserItem {
    id: number;
    name: string;
    email: string;
    system: string;
    banned: string | null;
}

const props = defineProps<{
    users: UserItem[];
}>();

const page = usePage();

// Search filter state
const searchQuery = ref('');

const filteredUsers = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.users;
    }

    const query = searchQuery.value.toLowerCase();

    return props.users.filter(
        (u) =>
            u.name.toLowerCase().includes(query) ||
            u.email.toLowerCase().includes(query) ||
            u.system.toLowerCase().includes(query) ||
            (u.banned && u.banned.toLowerCase().includes(query)),
    );
});

// Modal / Slide-over state
const isFormModalOpen = ref(false);
const editingUser = ref<UserItem | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    system: 'god',
    banned: '',
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();

    form.name = '';
    form.email = '';
    form.password = '';
    form.system = 'god';
    form.banned = '';

    isFormModalOpen.value = true;
};

const openEditModal = (user: UserItem) => {
    editingUser.value = user;
    form.clearErrors();

    form.name = user.name;
    form.email = user.email;
    form.password = ''; // Keep blank to not change password
    form.system = user.system;
    form.banned = user.banned || '';

    isFormModalOpen.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.post(`/admin/users/${editingUser.value.id}`, {
            onSuccess: () => {
                isFormModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin/users', {
            onSuccess: () => {
                isFormModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const deleteUser = (user: UserItem) => {
    if (user.id === page.props.auth.user.id) {
        alert('You cannot delete your own active administrator account.');

        return;
    }

    if (
        confirm(
            `Are you sure you want to permanently delete the user account for ${user.name}?`,
        )
    ) {
        useForm({}).post(`/admin/users/${user.id}/delete`, {
            onSuccess: () => {
                alert('User account deleted successfully.');
            },
        });
    }
};

// KPIs
const totalUsersCount = computed(() => props.users.length);
const totalAdminsCount = computed(
    () => props.users.filter((u) => u.system !== 'god').length,
);
const totalBannedCount = computed(
    () =>
        props.users.filter((u) => u.banned !== null && u.banned !== '').length,
);

const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'ghost': // Admin
        case 'simp': // Site Owner (shown as Admin)
            return 'bg-slate-900 border-slate-700 text-slate-200';
        case 'bat': // Moderator
            return 'bg-blue-500/10 border-blue-500/20 text-blue-600';
        case 'bip': // Front Mod
            return 'bg-sky-500/10 border-sky-500/20 text-sky-600';
        case 'god': // Standard User
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
</script>

<template>
    <Head title="Admin Users Registry" />

    <div class="space-y-6">
        <!-- Toast Flash Messages -->
        <div
            v-if="$page.props.flash?.success"
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 shadow-xs"
        >
            <Check class="h-5 w-5 shrink-0 text-emerald-600" />
            <div>{{ $page.props.flash.success }}</div>
        </div>

        <!-- Page Header Action Control -->
        <div
            class="flex flex-col justify-between gap-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs sm:flex-row sm:items-center"
        >
            <div class="text-left">
                <h1 class="font-serif text-xl font-bold text-neutral-800">
                    User Registry
                </h1>
                <p class="mt-1 text-xs font-medium text-neutral-400">
                    Configure credentials, system-level roles, and ban status
                    reasons.
                </p>
            </div>
            <div>
                <button
                    @click="openCreateModal"
                    class="flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#C8961E] px-4 py-2 text-xs font-bold tracking-wider text-[#0A1929] uppercase shadow-sm transition-all hover:bg-[#DBA93E] hover:shadow"
                >
                    <Plus class="h-4 w-4" /> Add User Account
                </button>
            </div>
        </div>

        <!-- KPI Mini Board -->
        <div class="grid grid-cols-3 gap-4">
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-[#C8961E]">
                    {{ totalUsersCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Total Users
                </div>
            </div>
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-sky-600">
                    {{ totalAdminsCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Privileged Roles
                </div>
            </div>
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-red-500">
                    {{ totalBannedCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Banned Accounts
                </div>
            </div>
        </div>

        <!-- Users Table Registry -->
        <div
            class="overflow-hidden rounded-2xl border border-black/5 bg-white text-left shadow-xs"
        >
            <!-- Search & Filters -->
            <div
                class="flex flex-wrap items-center justify-between gap-4 border-b border-neutral-100 bg-neutral-50/50 p-4"
            >
                <div class="relative min-w-[240px] flex-1">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400"
                    >
                        <Search class="h-4 w-4" />
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Filter by name, email, role or ban reason..."
                        class="w-full rounded-lg border border-black/8 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                    />
                </div>
                <div class="text-xs font-medium text-neutral-400">
                    Showing {{ filteredUsers.length }} of
                    {{ users.length }} accounts
                </div>
            </div>

            <!-- Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 bg-neutral-50/30 text-[9px] tracking-wider text-neutral-400 uppercase"
                        >
                            <th class="px-4 py-3">User Account</th>
                            <th class="px-4 py-3">System Role</th>
                            <th class="px-4 py-3">Account Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr
                            v-if="filteredUsers.length === 0"
                            class="text-center text-neutral-400"
                        >
                            <td colspan="4" class="py-12">
                                No user accounts match the search parameters.
                            </td>
                        </tr>
                        <tr
                            v-for="user in filteredUsers"
                            :key="user.id"
                            class="transition-all hover:bg-neutral-50/40"
                        >
                            <td class="px-4 py-3.5">
                                <div>
                                    <div
                                        class="flex items-center gap-1.5 text-xs font-bold text-neutral-800"
                                    >
                                        {{ user.name }}
                                        <span
                                            v-if="
                                                user.id ===
                                                $page.props.auth.user.id
                                            "
                                            class="py-0.2 rounded border border-[#C8961E]/22 bg-[#C8961E]/12 px-1.5 text-[8px] font-semibold tracking-wider text-[#7A4F00] uppercase"
                                        >
                                            (You)
                                        </span>
                                    </div>
                                    <div
                                        class="mt-0.5 text-[10px] text-neutral-400"
                                    >
                                        {{ user.email }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'inline-block rounded border px-2.5 py-0.5 text-[9px] font-bold tracking-wider uppercase',
                                        getRoleBadgeClass(user.system),
                                    ]"
                                >
                                    {{ getRoleName(user.system) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    v-if="user.banned"
                                    class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-red-700 uppercase"
                                    :title="user.banned"
                                >
                                    <UserX class="h-3 w-3" /> Banned:
                                    {{ user.banned }}
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[9px] font-bold tracking-wider text-emerald-700 uppercase"
                                >
                                    <UserCheck class="h-3 w-3" /> Active
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <Link
                                        :href="`/admin/users/${user.id}`"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-neutral-100 hover:text-[#C8961E]"
                                        title="View User Profile Details"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="openEditModal(user)"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-neutral-100 hover:text-[#C8961E]"
                                        title="Edit User Details"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </button>
                                    <button
                                        @click="deleteUser(user)"
                                        :disabled="
                                            user.id === $page.props.auth.user.id
                                        "
                                        :class="[
                                            'cursor-pointer rounded p-1.5 transition-all',
                                            user.id === $page.props.auth.user.id
                                                ? 'cursor-not-allowed text-neutral-400 opacity-30'
                                                : 'text-neutral-600 hover:bg-red-50 hover:text-red-600',
                                        ]"
                                        title="Delete User Account"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Dialog Modal -->
        <div
            v-if="isFormModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-2xl bg-white text-left shadow-2xl"
            >
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between bg-[#0A1929] px-6 py-4 text-white"
                >
                    <div>
                        <h3 class="font-serif text-base font-bold text-white">
                            {{
                                editingUser
                                    ? `Edit Account: ${editingUser.name}`
                                    : 'Create New User Account'
                            }}
                        </h3>
                        <p class="text-[10px] text-white/50">
                            Ensure all parameters comply with system guidelines.
                        </p>
                    </div>
                    <button
                        @click="isFormModalOpen = false"
                        class="cursor-pointer text-white/60 transition-all hover:text-white"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Modal Form Body -->
                <form
                    @submit.prevent="submitForm"
                    class="flex-1 space-y-5 overflow-y-auto p-6"
                >
                    <div>
                        <label
                            class="mb-1 block text-xs font-bold text-neutral-700"
                            >Full User Name *</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            required
                        />
                        <div
                            v-if="form.errors.name"
                            class="mt-1 text-[10px] text-red-500"
                        >
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-xs font-bold text-neutral-700"
                            >Email Address *</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            required
                        />
                        <div
                            v-if="form.errors.email"
                            class="mt-1 text-[10px] text-red-500"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-xs font-bold text-neutral-700"
                        >
                            Password <span v-if="!editingUser">*</span>
                            <span
                                v-else
                                class="text-[10px] font-normal text-neutral-400"
                                >(Leave blank to keep current)</span
                            >
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            :required="!editingUser"
                        />
                        <div
                            v-if="form.errors.password"
                            class="mt-1 text-[10px] text-red-500"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Role Dropdown -->
                    <div>
                        <label
                            class="mb-1 block text-xs font-bold text-neutral-700"
                            >System Permission Role *</label
                        >
                        <select
                            v-model="form.system"
                            :disabled="
                                editingUser &&
                                editingUser.id === $page.props.auth.user.id
                            "
                            class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2.5 text-xs outline-none disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <option value="god">Standard User</option>
                            <option value="bip">Front Mod</option>
                            <option value="bat">Mod</option>
                            <option v-if="$page.props.auth.user?.system === 'ghost'" value="simp">Site Owner</option>
                            <option v-if="$page.props.auth.user?.system === 'ghost'" value="ghost">Admin</option>
                            <option v-if="$page.props.auth.user?.system === 'simp'" value="simp">Admin</option>
                        </select>
                        <p
                            v-if="
                                editingUser &&
                                editingUser.id === $page.props.auth.user.id
                            "
                            class="mt-1 flex items-start gap-1 text-[9px] text-amber-600"
                        >
                            <Lock class="mt-0.5 h-3 w-3 shrink-0" /> You cannot
                            demote your own active account permissions.
                        </p>
                    </div>

                    <!-- Ban Status Reason -->
                    <div>
                        <label
                            class="mb-1 block text-xs font-bold text-neutral-700"
                            >Account Ban Status</label
                        >
                        <input
                            v-model="form.banned"
                            type="text"
                            :disabled="
                                editingUser &&
                                editingUser.id === $page.props.auth.user.id
                            "
                            placeholder="Enter description to ban this user (leave blank to unban)"
                            class="w-full rounded-lg border border-black/10 p-2.5 text-xs outline-none focus:border-[#C8961E] disabled:cursor-not-allowed disabled:opacity-60"
                        />
                        <div
                            v-if="form.errors.banned"
                            class="mt-1 text-[10px] text-red-500"
                        >
                            {{ form.errors.banned }}
                        </div>
                        <p
                            v-if="
                                editingUser &&
                                editingUser.id === $page.props.auth.user.id
                            "
                            class="mt-1 flex items-start gap-1 text-[9px] text-amber-600"
                        >
                            <Lock class="mt-0.5 h-3 w-3 shrink-0" /> You cannot
                            ban your own active account.
                        </p>
                    </div>
                </form>

                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end gap-3 border-t border-neutral-100 bg-neutral-50 px-6 py-4"
                >
                    <button
                        type="button"
                        @click="isFormModalOpen = false"
                        class="cursor-pointer rounded-lg border border-neutral-200 bg-white px-4 py-2 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitForm"
                        :disabled="form.processing"
                        class="cursor-pointer rounded-lg bg-[#0A1929] px-5 py-2 text-xs font-bold tracking-wider text-white uppercase hover:bg-[#1E3A54] disabled:opacity-50"
                    >
                        {{
                            form.processing ? 'Saving...' : 'Save User Account'
                        }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
