<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Briefcase,
    Users,
    Compass,
    LogOut,
    Menu,
    X,
    ChevronRight,
    Bell,
    Clock,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import ConfirmLogoutModal from '@/components/ConfirmLogoutModal.vue';
import { useConfirmLogout } from '@/composables/useConfirmLogout';
import type { User } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth.user as User);
const isSidebarOpen = ref(false);
const { triggerLogout } = useConfirmLogout();

const handleLogout = () => {
    triggerLogout();
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

const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'ghost':
        case 'simp':
            return 'bg-slate-900 border-slate-700 text-slate-200';
        case 'bat':
            return 'bg-blue-500/10 border-blue-500/20 text-blue-400';
        case 'bip':
            return 'bg-sky-500/10 border-sky-500/20 text-sky-400';
        default:
            return 'bg-neutral-800 border-neutral-700 text-neutral-400';
    }
};
</script>

<template>
    <div
        class="relative flex min-h-screen overflow-x-hidden bg-[#F8F6F0] font-sans text-[#16161A]"
    >
        <!-- Mobile Sidebar Toggle -->
        <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="fixed top-4 left-4 z-50 cursor-pointer rounded-xl border border-white/10 bg-[#0A1929] p-2 text-white shadow-lg transition-all hover:bg-[#0F2234] lg:hidden"
        >
            <Menu v-if="!isSidebarOpen" class="h-5 w-5" />
            <X v-else class="h-5 w-5" />
        </button>

        <!-- Sidebar Navigation Container -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col justify-between border-r border-white/5 bg-[#0A1929] text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Sidebar Top Content -->
            <div class="flex flex-1 flex-col">
                <!-- Branding Header -->
                <div
                    class="flex items-center justify-between border-b border-white/5 px-6 py-6"
                >
                    <Link
                        href="/"
                        class="font-serif text-2xl font-bold tracking-wide text-[#DBA93E]"
                    >
                        Lex<span class="font-normal text-white">Find</span>
                    </Link>
                    <span
                        class="rounded border border-[#C8961E]/30 bg-[#C8961E]/20 px-2 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                    >
                        Admin
                    </span>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 space-y-1.5 px-4 py-6">
                    <Link
                        href="/admin/dashboard"
                        :class="[
                            'group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition-all duration-200',
                            $page.url === '/admin/dashboard'
                                ? 'border border-white/5 bg-white/10 font-medium text-[#F0C96A]'
                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <LayoutDashboard class="h-4 w-4 text-[#DBA93E]" />
                            <span>Overview</span>
                        </div>
                        <ChevronRight
                            :class="[
                                'h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:opacity-100',
                                $page.url === '/admin/dashboard'
                                    ? 'translate-x-0 opacity-100'
                                    : 'group-hover:translate-x-1',
                            ]"
                        />
                    </Link>

                    <Link
                        href="/admin/lawyers"
                        :class="[
                            'group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition-all duration-200',
                            $page.url === '/admin/lawyers'
                                ? 'border border-white/5 bg-white/10 font-medium text-[#F0C96A]'
                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <Briefcase class="h-4 w-4 text-[#DBA93E]" />
                            <span>Attorneys Directory</span>
                        </div>
                        <ChevronRight
                            :class="[
                                'h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:opacity-100',
                                $page.url === '/admin/lawyers'
                                    ? 'translate-x-0 opacity-100'
                                    : 'group-hover:translate-x-1',
                            ]"
                        />
                    </Link>

                    <Link
                        href="/admin/cases"
                        :class="[
                            'group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition-all duration-200',
                            $page.url === '/admin/cases'
                                ? 'border border-white/5 bg-white/10 font-medium text-[#F0C96A]'
                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <Briefcase class="h-4 w-4 text-[#DBA93E]" />
                            <span>Cases Management</span>
                        </div>
                        <ChevronRight
                            :class="[
                                'h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:opacity-100',
                                $page.url === '/admin/cases'
                                    ? 'translate-x-0 opacity-100'
                                    : 'group-hover:translate-x-1',
                            ]"
                        />
                    </Link>

                    <!-- Users Management (Visible to admin, site owner, and mod) -->
                    <Link
                        v-if="user?.system === 'ghost' || user?.system === 'simp' || user?.system === 'bat'"
                        href="/admin/users"
                        :class="[
                            'group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition-all duration-200',
                            $page.url.startsWith('/admin/users')
                                ? 'border border-white/5 bg-white/10 font-medium text-[#F0C96A]'
                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <Users class="h-4 w-4 text-[#DBA93E]" />
                            <span>User Accounts</span>
                        </div>
                        <ChevronRight
                            :class="[
                                'h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:opacity-100',
                                $page.url.startsWith('/admin/users')
                                    ? 'translate-x-0 opacity-100'
                                    : 'group-hover:translate-x-1',
                            ]"
                        />
                    </Link>

                    <!-- Activity Audit Trail (Visible to ghost and simp) -->
                    <Link
                        v-if="user?.system === 'ghost' || user?.system === 'simp'"
                        href="/admin/audit-logs"
                        :class="[
                            'group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm transition-all duration-200',
                            $page.url.startsWith('/admin/audit-logs')
                                ? 'border border-white/5 bg-white/10 font-medium text-[#F0C96A]'
                                : 'text-white/70 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <Clock class="h-4 w-4 text-[#DBA93E]" />
                            <span>Activity Trail</span>
                        </div>
                        <ChevronRight
                            :class="[
                                'h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:opacity-100',
                                $page.url.startsWith('/admin/audit-logs')
                                    ? 'translate-x-0 opacity-100'
                                    : 'group-hover:translate-x-1',
                            ]"
                        />
                    </Link>

                    <div class="my-4 border-t border-white/5 pt-4"></div>

                    <Link
                        href="/browse"
                        class="group flex cursor-pointer items-center justify-between rounded-xl px-4 py-3 text-sm text-white/70 transition-all duration-200 hover:bg-white/5 hover:text-white"
                    >
                        <div class="flex items-center gap-3">
                            <Compass class="h-4 w-4 text-[#DBA93E]" />
                            <span>Public Site</span>
                        </div>
                        <ChevronRight
                            class="h-3.5 w-3.5 transform opacity-0 transition-all duration-200 group-hover:translate-x-1 group-hover:opacity-100"
                        />
                    </Link>
                </nav>
            </div>

            <!-- User Status Footer -->
            <div class="border-t border-white/5 bg-black/10 p-4">
                <div v-if="user" class="flex items-center justify-between">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#C8961E] text-xs font-bold text-[#0A1929] uppercase"
                        >
                            {{ user.name.substring(0, 2) }}
                        </div>
                        <div class="overflow-hidden text-left">
                            <h4
                                class="truncate text-xs font-semibold text-white"
                            >
                                {{ user.name }}
                            </h4>
                            <span
                                :class="[
                                    'py-0.2 mt-0.5 inline-block rounded border px-1.5 text-[8px] font-bold tracking-wider uppercase',
                                    getRoleBadgeClass(user.system || ''),
                                ]"
                            >
                                {{ getRoleName(user.system || '') }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="handleLogout"
                        class="cursor-pointer rounded-lg p-2 text-white/40 transition-all hover:bg-white/5 hover:text-red-400"
                        title="Sign Out"
                    >
                        <LogOut class="h-4.5 w-4.5" />
                    </button>
                </div>
            </div>
        </aside>

        <!-- Overlay for Mobile Menu -->
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/50 backdrop-blur-xs lg:hidden"
        ></div>

        <!-- Main Content Area -->
        <div class="flex max-h-screen min-w-0 flex-1 flex-col overflow-y-auto">
            <!-- Top Sticky Header -->
            <header
                class="sticky top-0 z-10 flex items-center justify-between border-b border-black/5 bg-[#F8F6F0]/90 px-6 py-4 backdrop-blur-md lg:px-10"
            >
                <!-- Breadcrumbs & Path Info -->
                <div
                    class="flex items-center gap-2 pl-12 text-xs text-neutral-400 lg:pl-0"
                >
                    <Link
                        href="/admin/dashboard"
                        class="transition-all hover:text-[#C8961E]"
                        >Admin</Link
                    >
                    <ChevronRight class="h-3 w-3 text-neutral-300" />
                    <span
                        v-if="$page.url === '/admin/dashboard'"
                        class="font-semibold text-neutral-700"
                        >Overview</span
                    >
                    <span
                        v-else-if="$page.url === '/admin/lawyers'"
                        class="font-semibold text-neutral-700"
                        >Attorneys Directory</span
                    >
                    <span
                        v-else-if="$page.url === '/admin/cases'"
                        class="font-semibold text-neutral-700"
                        >Cases Management</span
                    >
                    <span
                        v-else-if="$page.url.startsWith('/admin/users')"
                        class="font-semibold text-neutral-700"
                        >User Accounts</span
                    >
                    <span
                        v-else-if="$page.url.startsWith('/admin/audit-logs')"
                        class="font-semibold text-neutral-700"
                        >Activity Trail</span
                    >
                    <span v-else class="font-semibold text-neutral-700"
                        >Details</span
                    >
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-4">
                    <span
                        class="hidden rounded-lg border border-black/5 bg-white px-2.5 py-1 font-mono text-xs text-neutral-500 sm:inline-block"
                    >
                        System Online
                    </span>

                    <!-- Notification Bell Mockup -->
                    <div class="relative">
                        <button
                            class="cursor-pointer rounded-lg p-1.5 text-neutral-400 transition-all hover:bg-black/5 hover:text-neutral-600"
                        >
                            <Bell class="h-4 w-4" />
                            <span
                                class="absolute top-1 right-1 h-1.5 w-1.5 rounded-full bg-[#C8961E]"
                            ></span>
                        </button>
                    </div>

                    <!-- User Quick Info -->
                    <div class="h-6 w-px bg-black/5"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-neutral-700">{{
                            user?.name
                        }}</span>
                    </div>
                </div>
            </header>

            <!-- Inner Page Slot -->
            <main class="flex-1 p-6 lg:p-10">
                <div class="mx-auto max-w-6xl">
                    <slot />
                </div>
            </main>
        </div>
        <ConfirmLogoutModal />
    </div>
</template>
