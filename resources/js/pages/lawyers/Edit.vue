<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Globe, Lock, Shield, Activity, CheckCircle } from 'lucide-vue-next';

interface Lawyer {
    id: number;
    slug: string;
    user_id: number | null;
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

const props = defineProps<{
    lawyer: Lawyer;
}>();

const form = useForm({
    bio: props.lawyer.bio,
    email: props.lawyer.email,
    phone: props.lawyer.phone,
    website: props.lawyer.website,
    linkedin: props.lawyer.linkedin || '',
    availability: props.lawyer.availability,
});

const submitForm = () => {
    form.post(`/profile/lawyer/${props.lawyer.slug}/update`, {
        onSuccess: () => {
            // Success alert / notification is handled by the flash message component
        },
    });
};
</script>

<template>
    <Head title="Manage Lawyer Profile" />

    <div class="flex flex-1 flex-col bg-[#F3EFE8] font-sans text-[#16161A]">
        <!-- Profile Management Hero -->
        <div
            class="relative overflow-hidden bg-[#0A1929] px-6 pt-10 pb-8 text-white md:px-12"
        >
            <div class="relative z-10 mx-auto max-w-5xl text-left">
                <div class="flex flex-col items-start justify-between gap-5 sm:flex-row sm:items-center">
                    <div class="flex flex-col items-start gap-5 sm:flex-row">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-[#C8961E]/20 font-serif text-2xl font-bold text-white"
                            :style="{ backgroundColor: props.lawyer.avatar_color }"
                        >
                            {{ props.lawyer.initials }}
                        </div>
                        <div class="min-w-0">
                            <span
                                class="inline-flex items-center rounded border border-[#C8961E]/22 bg-[#C8961E]/12 px-2 py-0.5 text-[9px] font-bold tracking-wider text-[#F0C96A] uppercase"
                            >
                                Assigned Profile Owner
                            </span>
                            <h1
                                class="mt-1 font-serif text-xl font-bold text-white sm:text-2xl"
                            >
                                Hello, {{ props.lawyer.name }}
                            </h1>
                            <p class="mt-0.5 text-xs text-white/45">
                                Manage your public contact information and bio
                                details below.
                            </p>
                        </div>
                    </div>
                    <div>
                        <Link
                            href="/dashboard"
                            class="inline-flex items-center gap-1 rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-xs font-bold text-white hover:bg-white/10"
                        >
                            ← Back to Dashboard
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Body -->
        <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-8 md:px-12">
            <!-- Success Alert if profile updated -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800"
            >
                <CheckCircle class="h-5 w-5 shrink-0 text-emerald-600" />
                <div>
                    <span class="font-bold">Profile Updated!</span> Your changes
                    have been successfully saved and synchronized with the
                    directory directory.
                </div>
            </div>

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <!-- Left panel: Form fields (updatable) -->
                <div class="space-y-6 lg:col-span-8">
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-6 text-left shadow-sm"
                    >
                        <h4
                            class="mb-5 flex items-center justify-between text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <span>Profile Information Form</span>
                            <span
                                class="text-[10px] font-normal text-neutral-400 lowercase"
                                >* Indicates a required parameter</span
                            >
                        </h4>

                        <form @submit.prevent="submitForm" class="space-y-5">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold text-neutral-700"
                                    >Availability Status *</label
                                >
                                <select
                                    v-model="form.availability"
                                    class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none"
                                >
                                    <option value="available">
                                        Available (Accepting new clients)
                                    </option>
                                    <option value="busy">
                                        Busy (Limited availability)
                                    </option>
                                    <option value="unavailable">
                                        Unavailable (Not accepting cases)
                                    </option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-neutral-700"
                                        >Public Email Address *</label
                                    >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
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
                                        class="mb-1.5 block text-xs font-bold text-neutral-700"
                                        >Telephone Number *</label
                                    >
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.phone"
                                        class="mt-1 text-[10px] text-red-500"
                                    >
                                        {{ form.errors.phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-neutral-700"
                                        >Website URL (domain name) *</label
                                    >
                                    <input
                                        v-model="form.website"
                                        type="text"
                                        placeholder="e.g. b2b.legal"
                                        class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.website"
                                        class="mt-1 text-[10px] text-red-500"
                                    >
                                        {{ form.errors.website }}
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-neutral-700"
                                        >LinkedIn Profile Link</label
                                    >
                                    <input
                                        v-model="form.linkedin"
                                        type="url"
                                        placeholder="e.g. https://linkedin.com/in/username"
                                        class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                    />
                                    <div
                                        v-if="form.errors.linkedin"
                                        class="mt-1 text-[10px] text-red-500"
                                    >
                                        {{ form.errors.linkedin }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold text-neutral-700"
                                    >Professional Biography *</label
                                >
                                <textarea
                                    v-model="form.bio"
                                    rows="8"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                    required
                                ></textarea>
                                <div
                                    v-if="form.errors.bio"
                                    class="mt-1 text-[10px] text-red-500"
                                >
                                    {{ form.errors.bio }}
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-end border-t border-neutral-100 pt-4"
                            >
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="cursor-pointer rounded-lg bg-[#0A1929] px-5 py-2 text-xs font-bold tracking-wider text-white uppercase transition-all hover:bg-[#1E3A54] disabled:opacity-50"
                                >
                                    {{
                                        form.processing
                                            ? 'Saving...'
                                            : 'Update My Profile'
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right panel: Locked statistics (read-only) -->
                <div class="space-y-6 text-left text-sm lg:col-span-4">
                    <!-- Locked Status Warning -->
                    <div
                        class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-neutral-800 shadow-sm"
                    >
                        <div class="flex items-start gap-2.5">
                            <Lock
                                class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                            />
                            <div>
                                <h4
                                    class="text-xs font-bold tracking-wider text-[#7A4F00] uppercase"
                                >
                                    Locked Credentials
                                </h4>
                                <p
                                    class="mt-1 text-[10px] leading-relaxed text-neutral-500"
                                >
                                    Performance analytics, ratings, board
                                    certifications, and legal clearances are
                                    verified by the registry administrator.
                                    These values are read-only and cannot be
                                    manually modified.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Verified Performance Metrics -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Activity class="h-4 w-4 text-neutral-400" /> Locked
                            Statistics
                        </h4>

                        <div class="space-y-3.5">
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Martindale Rating</span
                                >
                                <span class="font-bold text-[#16161A]"
                                    >{{
                                        props.lawyer.rating.toFixed(1)
                                    }}
                                    ★</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Total Cases Handled</span
                                >
                                <span class="font-bold text-neutral-800">{{
                                    props.lawyer.cases_count
                                }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Cases Won / Success</span
                                >
                                <span class="font-semibold text-emerald-600"
                                    >{{ props.lawyer.cases_won }} ({{
                                        props.lawyer.cases_count > 0
                                            ? Math.round(
                                                  (props.lawyer.cases_won /
                                                      props.lawyer
                                                          .cases_count) *
                                                      100,
                                              )
                                            : 0
                                    }}%)</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Active Case Load</span
                                >
                                <span class="font-semibold text-neutral-800"
                                    >{{
                                        props.lawyer.cases_active
                                    }}
                                    active</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Board Certification</span
                                >
                                <span class="font-semibold text-neutral-800">{{
                                    props.lawyer.is_certified
                                        ? 'Business Litigation Certified'
                                        : 'None'
                                }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400"
                                    >Years Active</span
                                >
                                <span class="font-semibold text-neutral-800"
                                    >{{
                                        props.lawyer.years_experience
                                    }}
                                    Years</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Verified Clearances -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-4 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Shield class="h-4 w-4 text-neutral-400" />
                            Background Clearances
                        </h4>

                        <div class="space-y-3.5">
                            <div
                                class="flex items-center justify-between border-b border-neutral-100 pb-2"
                            >
                                <span class="text-xs text-neutral-400"
                                    >Criminal Background</span
                                >
                                <span
                                    class="text-xs font-bold text-emerald-600 uppercase"
                                    >{{ props.lawyer.criminal_record }}</span
                                >
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-neutral-400"
                                    >Ethics Bar Record</span
                                >
                                <span
                                    class="text-xs font-bold text-emerald-600 uppercase"
                                    >{{ props.lawyer.bar_discipline }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- practice areas -->
                    <div
                        class="rounded-2xl border border-black/5 bg-white p-5 shadow-sm"
                    >
                        <h4
                            class="mb-3 flex items-center gap-1.5 text-xs font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            <Globe class="h-4 w-4 text-neutral-400" /> Active
                            Practice Areas
                        </h4>
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            <span
                                v-for="area in props.lawyer.practice_areas"
                                :key="area"
                                class="rounded border border-black/5 bg-[#F3EFE8] px-2 py-0.5 text-[10px] font-semibold text-neutral-600"
                            >
                                {{ area }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
