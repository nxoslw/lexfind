<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    Scale,
    ShieldAlert,
    HelpCircle,
    Home,
    Search,
    AlertTriangle,
    Wrench
} from 'lucide-vue-next';

const props = defineProps<{
    status: number;
}>();

const errorDetails = computed(() => {
    switch (props.status) {
        case 403:
            return {
                title: 'Access Restricted',
                subtitle: 'You do not have authorization to view this area.',
                description: 'This section of the LexFind command center is reserved for verified administrators or specific credential holders.',
                icon: ShieldAlert,
                iconColor: 'text-red-500 bg-red-500/10 border-red-500/20',
            };
        case 500:
            return {
                title: 'System Interrupted',
                subtitle: 'Something went wrong on our servers.',
                description: 'Our system integrity monitoring has recorded this exception and notified the technical ops team.',
                icon: AlertTriangle,
                iconColor: 'text-amber-500 bg-amber-500/10 border-amber-500/20',
            };
        case 503:
            return {
                title: 'Under Maintenance',
                subtitle: 'The legal registry is temporarily offline.',
                description: 'We are performing scheduled updates to our case indices and database systems. Please check back shortly.',
                icon: Wrench,
                iconColor: 'text-sky-500 bg-sky-500/10 border-sky-500/20',
            };
        case 404:
        default:
            return {
                title: 'Lost in the Registry ?',
                subtitle: "We couldn't find the page you were looking for.",
                description: 'The URL may be misspelled, the attorney profile might have been archived, or the directory reference is expired.',
                icon: HelpCircle,
                iconColor: 'text-[#C8961E] bg-[#C8961E]/10 border-[#C8961E]/20',
            };
    }
});
</script>

<template>
    <Head :title="`${props.status} — ${errorDetails.title}`" />

    <div class="flex flex-1 flex-col items-center justify-center bg-[#F8F6F0] px-6 py-20 font-sans text-[#16161A]">
        <div class="relative w-full max-w-lg text-center space-y-8 animate-[fadeUp_0.2s_ease-out]">
            
            <!-- Graphic Element and Icon -->
            <div class="relative flex justify-center">
                <div class="pointer-events-none absolute h-40 w-40 rounded-full bg-gradient-to-br from-[#C8961E]/8 to-transparent blur-2xl"></div>
                <div
                    :class="[
                        'relative z-10 flex h-20 w-20 items-center justify-center rounded-2xl border-2 p-4 shadow-sm transition-transform duration-500 hover:rotate-6',
                        errorDetails.iconColor
                    ]"
                >
                    <component :is="errorDetails.icon" class="h-10 w-10 shrink-0" />
                </div>
            </div>

            <!-- Big Status Code -->
            <div class="space-y-2">
                <h1 class="font-serif text-8xl font-black tracking-tighter text-[#0A1929] select-none">
                    {{ props.status }}
                </h1>
                <div class="mx-auto h-1 w-16 bg-[#C8961E] rounded"></div>
            </div>

            <!-- Error Messages -->
            <div class="space-y-3">
                <h2 class="font-serif text-2xl font-bold tracking-tight text-[#0A1929] sm:text-3xl">
                    {{ errorDetails.title }}
                </h2>
                <h3 class="text-sm font-semibold text-neutral-600">
                    {{ errorDetails.subtitle }}
                </h3>
                <p class="mx-auto max-w-md text-xs leading-relaxed text-neutral-400">
                    {{ errorDetails.description }}
                </p>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-col items-center justify-center gap-3 sm:flex-row pt-4">
                <Link
                    href="/"
                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0A1929] px-6 py-3 text-xs font-bold tracking-wider text-white uppercase shadow-md transition-all hover:bg-[#1E3A54] sm:w-auto"
                >
                    <Home class="h-4 w-4" />
                    Return Home
                </Link>
                <Link
                    href="/browse"
                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-black/10 bg-white px-6 py-3 text-xs font-bold tracking-wider text-neutral-700 transition-all hover:bg-neutral-50 sm:w-auto"
                >
                    <Search class="h-4 w-4 text-neutral-400" />
                    Search Directory
                </Link>
            </div>

            <!-- Branding footer markup -->
            <div class="pt-8 flex items-center justify-center gap-1.5 text-[9px] font-bold tracking-wider text-neutral-300 uppercase">
                <Scale class="h-3.5 w-3.5" /> LexFind Security Core
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
