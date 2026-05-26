<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Scale,
    Search,
    Shield,
    Star,
    Calendar,
    MessageSquare,
    ArrowRight,
    Check,
    FileText,
    TrendingUp,
    Lock,
    Award,
} from 'lucide-vue-next';
import { openAuthModal } from '@/composables/useAuthModal';

defineProps<{
    lawyersCount: number;
    casesCount: number;
}>();
</script>

<template>
    <Head title="LexFind — Verified Lawyer Directory & Intelligence" />

    <div
        class="relative flex flex-1 flex-col overflow-hidden bg-[#0A1929] pb-20 font-sans text-white select-none"
    >
        <!-- Background Orbs -->
        <div
            class="pointer-events-none absolute top-[-10%] right-[-5%] h-[500px] w-[500px] rounded-full bg-gradient-to-br from-[#C8961E]/5 to-transparent blur-3xl"
        ></div>
        <div
            class="pointer-events-none absolute bottom-[10%] left-[-5%] h-[400px] w-[400px] rounded-full bg-gradient-to-br from-[#0F3E8F]/10 to-transparent blur-3xl"
        ></div>

        <!-- Hero Section -->
        <main
            class="relative z-10 mx-auto flex w-full max-w-7xl flex-1 flex-col justify-center px-6 py-12 md:px-12"
        >
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <!-- Left text content -->
                <div class="flex flex-col items-start text-left lg:col-span-7">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#C8961E]/20 bg-[#C8961E]/12 px-4 py-1.5 text-xs font-semibold tracking-wider text-[#F0C96A] uppercase"
                    >
                        <span
                            class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#C8961E]"
                        ></span>
                        Trusted by 12,000+ clients in 2026
                    </div>
                    <h1
                        class="mb-6 font-serif text-4xl leading-[1.1] font-bold text-white sm:text-5xl md:text-6xl"
                    >
                        The Smarter Way<br />to Find
                        <span class="font-semibold text-[#DBA93E] italic"
                            >Verified Legal Help.</span
                        >
                    </h1>
                    <p
                        class="mb-8 max-w-xl text-base leading-relaxed text-white/60 md:text-lg"
                    >
                        LexFind connects you with {{ lawyersCount }}+
                        bar-verified attorneys — complete with real case win
                        rates, court records, peer reviews, and live
                        availability. Stop guessing. Start winning.
                    </p>
                    <div class="mb-8 flex flex-wrap gap-4">
                        <Link
                            href="/browse"
                            class="flex items-center gap-2 rounded-xl bg-[#C8961E] px-6 py-3.5 text-base font-bold text-[#0A1929] shadow-xl shadow-[#C8961E]/20 transition-all hover:translate-y-[-1px] hover:bg-[#DBA93E]"
                        >
                            Find My Lawyer — It's Free
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <button
                            v-if="!$page.props.auth.user"
                            @click="openAuthModal('signup')"
                            class="flex cursor-pointer items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-6 py-3.5 text-base font-semibold transition-all hover:bg-white/10"
                        >
                            I'm an Attorney →
                        </button>
                    </div>
                    <div
                        class="flex flex-col gap-4 text-xs text-white/40 sm:flex-row sm:gap-8"
                    >
                        <div class="flex items-center gap-1.5">
                            <Check class="h-4 w-4 text-[#DBA93E]" /> Free to
                            join & search
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Check class="h-4 w-4 text-[#DBA93E]" /> No ads, no
                            referral fees
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Check class="h-4 w-4 text-[#DBA93E]" />
                            Bar-verified attorneys only
                        </div>
                    </div>
                </div>

                <!-- Right preview card -->
                <div class="relative lg:col-span-5">
                    <!-- Preview Lawyer Card -->
                    <div
                        class="relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur-xl"
                    >
                        <!-- Blurred details content under lock -->
                        <div
                            class="pointer-events-none absolute inset-0 z-10 bg-gradient-to-t from-[#0A1929] via-transparent to-transparent"
                        ></div>

                        <div class="mb-4 flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-[#C8961E]/20 bg-[#1E3A54] font-serif text-lg font-bold text-[#DBA93E]"
                            >
                                BB
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4
                                    class="truncate text-sm font-semibold text-white"
                                >
                                    Brian F. Barakat, Esq.
                                </h4>
                                <p class="truncate text-xs text-white/40">
                                    Barakat + Bossa, PLLC · Coral Gables, FL
                                </p>
                                <div
                                    class="mt-1 flex items-center gap-1 text-xs text-[#DBA93E]"
                                >
                                    <Star
                                        class="text-none h-3.5 w-3.5 fill-[#DBA93E]"
                                    />
                                    <Star
                                        class="text-none h-3.5 w-3.5 fill-[#DBA93E]"
                                    />
                                    <Star
                                        class="text-none h-3.5 w-3.5 fill-[#DBA93E]"
                                    />
                                    <Star
                                        class="text-none h-3.5 w-3.5 fill-[#DBA93E]"
                                    />
                                    <Star
                                        class="text-none h-3.5 w-3.5 fill-[#DBA93E]"
                                    />
                                    <span
                                        class="ml-1 font-semibold text-white/50"
                                        >5.0 · 25 yrs exp</span
                                    >
                                </div>
                            </div>
                            <div
                                class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/25"
                            ></div>
                        </div>

                        <div class="mb-4 flex flex-wrap gap-2">
                            <span
                                class="rounded-full border border-[#C8961E]/25 bg-[#C8961E]/15 px-2.5 py-1 text-xs font-medium text-[#F0C96A]"
                                >Business Litigation</span
                            >
                            <span
                                class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-white/50"
                                >Corporate Law</span
                            >
                            <span
                                class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-white/50"
                                >✓ Board Certified</span
                            >
                        </div>

                        <div
                            class="mb-4 grid grid-cols-3 gap-2 border-t border-white/5 pt-4 text-center"
                        >
                            <div>
                                <p
                                    class="font-serif text-lg font-bold text-white"
                                >
                                    1,600+
                                </p>
                                <p
                                    class="text-[10px] tracking-wider text-white/40 uppercase"
                                >
                                    Cases
                                </p>
                            </div>
                            <div>
                                <p
                                    class="font-serif text-lg font-bold text-[#DBA93E]"
                                >
                                    97%
                                </p>
                                <p
                                    class="text-[10px] tracking-wider text-white/40 uppercase"
                                >
                                    Win Rate
                                </p>
                            </div>
                            <div>
                                <p
                                    class="font-serif text-lg font-bold text-white"
                                >
                                    $50M+
                                </p>
                                <p
                                    class="text-[10px] tracking-wider text-white/40 uppercase"
                                >
                                    Recovered
                                </p>
                            </div>
                        </div>

                        <!-- Locked overlay -->
                        <div
                            class="relative z-10 mt-4 rounded-xl border border-white/5 bg-[#0A1929]/90 p-5 text-center"
                        >
                            <Lock class="mx-auto mb-2 h-7 w-7 text-[#DBA93E]" />
                            <h5 class="mb-1 text-sm font-semibold text-white">
                                Full profile available after registration
                            </h5>
                            <p class="mb-4 text-xs text-white/50">
                                View complete case history, contact details,
                                performance statistics & more.
                            </p>
                            <button
                                @click="openModal('signup')"
                                class="cursor-pointer rounded-lg bg-[#C8961E] px-4 py-2.5 text-xs font-bold text-[#0A1929] transition-all hover:bg-[#DBA93E]"
                            >
                                Create Free Account
                            </button>
                        </div>
                    </div>

                    <!-- Floating Badges -->
                    <div
                        class="absolute top-[-10px] left-[-30px] hidden animate-[bounce_4s_infinite] rounded-full border border-white/10 bg-[#0A1929]/95 px-4 py-2 text-xs font-semibold text-white/80 shadow-xl lg:block"
                    >
                        ⚖️ {{ lawyersCount }} Verified Attorneys
                    </div>
                    <div
                        class="absolute bottom-[60px] left-[-40px] hidden animate-[bounce_4s_infinite_1.5s] rounded-full border border-white/10 bg-[#0A1929]/95 px-4 py-2 text-xs font-semibold text-white/80 shadow-xl lg:block"
                    >
                        📁 1.2M+ Cases on Record
                    </div>
                    <div
                        class="absolute top-[35%] right-[-40px] hidden animate-[bounce_4s_infinite_3s] rounded-full border border-white/10 bg-[#0A1929]/95 px-4 py-2 text-xs font-semibold text-white/80 shadow-xl lg:block"
                    >
                        🏆 Avg. 4.8★ Peer Rating
                    </div>
                </div>
            </div>
        </main>

        <!-- Stats Strip -->
        <section class="border-y border-white/5 bg-black/20 py-6">
            <div
                class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-6 text-center md:grid-cols-5"
            >
                <div>
                    <h3 class="font-serif text-2xl font-bold text-[#DBA93E]">
                        {{ lawyersCount }}
                    </h3>
                    <p
                        class="mt-1 text-xs tracking-wider text-white/35 uppercase"
                    >
                        Verified Attorneys
                    </p>
                </div>
                <div
                    class="hidden h-8 w-px self-center bg-white/10 md:block"
                ></div>
                <div>
                    <h3 class="font-serif text-2xl font-bold text-white">
                        50 States
                    </h3>
                    <p
                        class="mt-1 text-xs tracking-wider text-white/35 uppercase"
                    >
                        + Federal Courts
                    </p>
                </div>
                <div
                    class="hidden h-8 w-px self-center bg-white/10 md:block"
                ></div>
                <div>
                    <h3 class="font-serif text-2xl font-bold text-[#DBA93E]">
                        1.2M+
                    </h3>
                    <p
                        class="mt-1 text-xs tracking-wider text-white/35 uppercase"
                    >
                        Cases on Record
                    </p>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section
            id="how-it-works"
            class="bg-[#F3EFE8] px-6 py-20 text-[#16161A]"
        >
            <div class="mx-auto max-w-5xl">
                <span
                    class="mb-4 inline-block rounded-full border border-[#0A1929]/10 bg-[#0A1929]/5 px-4 py-1 text-xs font-bold tracking-wider text-[#0A1929] uppercase"
                    >How LexFind Works</span
                >
                <h2
                    class="mb-4 font-serif text-3xl font-bold text-[#0A1929] md:text-4xl"
                >
                    From search to signed — in minutes.
                </h2>
                <p
                    class="mb-12 max-w-xl text-base leading-relaxed text-neutral-500"
                >
                    Unlike directories that show outdated profiles and paid
                    placements, LexFind pulls real case data directly from court
                    records.
                </p>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Step 1 -->
                    <div
                        class="flex flex-col rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                    >
                        <span class="mb-4 font-mono text-xs text-neutral-400"
                            >01</span
                        >
                        <Search class="mb-4 h-8 w-8 text-[#C8961E]" />
                        <h4
                            class="mb-2 font-serif text-lg font-bold text-[#0A1929]"
                        >
                            Search by what matters
                        </h4>
                        <p class="text-sm leading-relaxed text-neutral-500">
                            Filter by practice area, state, win rate, board
                            certification, availability, and fee structure — not
                            just name recognition.
                        </p>
                    </div>
                    <!-- Step 2 -->
                    <div
                        class="flex flex-col rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                    >
                        <span class="mb-4 font-mono text-xs text-neutral-400"
                            >02</span
                        >
                        <FileText class="mb-4 h-8 w-8 text-[#C8961E]" />
                        <h4
                            class="mb-2 font-serif text-lg font-bold text-[#0A1929]"
                        >
                            Review real case records
                        </h4>
                        <p class="text-sm leading-relaxed text-neutral-500">
                            Every attorney profile shows actual docket history,
                            motion outcomes, appeal records, and court documents
                            — pulled from live court data.
                        </p>
                    </div>
                    <!-- Step 3 -->
                    <div
                        class="flex flex-col rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
                    >
                        <span class="mb-4 font-mono text-xs text-neutral-400"
                            >03</span
                        >
                        <MessageSquare class="mb-4 h-8 w-8 text-[#C8961E]" />
                        <h4
                            class="mb-2 font-serif text-lg font-bold text-[#0A1929]"
                        >
                            Contact with confidence
                        </h4>
                        <p class="text-sm leading-relaxed text-neutral-500">
                            Message verified attorneys directly, compare
                            multiple profiles, and make your decision backed by
                            data — not just a website bio.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Client Features -->
        <section id="features" class="bg-[#0A1929] px-6 py-20">
            <div class="mx-auto max-w-5xl">
                <span
                    class="mb-4 inline-block rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-bold tracking-wider text-[#DBA93E] uppercase"
                    >For Clients</span
                >
                <h2
                    class="mb-12 font-serif text-3xl font-bold text-white md:text-4xl"
                >
                    Everything you need to pick the right attorney.
                </h2>

                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <TrendingUp class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Real Win Rates
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            See each attorney's actual win/loss record from
                            court filings — not a self-reported percentage on
                            their bio page.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <FileText class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Full Case Timeline
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            Browse complete docket histories — every filing,
                            motion, hearing, and outcome — for any case the
                            attorney has handled.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <Shield class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Conduct Verification
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            Every attorney is cross-checked against all state
                            bar disciplinary records, criminal databases, and
                            federal court standings.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <Star class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Peer-Verified Reviews
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            Ratings from Martindale-Hubbell peer reviews — the
                            gold standard in legal reputation — not anonymous
                            user submissions.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <Calendar class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Availability Tracking
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            See which attorneys are available now, on a
                            waitlist, or unavailable — and filter your search
                            accordingly.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:bg-white/10"
                    >
                        <MessageSquare class="mb-3 h-6 w-6 text-[#DBA93E]" />
                        <h4 class="mb-2 font-bold text-white">
                            Direct Messaging
                        </h4>
                        <p class="text-sm leading-relaxed text-white/50">
                            Contact attorneys directly through LexFind — no
                            referral middlemen, no cold calls, no paid placement
                            prioritization.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Attorney Split Section -->
        <section id="attorneys" class="bg-[#F3EFE8] px-6 py-20 text-[#16161A]">
            <div
                class="mx-auto grid max-w-5xl grid-cols-1 items-center gap-12 lg:grid-cols-12"
            >
                <div class="flex flex-col items-start lg:col-span-7">
                    <span
                        class="mb-4 inline-block rounded-full border border-[#0A1929]/10 bg-[#0A1929]/5 px-4 py-1 text-xs font-bold tracking-wider text-[#0A1929] uppercase"
                        >For Attorneys</span
                    >
                    <h2
                        class="mb-4 font-serif text-3xl font-bold text-[#0A1929] md:text-4xl"
                    >
                        Your competitive intelligence platform.
                    </h2>
                    <p class="mb-8 text-base leading-relaxed text-neutral-500">
                        LexFind isn't just a directory. For attorneys, it's a
                        full opponent research and practice development tool.
                    </p>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <TrendingUp
                                class="mt-1 h-6 w-6 shrink-0 text-[#C8961E]"
                            />
                            <div>
                                <h5 class="font-bold text-[#0A1929]">
                                    Opponent Intelligence (Pro)
                                </h5>
                                <p class="mt-1 text-sm text-neutral-500">
                                    Research any opposing counsel's win rate by
                                    judge, motion success patterns, settlement
                                    behavior, and cross-examination style —
                                    before you step into court.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <Scale
                                class="mt-1 h-6 w-6 shrink-0 text-[#C8961E]"
                            />
                            <div>
                                <h5 class="font-bold text-[#0A1929]">
                                    Judge Frequency Analytics
                                </h5>
                                <p class="mt-1 text-sm text-neutral-500">
                                    Know your win rate in front of each judge
                                    before you file. LexFind tracks
                                    judge-specific outcomes for every attorney
                                    in our network.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <Award
                                class="mt-1 h-6 w-6 shrink-0 text-[#C8961E]"
                            />
                            <div>
                                <h5 class="font-bold text-[#0A1929]">
                                    Claim & Manage Your Profile
                                </h5>
                                <p class="mt-1 text-sm text-neutral-500">
                                    Claim your auto-generated profile, add your
                                    bio, set your availability, and let verified
                                    case data build your reputation for you.
                                </p>
                            </div>
                        </div>
                    </div>
                    <button
                        @click="openModal('signup')"
                        class="mt-8 cursor-pointer rounded-xl bg-[#0A1929] px-6 py-3 font-bold text-[#DBA93E] shadow-md transition-all hover:bg-[#1E3A54]"
                    >
                        Claim My Attorney Profile →
                    </button>
                </div>

                <div
                    class="rounded-2xl border border-[#C8961E]/20 bg-[#0A1929] p-6 text-white shadow-xl lg:col-span-5"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <span
                            class="rounded-full border border-[#C8961E]/22 bg-[#C8961E]/12 px-2.5 py-1 text-[10px] font-bold tracking-wider text-[#F0C96A] uppercase"
                            >⚡ PRO INTELLIGENCE</span
                        >
                        <span
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-400"
                            >● Live</span
                        >
                    </div>
                    <h5 class="mb-4 font-serif text-base font-bold text-white">
                        Opposing Counsel: J. Pierce
                    </h5>

                    <div
                        class="mb-4 grid grid-cols-4 gap-1.5 rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                    >
                        <div>
                            <p
                                class="font-serif text-base font-bold text-[#86EFAC]"
                            >
                                93%
                            </p>
                            <p
                                class="text-[9px] tracking-wider text-white/40 uppercase"
                            >
                                Win Rate
                            </p>
                        </div>
                        <div>
                            <p
                                class="font-serif text-base font-bold text-[#DBA93E]"
                            >
                                $2.1M
                            </p>
                            <p
                                class="text-[9px] tracking-wider text-white/40 uppercase"
                            >
                                Avg Settl.
                            </p>
                        </div>
                        <div>
                            <p
                                class="font-serif text-base font-bold text-[#93B8F8]"
                            >
                                18
                            </p>
                            <p
                                class="text-[9px] tracking-wider text-white/40 uppercase"
                            >
                                Active Cases
                            </p>
                        </div>
                        <div>
                            <p
                                class="font-serif text-base font-bold text-[#DBA93E]"
                            >
                                84%
                            </p>
                            <p
                                class="text-[9px] tracking-wider text-white/40 uppercase"
                            >
                                Jury Wins
                            </p>
                        </div>
                    </div>

                    <p
                        class="mb-2 text-[10px] font-bold tracking-wider text-white/30 uppercase"
                    >
                        Judge Win Rate
                    </p>
                    <div class="mb-4 space-y-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-24 truncate text-right text-white/50"
                                >Hon. Altonaga</span
                            >
                            <div
                                class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/10"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-[#C8961E] to-[#DBA93E]"
                                    style="width: 93%"
                                ></div>
                            </div>
                            <span class="w-8 font-mono text-[#DBA93E]"
                                >93%</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-24 truncate text-right text-white/50"
                                >Hon. Scola</span
                            >
                            <div
                                class="h-1.5 flex-1 overflow-hidden rounded-full bg-white/10"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-[#C8961E] to-[#DBA93E]"
                                    style="width: 78%"
                                ></div>
                            </div>
                            <span class="w-8 font-mono text-[#DBA93E]"
                                >78%</span
                            >
                        </div>
                    </div>

                    <p
                        class="mb-2 text-[10px] font-bold tracking-wider text-white/30 uppercase"
                    >
                        Tactical Profile
                    </p>
                    <div class="space-y-1.5 text-xs text-white/70">
                        <div
                            class="rounded-lg border border-white/5 bg-white/5 p-2"
                        >
                            📝 Heavy on pre-trial motions
                        </div>
                        <div
                            class="rounded-lg border border-white/5 bg-white/5 p-2"
                        >
                            🤝 Settles 70% before trial
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="bg-[#0A1929] px-6 py-20">
            <div class="mx-auto max-w-5xl text-center">
                <span
                    class="mb-4 inline-block rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-bold tracking-wider text-[#DBA93E] uppercase"
                    >Simple Pricing</span
                >
                <h2
                    class="mb-12 font-serif text-3xl font-bold text-white md:text-4xl"
                >
                    Start free. Upgrade when you need more.
                </h2>

                <div class="grid grid-cols-1 gap-8 text-left md:grid-cols-3">
                    <!-- Client Plan -->
                    <div
                        class="flex flex-col justify-between rounded-2xl border border-white/10 bg-white/5 p-6"
                    >
                        <div>
                            <h4
                                class="mb-2 text-xs font-bold tracking-wider text-white/40 uppercase"
                            >
                                Client — Free
                            </h4>
                            <p
                                class="mb-4 font-serif text-4xl font-bold text-white"
                            >
                                $0
                                <span
                                    class="font-sans text-sm font-normal text-white/40"
                                    >/mo</span
                                >
                            </p>
                            <p
                                class="mb-6 border-b border-white/10 pb-6 text-sm text-white/50"
                            >
                                Everything you need to find and vet the right
                                attorney for your case.
                            </p>

                            <ul class="mb-8 space-y-3 text-sm text-white/70">
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Search verified attorneys
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    View profiles & case history
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Filter by win rate, state
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Bar record verification
                                </li>
                            </ul>
                        </div>
                        <button
                            @click="openModal('signup')"
                            class="w-full cursor-pointer rounded-xl border border-white/10 bg-white/10 py-2.5 font-semibold text-white/80 transition-all hover:bg-white/15 hover:text-white"
                        >
                            Get Started Free
                        </button>
                    </div>

                    <!-- Pro Plan -->
                    <div
                        class="relative flex flex-col justify-between rounded-2xl border border-[#C8961E]/30 bg-[#C8961E]/7 p-6 shadow-2xl shadow-[#C8961E]/5"
                    >
                        <span
                            class="absolute top-[-12px] left-1/2 -translate-x-1/2 rounded-full bg-[#C8961E] px-3 py-1 text-[10px] font-bold tracking-widest text-[#0A1929] uppercase"
                            >Most Popular</span
                        >
                        <div>
                            <h4
                                class="mb-2 text-xs font-bold tracking-wider text-[#DBA93E] uppercase"
                            >
                                Attorney Pro
                            </h4>
                            <p
                                class="mb-4 font-serif text-4xl font-bold text-white"
                            >
                                $200
                                <span
                                    class="font-sans text-sm font-normal text-white/40"
                                    >/mo</span
                                >
                            </p>
                            <p
                                class="mb-6 border-b border-white/10 pb-6 text-sm text-white/50"
                            >
                                Full competitive intelligence suite for
                                practicing attorneys.
                            </p>

                            <ul class="mb-8 space-y-3 text-sm text-white/70">
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Claim & manage profile
                                </li>
                                <li
                                    class="flex gap-2 font-medium text-[#DBA93E]"
                                >
                                    <Check class="h-4.5 w-4.5" /> ⚡ Opponent
                                    Intelligence
                                </li>
                                <li
                                    class="flex gap-2 font-medium text-[#DBA93E]"
                                >
                                    <Check class="h-4.5 w-4.5" /> ⚡
                                    Judge-specific win rates
                                </li>
                                <li
                                    class="flex gap-2 font-medium text-[#DBA93E]"
                                >
                                    <Check class="h-4.5 w-4.5" /> ⚡ Motion
                                    success tracking
                                </li>
                            </ul>
                        </div>
                        <button
                            @click="openModal('signup')"
                            class="w-full cursor-pointer rounded-xl bg-[#C8961E] py-2.5 font-bold text-[#0A1929] transition-all hover:bg-[#DBA93E]"
                        >
                            Start 14-Day Free Trial
                        </button>
                    </div>

                    <!-- Enterprise Plan -->
                    <div
                        class="flex flex-col justify-between rounded-2xl border border-white/10 bg-white/5 p-6"
                    >
                        <div>
                            <h4
                                class="mb-2 text-xs font-bold tracking-wider text-white/40 uppercase"
                            >
                                Firm / Enterprise
                            </h4>
                            <p
                                class="mb-4 font-serif text-4xl font-bold text-white"
                            >
                                Custom
                            </p>
                            <p
                                class="mb-6 border-b border-white/10 pb-6 text-sm text-white/50"
                            >
                                For law firms and legal teams that need bulk
                                data exports and API integrations.
                            </p>

                            <ul class="mb-8 space-y-3 text-sm text-white/70">
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Unlimited team seats
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    Bulk case data exports
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    CourtListener API integrations
                                </li>
                                <li class="flex gap-2">
                                    <Check class="h-4.5 w-4.5 text-[#DBA93E]" />
                                    White-label profiles
                                </li>
                            </ul>
                        </div>
                        <button
                            @click="openModal('signup')"
                            class="w-full cursor-pointer rounded-xl border border-white/10 bg-white/10 py-2.5 font-semibold text-white/80 transition-all hover:bg-white/15 hover:text-white"
                        >
                            Contact Sales
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section
            class="relative border-t border-white/5 bg-gradient-to-br from-[#0A1929] via-[#0D2640] to-[#162D42] px-6 py-20 text-center"
        >
            <div
                class="pointer-events-none absolute top-1/2 left-1/2 h-[600px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#C8961E]/5 blur-3xl"
            ></div>
            <div class="relative z-10 mx-auto max-w-xl">
                <span
                    class="mb-6 inline-flex items-center gap-1.5 rounded-full border border-[#C8961E]/20 bg-[#C8961E]/12 px-4 py-1.5 text-xs font-semibold tracking-wider text-[#F0C96A] uppercase"
                >
                    Join 12,000+ clients and attorneys
                </span>
                <h2
                    class="mb-4 font-serif text-3xl leading-tight font-bold text-white md:text-5xl"
                >
                    Ready to make smarter legal decisions?
                </h2>
                <p class="mb-8 text-base leading-relaxed text-white/40">
                    Search verified attorneys, review real case records, and
                    connect with confidence — for free.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <button
                        @click="openAuthModal('signup')"
                        class="cursor-pointer rounded-xl bg-[#C8961E] px-6 py-3.5 font-bold text-[#0A1929] shadow-xl shadow-[#C8961E]/20 transition-all hover:bg-[#DBA93E]"
                    >
                        Create Free Account
                    </button>
                    <button
                        @click="openAuthModal('login')"
                        class="cursor-pointer rounded-xl border border-white/10 bg-white/5 px-6 py-3.5 font-semibold transition-all hover:bg-white/10"
                    >
                        Sign In
                    </button>
                </div>
            </div>
        </section>
    </div>
</template>
