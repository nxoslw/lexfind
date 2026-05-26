<script setup lang="ts">
import { X, AlertTriangle, LogOut } from 'lucide-vue-next';
import { useConfirmLogout } from '@/composables/useConfirmLogout';

const { isConfirmOpen, confirm, cancel } = useConfirmLogout();
</script>

<template>
    <Teleport to="body">
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isConfirmOpen"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <!-- Glassmorphism Overlay backdrop -->
                <div
                    class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs"
                    @click="cancel"
                ></div>

                <!-- Premium Dialog Box -->
                <div
                    class="relative z-10 flex w-full max-w-[380px] flex-col items-center rounded-2xl border border-slate-200/50 bg-white p-6 text-center text-[#16161A] shadow-2xl dark:border-slate-800 dark:bg-slate-900 dark:text-neutral-200"
                >
                    <!-- Close Button -->
                    <button
                        @click="cancel"
                        class="absolute top-4 right-4 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full border-none bg-slate-100 text-slate-400 transition-all hover:bg-slate-200 hover:text-slate-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                    >
                        <X class="h-4 w-4" />
                    </button>

                    <!-- Warning Icon with animated pulse -->
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-500 ring-4 ring-amber-500/10 dark:bg-amber-950/30"
                    >
                        <AlertTriangle class="h-6 w-6 animate-pulse" />
                    </div>

                    <!-- Title & Description -->
                    <h3
                        class="mb-2 font-serif text-lg font-bold text-slate-800 dark:text-neutral-100"
                    >
                        Confirm Sign Out
                    </h3>
                    <p
                        class="mb-6 max-w-[280px] text-xs text-slate-500 dark:text-neutral-400"
                    >
                        Are you sure you want to log out of your LexFind
                        account? You will need to sign back in to manage
                        profiles or view case details.
                    </p>

                    <!-- Actions Grid -->
                    <div class="grid w-full grid-cols-2 gap-3">
                        <button
                            @click="cancel"
                            class="w-full cursor-pointer rounded-xl border-none bg-slate-100 py-2.5 text-xs font-semibold text-slate-600 transition-all hover:bg-slate-200 dark:bg-slate-800 dark:text-neutral-300 dark:hover:bg-slate-700"
                        >
                            Keep Logged In
                        </button>
                        <button
                            @click="confirm"
                            class="flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-xl border-none bg-[#0A1929] py-2.5 text-xs font-semibold text-[#DBA93E] shadow-md shadow-[#0A1929]/20 transition-all hover:bg-[#1E3A54] hover:text-white"
                        >
                            <LogOut class="h-3.5 w-3.5" />
                            Sign Out
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>
