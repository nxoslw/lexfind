<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Search,
    ChevronDown,
    LogOut,
    Sliders,
    User,
    Menu,
    X,
    ChevronRight,
    LayoutDashboard,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AuthModal from '@/components/AuthModal.vue';
import ConfirmLogoutModal from '@/components/ConfirmLogoutModal.vue';
import { openAuthModal } from '@/composables/useAuthModal';
import { useConfirmLogout } from '@/composables/useConfirmLogout';

import type { User as UserType } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth.user as UserType);
const isDropdownOpen = ref(false);
const isMobileMenuOpen = ref(false);
const { triggerLogout } = useConfirmLogout();

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const handleLogout = () => {
    triggerLogout();
};

// Auto-compute breadcrumbs based on active URL and route parameters
const breadcrumbs = computed(() => {
    const url = page.url;
    const props = page.props as any;
    const list = [{ title: 'Home', href: '/' }];

    if (url === '/' || url === '/dashboard') {
        return [];
    }

    if (url.startsWith('/browse')) {
        list.push({ title: 'Find a Lawyer', href: '/browse' });
    } else if (url.startsWith('/lawyers/')) {
        list.push({ title: 'Find a Lawyer', href: '/browse' });

        if (props.lawyer?.name) {
            list.push({
                title: props.lawyer.name,
                href: `/lawyers/${props.lawyer.slug}`,
            });
        }
    } else if (url.startsWith('/cases/')) {
        list.push({ title: 'Find a Lawyer', href: '/browse' });

        if (props.case?.name) {
            list.push({
                title: props.case.name,
                href: `/cases/${props.case.slug}`,
            });
        }
    } else if (url.startsWith('/profile/lawyer')) {
        list.push({ title: 'Edit My Profile', href: '/profile/lawyer' });
    }

    return list;
});
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col overflow-x-hidden bg-[#F8F6F0] font-sans text-[#16161A]"
    >
        <!-- Navigation Header -->
        <nav
            class="relative z-30 flex items-center justify-between border-b border-white/5 bg-[#0A1929] px-6 py-4 text-white md:px-12"
        >
            <div class="flex min-w-0 items-center gap-6">
                <Link
                    href="/"
                    class="shrink-0 font-serif text-2xl font-bold tracking-wide text-[#DBA93E]"
                >
                    Lex<span class="font-normal text-white">Find</span>
                </Link>

                <!-- Desktop Middle Navigation (Homepage only) -->
                <div
                    v-if="page.url.split('?')[0] === '/'"
                    class="hidden items-center gap-8 text-xs font-bold tracking-wider text-white/50 uppercase md:flex"
                >
                    <a
                        href="#how-it-works"
                        class="transition-colors hover:text-white"
                        >How it works</a
                    >
                    <a
                        href="#features"
                        class="transition-colors hover:text-white"
                        >For Clients</a
                    >
                    <a
                        href="#attorneys"
                        class="transition-colors hover:text-white"
                        >For Attorneys</a
                    >
                    <a
                        href="#pricing"
                        class="transition-colors hover:text-white"
                        >Pricing</a
                    >
                </div>

                <!-- Dynamic Breadcrumbs (Other pages) -->
                <div
                    v-else-if="breadcrumbs.length > 0"
                    class="breadcrumb hidden max-w-md items-center gap-1.5 overflow-hidden text-xs text-white/40 md:flex"
                >
                    <template v-for="(item, idx) in breadcrumbs" :key="idx">
                        <Link
                            v-if="item.href && idx < breadcrumbs.length - 1"
                            :href="item.href"
                            class="whitespace-nowrap transition-all hover:text-[#DBA93E]"
                        >
                            {{ item.title }}
                        </Link>
                        <span
                            v-else
                            class="max-w-[150px] truncate font-medium text-white/60"
                            :title="item.title"
                        >
                            {{ item.title }}
                        </span>
                        <ChevronRight
                            v-if="idx < breadcrumbs.length - 1"
                            class="h-3 w-3 shrink-0 text-white/20"
                        />
                    </template>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center gap-4">
                <Link
                    href="/browse"
                    class="group relative hidden cursor-pointer items-center gap-2 overflow-hidden rounded-full border-2 border-[#DBA93E]/30 bg-[#DBA93E]/5 px-4 py-2 text-[11px] font-bold tracking-wider text-[#DBA93E] uppercase shadow-[0_0_15px_rgba(219,169,62,0.02)] transition-all duration-300 hover:scale-[1.02] hover:border-[#DBA93E]/60 hover:bg-[#DBA93E]/15 hover:shadow-[0_0_20px_rgba(219,169,62,0.12)] sm:inline-flex"
                >
                    <span
                        class="absolute inset-0 h-full w-full -translate-x-full bg-gradient-to-r from-transparent via-white/5 to-transparent transition-transform duration-1000 ease-out group-hover:translate-x-full"
                    ></span>

                    <Search
                        class="h-3.5 w-3.5 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6"
                    />
                    <span>Browse Directory</span>
                </Link>

                <!-- Authenticated User Menu Dropdown (Desktop) -->
                <div v-if="user" class="relative hidden md:block">
                    <button
                        @click="toggleDropdown"
                        class="flex cursor-pointer items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs transition-all hover:bg-white/10"
                    >
                        <div
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[#C8961E] text-xs font-bold text-[#0A1929] uppercase shadow-inner"
                        >
                            {{ user.name.substring(0, 2) }}
                        </div>
                        <span
                            class="max-w-[100px] truncate font-medium text-white/90"
                            >{{ user.name }}</span
                        >
                        <ChevronDown class="h-3.5 w-3.5 text-white/40" />
                    </button>

                    <div
                        v-if="isDropdownOpen"
                        class="absolute right-0 z-40 mt-2 w-52 rounded-xl border border-white/10 bg-[#0F2234] py-1 text-xs shadow-2xl"
                    >
                        <div
                            class="border-b border-white/5 bg-white/5 px-4 py-2 text-left"
                        >
                            <p class="truncate font-semibold text-white">
                                {{ user.name }}
                            </p>
                            <p class="truncate text-[10px] text-white/40">
                                {{ user.email }}
                            </p>
                        </div>
                        <!-- Admin Dashboard for staff roles -->
                        <Link
                            v-if="
                                ['ghost', 'simp', 'bat'].includes(
                                    user.system || '',
                                )
                            "
                            href="/admin/dashboard"
                            class="flex items-center gap-2 px-4 py-2.5 text-left text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <Sliders class="h-3.5 w-3.5 text-[#DBA93E]" /> Admin
                            Dashboard
                        </Link>
                        <!-- Client & Moderator Dashboard -->
                        <Link
                            v-if="
                                !['ghost', 'simp', 'bat'].includes(
                                    user.system || '',
                                )
                            "
                            href="/dashboard"
                            class="flex items-center gap-2 px-4 py-2.5 text-left text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <LayoutDashboard class="h-3.5 w-3.5 text-[#DBA93E]" /> Dashboard
                        </Link>
                        <Link
                            v-if="user.has_lawyer_profile"
                            href="/profile/lawyer"
                            class="flex items-center gap-2 px-4 py-2.5 text-left text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <User class="h-3.5 w-3.5 text-[#DBA93E]" /> Edit
                            Lawyer Profile
                        </Link>
                        <button
                            @click="handleLogout"
                            class="flex w-full cursor-pointer items-center gap-2 px-4 py-2.5 text-left text-red-400 transition-all hover:bg-red-500/10 hover:text-red-300"
                        >
                            <LogOut class="h-3.5 w-3.5" /> Sign Out
                        </button>
                    </div>
                </div>

                <!-- Guest Sign In / Join Buttons (Desktop) -->
                <template v-else>
                    <div class="hidden items-center gap-4 md:flex">
                        <button
                            @click="openAuthModal('login')"
                            class="cursor-pointer text-xs font-bold tracking-wider text-white/70 uppercase hover:text-white"
                        >
                            Sign In
                        </button>
                        <button
                            @click="openAuthModal('signup')"
                            class="cursor-pointer rounded-lg bg-[#C8961E] px-3.5 py-2 text-xs font-bold tracking-wider text-[#0A1929] uppercase shadow-md shadow-[#C8961E]/20 transition-all hover:scale-[1.01] hover:bg-[#DBA93E]"
                        >
                            Join Free
                        </button>
                    </div>
                </template>

                <!-- Mobile Hamburger Button -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="cursor-pointer p-2 text-white/80 transition-all hover:text-white focus:outline-none md:hidden"
                >
                    <X v-if="isMobileMenuOpen" class="h-6.5 w-6.5" />
                    <Menu v-else class="h-6.5 w-6.5" />
                </button>
            </div>
        </nav>

        <!-- Mobile Dropdown Drawer -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform -translate-y-4 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-4 opacity-0"
        >
            <div
                v-if="isMobileMenuOpen"
                class="relative z-25 space-y-6 border-b border-white/10 bg-[#0A1929] px-6 py-6 text-left text-xs text-white md:hidden"
            >
                <!-- Navigation links -->
                <div class="flex flex-col gap-4">
                    <a
                        @click="isMobileMenuOpen = false"
                        href="/#how-it-works"
                        class="border-b border-white/5 py-1 font-bold tracking-wider text-white/60 uppercase transition-all hover:text-white"
                        >How it works</a
                    >
                    <a
                        @click="isMobileMenuOpen = false"
                        href="/#features"
                        class="border-b border-white/5 py-1 font-bold tracking-wider text-white/60 uppercase transition-all hover:text-white"
                        >For Clients</a
                    >
                    <a
                        @click="isMobileMenuOpen = false"
                        href="/#attorneys"
                        class="border-b border-white/5 py-1 font-bold tracking-wider text-white/60 uppercase transition-all hover:text-white"
                        >For Attorneys</a
                    >
                    <a
                        @click="isMobileMenuOpen = false"
                        href="/#pricing"
                        class="border-b border-white/5 py-1 font-bold tracking-wider text-white/60 uppercase transition-all hover:text-white"
                        >Pricing</a
                    >
                    <Link
                        @click="isMobileMenuOpen = false"
                        href="/browse"
                        class="py-1 font-bold tracking-wider text-white/60 uppercase transition-all hover:text-white"
                        >Browse Directory</Link
                    >
                </div>

                <!-- Authenticated / Guest Actions -->
                <div
                    v-if="user"
                    class="space-y-4 border-t border-white/10 pt-4"
                >
                    <div class="px-2">
                        <p class="truncate font-semibold text-white">
                            {{ user.name }}
                        </p>
                        <p class="mt-0.5 truncate text-[10px] text-white/40">
                            {{ user.email }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Link
                            v-if="
                                ['ghost', 'simp', 'bat'].includes(
                                    user.system || '',
                                )
                            "
                            @click="isMobileMenuOpen = false"
                            href="/admin/dashboard"
                            class="flex items-center gap-2 rounded-lg px-2 py-2 text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <Sliders class="h-4 w-4 text-[#DBA93E]" /> Admin
                            Dashboard
                        </Link>
                        <!-- Client & Moderator Dashboard -->
                        <Link
                            v-if="
                                !['ghost', 'simp', 'bat'].includes(
                                    user.system || '',
                                )
                            "
                            @click="isMobileMenuOpen = false"
                            href="/dashboard"
                            class="flex items-center gap-2 rounded-lg px-2 py-2 text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <LayoutDashboard class="h-4 w-4 text-[#DBA93E]" /> Dashboard
                        </Link>
                        <Link
                            v-if="user.has_lawyer_profile"
                            @click="isMobileMenuOpen = false"
                            href="/profile/lawyer"
                            class="flex items-center gap-2 rounded-lg px-2 py-2 text-white/80 transition-all hover:bg-white/5 hover:text-white"
                        >
                            <User class="h-4 w-4 text-[#DBA93E]" /> Edit Lawyer
                            Profile
                        </Link>
                        <button
                            @click="
                                isMobileMenuOpen = false;
                                handleLogout();
                            "
                            class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-2 py-2 text-left text-red-400 transition-all hover:bg-red-500/10 hover:text-red-300"
                        >
                            <LogOut class="h-4 w-4" /> Sign Out
                        </button>
                    </div>
                </div>
                <div
                    v-else
                    class="flex flex-col gap-3 border-t border-white/10 pt-4"
                >
                    <button
                        @click="
                            isMobileMenuOpen = false;
                            openAuthModal('login');
                        "
                        class="w-full cursor-pointer rounded-lg border border-white/10 py-3 text-center font-bold tracking-wider uppercase transition-all hover:bg-white/5"
                    >
                        Sign In
                    </button>
                    <button
                        @click="
                            isMobileMenuOpen = false;
                            openAuthModal('signup');
                        "
                        class="w-full cursor-pointer rounded-lg bg-[#C8961E] py-3 text-center font-bold tracking-wider text-[#0A1929] uppercase shadow-md shadow-[#C8961E]/20 transition-all hover:bg-[#DBA93E]"
                    >
                        Join Free
                    </button>
                </div>
            </div>
        </transition>

        <!-- Main Body Wrapper -->
        <div class="flex min-w-0 flex-1 flex-col">
            <slot />
        </div>

        <!-- Persistent Layout Footer -->
        <footer
            class="border-t border-white/5 bg-[#0A1929] px-6 py-12 text-left text-xs text-white/60 lg:px-12"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 md:flex-row"
            >
                <div class="space-y-3">
                    <h3 class="font-serif text-lg font-bold text-[#DBA93E]">
                        LexFind
                    </h3>
                    <p class="max-w-sm text-xs leading-relaxed text-white/30">
                        The verified attorney intelligence platform. Real case
                        records, win rate stats, and conduct monitoring.
                        <br />© 2026 LexFind, Inc. All rights reserved.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-12">
                    <div>
                        <h5
                            class="mb-3 text-xs font-bold tracking-wider text-white/30 uppercase"
                        >
                            Product
                        </h5>
                        <ul class="space-y-2">
                            <li>
                                <Link
                                    href="/browse"
                                    class="transition-all hover:text-white/70"
                                    >Find a Lawyer</Link
                                >
                            </li>
                            <li>
                                <a
                                    href="#attorneys"
                                    class="transition-all hover:text-white/70"
                                    >For Attorneys</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#pricing"
                                    class="transition-all hover:text-white/70"
                                    >Pricing</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h5
                            class="mb-3 text-xs font-bold tracking-wider text-white/30 uppercase"
                        >
                            Company
                        </h5>
                        <ul class="space-y-2">
                            <li>
                                <a
                                    href="#"
                                    class="transition-all hover:text-white/70"
                                    >About</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all hover:text-white/70"
                                    >Blog</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h5
                            class="mb-3 text-xs font-bold tracking-wider text-white/30 uppercase"
                        >
                            Legal
                        </h5>
                        <ul class="space-y-2">
                            <li>
                                <a
                                    href="#"
                                    class="transition-all hover:text-white/70"
                                    >Privacy</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="transition-all hover:text-white/70"
                                    >Terms</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Shared Auth Modal popup -->
        <AuthModal />
        <ConfirmLogoutModal />
    </div>
</template>
