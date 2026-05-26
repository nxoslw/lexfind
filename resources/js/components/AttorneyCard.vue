<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

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
    practice_areas: string[];
}

defineProps<{
    lawyer: Lawyer;
}>();
</script>

<template>
    <Link
        :href="`/lawyers/${lawyer.slug}`"
        class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-black/5 bg-white p-5 transition-all hover:translate-y-[-2px] hover:border-[#0A1929] hover:shadow-xl"
    >
        <!-- Availability indicator dot -->
        <div
            :class="[
                'absolute top-4 right-4 h-2 w-2 rounded-full shadow-xs',
                lawyer.availability === 'available'
                    ? 'bg-emerald-500'
                    : lawyer.availability === 'busy'
                      ? 'bg-amber-500'
                      : 'bg-red-500',
            ]"
        ></div>

        <div>
            <!-- Top Details -->
            <div class="mb-4 flex items-start gap-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl font-serif text-lg font-bold text-white shadow-inner"
                    :style="{ backgroundColor: lawyer.avatar_color }"
                >
                    {{ lawyer.initials }}
                </div>
                <div class="min-w-0 text-left">
                    <h3
                        class="truncate text-sm leading-snug font-bold text-neutral-800"
                    >
                        {{ lawyer.name }}
                    </h3>
                    <p class="truncate text-xs leading-snug text-neutral-400">
                        {{ lawyer.title }} · {{ lawyer.city }},
                        {{ lawyer.state }}
                    </p>
                    <div
                        class="mt-1.5 flex items-center gap-1 text-xs text-[#C8961E]"
                    >
                        <span class="stars font-mono">★</span>&nbsp;<span
                            class="font-bold text-neutral-800"
                            >{{ lawyer.rating.toFixed(1) }}</span
                        >
                        <span class="font-normal text-neutral-400"
                            >· {{ lawyer.years_experience }} yrs exp</span
                        >
                        <span
                            v-if="lawyer.is_certified"
                            class="ml-1.5 text-[10px] font-bold tracking-wider text-[#C8961E] uppercase"
                            >✓ Certified</span
                        >
                    </div>
                </div>
            </div>

            <!-- Specialties chips -->
            <div class="mb-4 flex flex-wrap gap-1.5">
                <span
                    class="rounded-full bg-[#0A1929] px-2.5 py-0.5 text-[10px] font-bold text-[#DBA93E]"
                >
                    {{ lawyer.practice_areas[0] }}
                </span>
                <span
                    v-if="lawyer.practice_areas[1]"
                    class="rounded-full bg-[#F3EFE8] px-2.5 py-0.5 text-[10px] font-medium text-neutral-600"
                >
                    {{ lawyer.practice_areas[1] }}
                </span>
            </div>

            <!-- Stats Row -->
            <div
                class="grid grid-cols-3 gap-1 border-t border-black/5 pt-4 text-center"
            >
                <div>
                    <p class="font-serif text-base font-bold text-[#16161A]">
                        {{ lawyer.cases_count.toLocaleString() }}+
                    </p>
                    <p
                        class="text-[9px] tracking-wider text-neutral-400 uppercase"
                    >
                        Cases
                    </p>
                </div>
                <div>
                    <p class="font-serif text-base font-bold text-[#16161A]">
                        {{
                            lawyer.cases_count > 0
                                ? Math.round(
                                      (lawyer.cases_won / lawyer.cases_count) *
                                          100,
                                  )
                                : 0
                        }}%
                    </p>
                    <p
                        class="text-[9px] tracking-wider text-neutral-400 uppercase"
                    >
                        Win Rate
                    </p>
                </div>
                <div>
                    <p class="font-serif text-base font-bold text-[#16161A]">
                        {{ lawyer.rating }}★
                    </p>
                    <p
                        class="text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                    >
                        Rating
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Bottom Actions & Pricing -->
        <div
            class="mt-4 flex items-center justify-between border-t border-black/5 pt-4 text-xs font-semibold"
        >
            <div class="text-left text-neutral-600">
                {{
                    lawyer.fee_structure
                        ? lawyer.fee_structure.split('/')[0].trim()
                        : 'Contact for Fees'
                }}
                <span class="font-normal text-neutral-400"
                    >·
                    {{
                        lawyer.availability === 'available'
                            ? 'Available now'
                            : lawyer.availability === 'busy'
                              ? 'Waitlist'
                              : 'Unavailable'
                    }}</span
                >
            </div>
            <button
                class="cursor-pointer rounded-lg bg-[#0A1929] px-3.5 py-1.5 font-semibold text-[#DBA93E] transition-all hover:bg-[#1E3A54]"
            >
                View Profile →
            </button>
        </div>
    </Link>
</template>
