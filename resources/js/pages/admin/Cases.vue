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
    User,
    CheckCircle,
    Scale,
    Activity,
    Clock,
    FileText,
    Gavel,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface LawyerItem {
    id: number;
    name: string;
    firm: string;
    avatar_color: string;
    initials: string;
}

interface CaseRecord {
    id: number;
    slug: string;
    lawyer_id: number;
    name: string;
    case_number: string;
    jurisdiction: string;
    type: string;
    type_label: string;
    status: string;
    year: number;
    court: string;
    won_party: string | null;
    motions_count: number;
    motion_success_rate: string | null;
    timeline: any[] | null;
    motions: any[] | null;
    parties: any[] | null;
    issues: any[] | null;
    documents: any[] | null;
    summary: string | null;
    key_finding: string | null;
    rate_boxes: any | null;
    next_steps: any[] | null;
    lawyer?: LawyerItem | null;
    lawyers?: (LawyerItem & { pivot?: { outcome: string } })[] | null;
}

const props = defineProps<{
    cases: CaseRecord[];
    lawyers: LawyerItem[];
}>();

const page = usePage();

// Search filter state
const searchQuery = ref('');

const filteredCases = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.cases;
    }

    const query = searchQuery.value.toLowerCase();

    return props.cases.filter(
        (c) =>
            c.name.toLowerCase().includes(query) ||
            c.case_number.toLowerCase().includes(query) ||
            c.court.toLowerCase().includes(query) ||
            c.jurisdiction.toLowerCase().includes(query) ||
            (c.lawyers && c.lawyers.some(l => l.name.toLowerCase().includes(query) || l.firm.toLowerCase().includes(query))) ||
            (c.lawyer && c.lawyer.name.toLowerCase().includes(query)) ||
            (c.lawyer && c.lawyer.firm.toLowerCase().includes(query)),
    );
});

// Modal state
const isFormModalOpen = ref(false);
const editingCase = ref<CaseRecord | null>(null);

const form = useForm({
    lawyer_id: '' as string | number,
    lawyers: [] as Array<{ id: number, name: string, firm: string, initials: string, avatar_color: string, outcome: string }>,
    name: '',
    case_number: '',
    jurisdiction: 'Florida State Court',
    court: 'Eleventh Circuit Court of Florida',
    type: 'civil',
    type_label: 'Commercial Litigation',
    status: 'active',
    year: new Date().getFullYear(),
    won_party: '',
    motions_count: 0,
    motion_success_rate: '0%',
    summary: '',
    key_finding: '',
    timeline: [] as any[],
    motions: [] as any[],
    parties: [] as any[],
    issues: [] as any[],
    documents: [] as any[],
    rate_boxes: { fail: '0%', failSub: 'at Trial Level', win: '100%', winSub: 'on Appeal' } as any,
    next_steps: [] as any[],
});

// Nested fields add state refs
const newParty = ref({ name: '', role: '', status: '', isWinner: false });
const addParty = () => {
    if (!newParty.value.name || !newParty.value.role) {
        alert('Party Name and Role are required.');
        return;
    }
    form.parties.push({ ...newParty.value });
    newParty.value = { name: '', role: '', status: '', isWinner: false };
};

const newTimeline = ref({ date: '', title: '', action: '', status: 'initiated', badgeLabel: '', context: '' });
const addTimeline = () => {
    if (!newTimeline.value.date || !newTimeline.value.title || !newTimeline.value.action) {
        alert('Date, Event Title, and Action/Description are required.');
        return;
    }
    form.timeline.push({
        date: newTimeline.value.date,
        title: newTimeline.value.title,
        action: newTimeline.value.action,
        status: newTimeline.value.status,
        badge: newTimeline.value.status,
        badgeLabel: newTimeline.value.badgeLabel || newTimeline.value.status.toUpperCase(),
        context: newTimeline.value.context || undefined,
    });
    newTimeline.value = { date: '', title: '', action: '', status: 'initiated', badgeLabel: '', context: '' };
};

const newMotion = ref({ date: '', motion: '', details: '', result: 'filed', resultLabel: 'FILED' });
const addMotion = () => {
    if (!newMotion.value.motion || !newMotion.value.date) {
        alert('Motion Name and Filed Date are required.');
        return;
    }
    form.motions.push({
        date: newMotion.value.date,
        motion: newMotion.value.motion,
        details: newMotion.value.details,
        result: newMotion.value.result,
        resultLabel: newMotion.value.resultLabel || newMotion.value.result.toUpperCase(),
    });
    newMotion.value = { date: '', motion: '', details: '', result: 'filed', resultLabel: 'FILED' };
};

const newIssue = ref({ title: '', desc: '' });
const addIssue = () => {
    if (!newIssue.value.title || !newIssue.value.desc) {
        alert('Issue Title and Description are required.');
        return;
    }
    const nextNum = form.issues.length + 1;
    form.issues.push({
        num: nextNum,
        title: newIssue.value.title,
        desc: newIssue.value.desc,
    });
    newIssue.value = { title: '', desc: '' };
};

const newDocument = ref({ name: '', date: '', type: 'motion', typeLabel: 'MOTION' });
const addDocument = () => {
    if (!newDocument.value.name || !newDocument.value.date) {
        alert('Document Name and Date Filed are required.');
        return;
    }
    form.documents.push({
        name: newDocument.value.name,
        date: newDocument.value.date,
        type: newDocument.value.type,
        typeLabel: newDocument.value.typeLabel || newDocument.value.type.toUpperCase(),
    });
    newDocument.value = { name: '', date: '', type: 'motion', typeLabel: 'MOTION' };
};

const newNextStep = ref({ icon: '⚖️', title: '', desc: '' });
const addNextStep = () => {
    if (!newNextStep.value.title || !newNextStep.value.desc) {
        alert('Title and Description are required.');
        return;
    }
    form.next_steps.push({ ...newNextStep.value });
    newNextStep.value = { icon: '⚖️', title: '', desc: '' };
};

// Lawyer select modal state
const isLawyerModalOpen = ref(false);
const lawyerSearchQuery = ref('');

const filteredLawyers = computed(() => {
    if (!lawyerSearchQuery.value.trim()) {
        return props.lawyers;
    }
    const q = lawyerSearchQuery.value.toLowerCase();
    return props.lawyers.filter(
        (l) => l.name.toLowerCase().includes(q) || l.firm.toLowerCase().includes(q),
    );
});

const openLawyerSelect = () => {
    lawyerSearchQuery.value = '';
    isLawyerModalOpen.value = true;
};

const selectLawyer = (l: LawyerItem) => {
    if (form.lawyers.some(lawyer => lawyer.id === l.id)) {
        alert(`${l.name} is already attached to this case.`);
        return;
    }
    form.lawyers.push({
        id: l.id,
        name: l.name,
        firm: l.firm,
        initials: l.initials,
        avatar_color: l.avatar_color,
        outcome: 'active'
    });
    isLawyerModalOpen.value = false;
};

const openCreateModal = () => {
    editingCase.value = null;
    form.reset();
    form.clearErrors();

    // Default setups
    form.lawyer_id = '';
    form.lawyers = [];
    form.name = '';
    form.case_number = '';
    form.jurisdiction = 'Florida State Court';
    form.court = 'Eleventh Circuit Court of Florida';
    form.type = 'civil';
    form.type_label = 'Commercial Litigation';
    form.status = 'active';
    form.year = new Date().getFullYear();
    form.won_party = '';
    form.motions_count = 0;
    form.motion_success_rate = '100%';
    form.summary = '';
    form.key_finding = '';
    form.timeline = [];
    form.motions = [];
    form.parties = [];
    form.issues = [];
    form.documents = [];
    form.rate_boxes = { fail: '0%', failSub: 'at Trial Level', win: '100%', winSub: 'on Appeal' };
    form.next_steps = [];

    isFormModalOpen.value = true;
};

const openEditModal = (c: CaseRecord) => {
    editingCase.value = c;
    form.clearErrors();

    form.lawyer_id = c.lawyer_id;
    if (c.lawyers && c.lawyers.length > 0) {
        form.lawyers = c.lawyers.map(l => ({
            id: l.id,
            name: l.name,
            firm: l.firm,
            initials: l.initials,
            avatar_color: l.avatar_color,
            outcome: l.pivot?.outcome ?? c.status
        }));
    } else if (c.lawyer) {
        form.lawyers = [{
            id: c.lawyer.id,
            name: c.lawyer.name,
            firm: c.lawyer.firm,
            initials: c.lawyer.initials,
            avatar_color: c.lawyer.avatar_color,
            outcome: c.status
        }];
    } else {
        form.lawyers = [];
    }

    form.name = c.name;
    form.case_number = c.case_number;
    form.jurisdiction = c.jurisdiction;
    form.court = c.court;
    form.type = c.type;
    form.type_label = c.type_label;
    form.status = c.status;
    form.year = c.year;
    form.won_party = c.won_party || '';
    form.motions_count = c.motions_count;
    form.motion_success_rate = c.motion_success_rate || '100%';
    form.summary = c.summary || '';
    form.key_finding = c.key_finding || '';
    form.timeline = c.timeline ? JSON.parse(JSON.stringify(c.timeline)) : [];
    form.motions = c.motions ? JSON.parse(JSON.stringify(c.motions)) : [];
    form.parties = c.parties ? JSON.parse(JSON.stringify(c.parties)) : [];
    form.issues = c.issues ? JSON.parse(JSON.stringify(c.issues)) : [];
    form.documents = c.documents ? JSON.parse(JSON.stringify(c.documents)) : [];
    form.rate_boxes = c.rate_boxes ? JSON.parse(JSON.stringify(c.rate_boxes)) : { fail: '0%', failSub: 'at Trial Level', win: '100%', winSub: 'on Appeal' };
    form.next_steps = c.next_steps ? JSON.parse(JSON.stringify(c.next_steps)) : [];

    isFormModalOpen.value = true;
};

const submitForm = () => {
    if (form.lawyers.length === 0) {
        alert('Please attach at least one Attorney to this case.');
        return;
    }

    form.lawyer_id = form.lawyers[0].id;

    if (editingCase.value) {
        form.post(`/admin/cases/${editingCase.value.id}`, {
            onSuccess: () => {
                isFormModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin/cases', {
            onSuccess: () => {
                isFormModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const deleteCase = (c: CaseRecord) => {
    if (confirm(`Are you sure you want to permanently delete the case "${c.name}" (${c.case_number})?`)) {
        useForm({}).post(`/admin/cases/${c.id}/delete`, {
            onSuccess: () => {
                alert('Case record deleted successfully.');
            },
        });
    }
};
</script>

<template>
    <Head title="Case Records Management" />

    <div class="space-y-6">
        <!-- Toast Notifications -->
        <div
            v-if="$page.props.flash?.success"
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 shadow-xs"
        >
            <CheckCircle class="h-5 w-5 shrink-0 text-emerald-600" />
            <div>{{ $page.props.flash.success }}</div>
        </div>

        <!-- Header -->
        <div
            class="flex flex-col justify-between gap-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs sm:flex-row sm:items-center"
        >
            <div class="text-left">
                <h1 class="font-serif text-xl font-bold text-neutral-800">
                    Cases Directory
                </h1>
                <p class="mt-1 text-xs text-neutral-400">
                    Create, update, and manage litigation case records and track docket timelines.
                </p>
            </div>
            <div>
                <button
                    @click="openCreateModal"
                    class="flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#C8961E] px-4 py-2 text-xs font-bold tracking-wider text-[#0A1929] uppercase shadow-sm transition-all hover:bg-[#DBA93E] hover:shadow"
                >
                    <Plus class="h-4 w-4" /> Add Case Record
                </button>
            </div>
        </div>

        <!-- Directory Table -->
        <div
            class="overflow-hidden rounded-2xl border border-black/5 bg-white text-left shadow-xs"
        >
            <!-- Search & Info Bar -->
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
                        placeholder="Search by case name, number, court or attorney..."
                        class="w-full rounded-lg border border-black/8 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                    />
                </div>
                <div class="text-xs font-medium text-neutral-400">
                    Showing {{ filteredCases.length }} of
                    {{ cases.length }} case records
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 bg-neutral-50/30 text-[9px] tracking-wider text-neutral-400 uppercase"
                        >
                            <th class="px-4 py-3">Case Info</th>
                            <th class="px-4 py-3">Attached Attorney</th>
                            <th class="px-4 py-3">Jurisdiction / Court</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr
                            v-if="filteredCases.length === 0"
                            class="text-center text-neutral-400"
                        >
                            <td colspan="5" class="py-12">
                                No case records match the search parameters.
                            </td>
                        </tr>
                        <tr
                            v-for="c in filteredCases"
                            :key="c.id"
                            class="transition-all hover:bg-neutral-50/40"
                        >
                            <td class="px-4 py-3.5">
                                <div>
                                    <span
                                        class="rounded border border-[#C8961E]/30 bg-[#C8961E]/10 px-1.5 py-0.2 font-mono text-[9px] font-bold text-[#C8961E] uppercase"
                                    >
                                        {{ c.case_number }}
                                    </span>
                                    <div class="mt-1.5 text-xs font-bold text-neutral-800">
                                        <Link
                                            :href="`/cases/${c.slug}`"
                                            class="hover:text-[#DBA93E] hover:underline"
                                        >
                                            {{ c.name }}
                                        </Link>
                                    </div>
                                    <div class="text-[9px] text-neutral-400 mt-0.5">
                                        Record Year: {{ c.year }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div v-if="c.lawyers && c.lawyers.length > 0" class="flex flex-col gap-2">
                                    <div v-for="l in c.lawyers" :key="l.id" class="flex items-center gap-2">
                                        <div
                                            class="flex h-6.5 w-6.5 shrink-0 items-center justify-center rounded-lg font-serif text-[9px] font-bold text-white shadow-inner"
                                            :style="{
                                                backgroundColor: l.avatar_color,
                                            }"
                                        >
                                            {{ l.initials }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <Link
                                                    :href="`/lawyers/${l.id}`"
                                                    class="font-bold text-neutral-700 hover:text-[#DBA93E] hover:underline"
                                                >
                                                    {{ l.name }}
                                                </Link>
                                                <span class="rounded bg-neutral-100 px-1 py-0.2 font-mono text-[8px] font-semibold text-neutral-600 uppercase">
                                                    {{ l.pivot?.outcome ?? c.status }}
                                                </span>
                                            </div>
                                            <div class="text-[9px] text-neutral-400">{{ l.firm }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="c.lawyer" class="flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg font-serif text-[10px] font-bold text-white shadow-inner"
                                        :style="{
                                            backgroundColor: c.lawyer.avatar_color,
                                        }"
                                    >
                                        {{ c.lawyer.initials }}
                                    </div>
                                    <div>
                                        <Link
                                            :href="`/lawyers/${c.lawyer.id}`"
                                            class="font-bold text-neutral-700 hover:text-[#DBA93E] hover:underline"
                                        >
                                            {{ c.lawyer.name }}
                                        </Link>
                                        <div class="text-[9px] text-neutral-400">{{ c.lawyer.firm }}</div>
                                    </div>
                                </div>
                                <div v-else class="text-neutral-400 italic">Unassigned</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-neutral-700">
                                    {{ c.jurisdiction }}
                                </div>
                                <div class="mt-0.5 text-[9px] text-neutral-400">
                                    {{ c.court }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'rounded-full border px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider',
                                        c.status === 'decided'
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                            : c.status === 'active'
                                                ? 'border-sky-200 bg-sky-50 text-sky-700'
                                                : 'border-amber-200 bg-amber-50 text-amber-700',
                                    ]"
                                >
                                    {{ c.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        @click="openEditModal(c)"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-neutral-100 hover:text-[#C8961E]"
                                        title="Edit Case"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </button>
                                    <button
                                        v-if="
                                            $page.props.auth.user?.system === 'ghost' ||
                                            $page.props.auth.user?.system === 'simp'
                                        "
                                        @click="deleteCase(c)"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-red-50 hover:text-red-600"
                                        title="Delete Case"
                                    >
                                        <Trash2 class="h-4.5 w-4.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Case Create / Edit Modal -->
        <div
            v-if="isFormModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white text-left shadow-2xl"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between bg-[#0A1929] px-6 py-4 text-white"
                >
                    <div>
                        <h3 class="font-serif text-base font-bold text-white">
                            {{
                                editingCase
                                    ? `Edit Case Record: ${editingCase.name}`
                                    : 'Create New Case Record'
                            }}
                        </h3>
                        <p class="text-[10px] text-white/50">
                            Configure full case facts, timeline actions, and attachments.
                        </p>
                    </div>
                    <button
                        @click="isFormModalOpen = false"
                        class="cursor-pointer text-white/60 transition-all hover:text-white"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="submitForm"
                    class="flex-1 space-y-6 overflow-y-auto p-6"
                >
                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            1. Attorneys of Record & Outcomes
                        </h4>
                        
                        <div v-if="form.lawyers.length > 0" class="space-y-2.5">
                            <div
                                v-for="(lawyer, idx) in form.lawyers"
                                :key="lawyer.id"
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-neutral-100 bg-neutral-50/50 p-3 text-xs"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-serif text-[11px] font-bold text-white shadow-inner"
                                        :style="{ backgroundColor: lawyer.avatar_color }"
                                    >
                                        {{ lawyer.initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ lawyer.name }}</div>
                                        <div class="text-[10px] text-neutral-400 truncate">{{ lawyer.firm }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5">
                                        <label class="text-[10px] font-bold text-neutral-500 uppercase">Outcome:</label>
                                        <select
                                            v-model="lawyer.outcome"
                                            class="cursor-pointer rounded-md border border-neutral-200 bg-white px-2 py-1 text-xs outline-none focus:border-[#C8961E]"
                                        >
                                            <option value="won">Won</option>
                                            <option value="lost">Lost</option>
                                            <option value="settled">Settled</option>
                                            <option value="active">Active</option>
                                        </select>
                                    </div>
                                    <button
                                        type="button"
                                        @click="form.lawyers.splice(idx, 1)"
                                        class="cursor-pointer font-bold text-red-500 hover:text-red-700 transition-colors"
                                    >
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-neutral-400 italic text-xs py-2">
                            No attorneys attached to this case yet. Please add at least one below.
                        </div>

                        <div>
                            <button
                                type="button"
                                @click="openLawyerSelect"
                                class="flex items-center justify-center gap-1.5 cursor-pointer rounded-lg border border-dashed border-[#C8961E]/40 hover:border-[#C8961E] bg-[#C8961E]/5 hover:bg-[#C8961E]/10 p-2.5 w-full text-xs font-semibold text-[#C8961E] transition-all"
                            >
                                <Plus class="w-4 h-4" /> Attach Attorney of Record
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            2. Case Core Details
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Case Name *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Acme Corp v. Beta LLC"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Case Number *</label>
                                <input
                                    v-model="form.case_number"
                                    type="text"
                                    placeholder="e.g. 2026-CA-001234"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Jurisdiction *</label>
                                <input
                                    v-model="form.jurisdiction"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Court Name *</label>
                                <input
                                    v-model="form.court"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Type *</label>
                                <select
                                    v-model="form.type"
                                    class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none focus:border-[#C8961E]"
                                >
                                    <option value="civil">Civil</option>
                                    <option value="criminal">Criminal</option>
                                    <option value="appellate">Appellate</option>
                                    <option value="arbitration">Arbitration</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Type Label *</label>
                                <input
                                    v-model="form.type_label"
                                    type="text"
                                    placeholder="Commercial Litigation"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Year *</label>
                                <input
                                    v-model.number="form.year"
                                    type="number"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Status *</label>
                                <select
                                    v-model="form.status"
                                    class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none focus:border-[#C8961E]"
                                >
                                    <option value="decided">Decided</option>
                                    <option value="settled">Settled</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Winning Party (optional)</label>
                                <input
                                    v-model="form.won_party"
                                    type="text"
                                    placeholder="e.g. Plaintiff"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Motions Filed Count</label>
                                <input
                                    v-model.number="form.motions_count"
                                    type="number"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold text-neutral-700">Motion Success Rate</label>
                                <input
                                    v-model="form.motion_success_rate"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold text-neutral-700">Case Summary</label>
                            <textarea
                                v-model="form.summary"
                                rows="3"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                            ></textarea>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold text-neutral-700">Key Finding/Ruling</label>
                            <textarea
                                v-model="form.key_finding"
                                rows="2"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E]"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Sub-elements Forms -->
                    <div class="space-y-6 border-t pt-6">
                        <h4
                            class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            3. Case Attachments & Interactive Records
                        </h4>

                        <!-- Litigation Parties -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Litigation Parties</label>
                                <span class="text-[9px] text-neutral-400">Total: {{ form.parties.length }}</span>
                            </div>
                            <div v-if="form.parties.length > 0" class="space-y-1 mb-2">
                                <div v-for="(p, pIdx) in form.parties" :key="pIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div>
                                        <span class="font-bold text-neutral-800">{{ p.name }}</span>
                                        <span class="text-[9px] text-neutral-400 ml-1">({{ p.role }}) - {{ p.status }} {{ p.isWinner ? '🏆' : '' }}</span>
                                    </div>
                                    <button type="button" @click="form.parties.splice(pIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newParty.name" type="text" placeholder="Party Name" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newParty.role" type="text" placeholder="Role (e.g. Plaintiff / Appellee)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newParty.status" type="text" placeholder="Status (e.g. LOST, WON)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <label class="flex items-center gap-1.5 text-[9px] font-semibold text-neutral-600 col-span-2 mt-1">
                                    <input type="checkbox" v-model="newParty.isWinner" class="rounded accent-[#C8961E]" /> Is Winner?
                                </label>
                                <button type="button" @click="addParty" class="col-span-2 text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Party
                                </button>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Docket Timeline History</label>
                                <span class="text-[9px] text-neutral-400">Events: {{ form.timeline.length }}</span>
                            </div>
                            <div v-if="form.timeline.length > 0" class="space-y-1 mb-2">
                                <div v-for="(t, tIdx) in form.timeline" :key="tIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ t.title }}</div>
                                        <div class="text-[9px] text-neutral-400">{{ t.date }} | Type: {{ t.badgeLabel }} | Status: {{ t.status }}</div>
                                    </div>
                                    <button type="button" @click="form.timeline.splice(tIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newTimeline.date" type="text" placeholder="Date (e.g. May 12, 2024)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newTimeline.title" type="text" placeholder="Event Title" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newTimeline.action" type="text" placeholder="Action Description" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <select v-model="newTimeline.status" class="rounded border bg-white p-1 text-[11px] outline-none">
                                    <option value="initiated">Initiated</option>
                                    <option value="denied">Denied</option>
                                    <option value="procedural">Procedural</option>
                                    <option value="brief">Brief</option>
                                    <option value="oral">Oral</option>
                                    <option value="victory">Victory</option>
                                    <option value="granted">Granted</option>
                                    <option value="won">Won</option>
                                </select>
                                <input v-model="newTimeline.badgeLabel" type="text" placeholder="Badge Label (e.g. INITIATED)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newTimeline.context" type="text" placeholder="Context (e.g. Denied at Trial Level)" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <button type="button" @click="addTimeline" class="col-span-2 text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Timeline Event
                                </button>
                            </div>
                        </div>

                        <!-- Motion Success Rates & Motions List -->
                        <div>
                            <label class="text-[10px] font-bold text-neutral-700 uppercase block mb-1.5">Motion Success Rates</label>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50 p-2 rounded-lg border border-neutral-100 mb-3">
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Trial Result %</label>
                                    <input v-model="form.rate_boxes.fail" type="text" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Trial Result Sub-label</label>
                                    <input v-model="form.rate_boxes.failSub" type="text" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Appellate Result %</label>
                                    <input v-model="form.rate_boxes.win" type="text" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Appellate Sub-label</label>
                                    <input v-model="form.rate_boxes.winSub" type="text" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Motions Tracker</label>
                                <span class="text-[9px] text-neutral-400">Total: {{ form.motions.length }}</span>
                            </div>
                            <div v-if="form.motions.length > 0" class="space-y-1 mb-2">
                                <div v-for="(m, mIdx) in form.motions" :key="mIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ m.motion }}</div>
                                        <div class="text-[9px] text-neutral-400">{{ m.date }} | Ruling: {{ m.resultLabel }}</div>
                                    </div>
                                    <button type="button" @click="form.motions.splice(mIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newMotion.motion" type="text" placeholder="Motion Name" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newMotion.date" type="text" placeholder="Date Filed" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <select v-model="newMotion.result" class="rounded border bg-white p-1 text-[11px] outline-none">
                                    <option value="granted">Granted</option>
                                    <option value="denied">Denied</option>
                                    <option value="filed">Filed</option>
                                    <option value="argued">Argued</option>
                                </select>
                                <input v-model="newMotion.resultLabel" type="text" placeholder="Result Label (e.g. GRANTED)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newMotion.details" type="text" placeholder="Details/Description" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <button type="button" @click="addMotion" class="col-span-2 text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Motion
                                </button>
                            </div>
                        </div>

                        <!-- Core Legal Issues -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Core Legal Issues</label>
                                <span class="text-[9px] text-neutral-400">Issues: {{ form.issues.length }}</span>
                            </div>
                            <div v-if="form.issues.length > 0" class="space-y-1 mb-2">
                                <div v-for="(i, iIdx) in form.issues" :key="iIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800">{{ i.num }}. {{ i.title }}</div>
                                        <div class="text-[9px] text-neutral-400 truncate">{{ i.desc }}</div>
                                    </div>
                                    <button type="button" @click="form.issues.splice(iIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newIssue.title" type="text" placeholder="Issue Title" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <textarea v-model="newIssue.desc" rows="2" placeholder="Issue Description" class="rounded border bg-white p-1 text-[11px] outline-none"></textarea>
                                <button type="button" @click="addIssue" class="text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Legal Issue
                                </button>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Court Documents on Record</label>
                                <span class="text-[9px] text-neutral-400">Docs: {{ form.documents.length }}</span>
                            </div>
                            <div v-if="form.documents.length > 0" class="space-y-1 mb-2">
                                <div v-for="(d, dIdx) in form.documents" :key="dIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ d.name }}</div>
                                        <div class="text-[9px] text-neutral-400">Filed: {{ d.date }} | Type: {{ d.typeLabel }}</div>
                                    </div>
                                    <button type="button" @click="form.documents.splice(dIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newDocument.name" type="text" placeholder="Document Title" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newDocument.date" type="text" placeholder="Date Filed" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <select v-model="newDocument.type" class="rounded border bg-white p-1 text-[11px] outline-none">
                                    <option value="motion">Motion</option>
                                    <option value="order">Order</option>
                                    <option value="procedural">Procedural</option>
                                    <option value="brief">Brief</option>
                                    <option value="judgment">Judgment</option>
                                </select>
                                <input v-model="newDocument.typeLabel" type="text" placeholder="Type Label (e.g. PROCEDURAL)" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none" />
                                <button type="button" @click="addDocument" class="col-span-2 text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Document
                                </button>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-700 uppercase">Post-Opinion Actions & Next Steps</label>
                                <span class="text-[9px] text-neutral-400">Steps: {{ form.next_steps.length }}</span>
                            </div>
                            <div v-if="form.next_steps.length > 0" class="space-y-1 mb-2">
                                <div v-for="(n, nIdx) in form.next_steps" :key="nIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800">{{ n.icon }} {{ n.title }}</div>
                                        <div class="text-[9px] text-neutral-400 truncate">{{ n.desc }}</div>
                                    </div>
                                    <button type="button" @click="form.next_steps.splice(nIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50/50 p-2 rounded-lg border border-dashed border-neutral-200">
                                <input v-model="newNextStep.icon" type="text" placeholder="Emoji Icon (e.g. ⚖️)" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <input v-model="newNextStep.title" type="text" placeholder="Title" class="rounded border bg-white p-1 text-[11px] outline-none" />
                                <textarea v-model="newNextStep.desc" rows="2" placeholder="Description" class="col-span-2 rounded border bg-white p-1 text-[11px] outline-none"></textarea>
                                <button type="button" @click="addNextStep" class="col-span-2 text-center bg-neutral-700 text-white rounded p-1 text-[9px] font-bold uppercase hover:bg-neutral-800">
                                    + Add Next Step
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Footer -->
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
                        {{ form.processing ? 'Saving...' : 'Save Case Record' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Attorney Selection Modal -->
        <div
            v-if="isLawyerModalOpen"
            class="fixed inset-0 z-70 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        >
            <div
                class="flex max-h-[80vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white text-left shadow-2xl border border-neutral-100"
            >
                <div class="flex items-center justify-between bg-[#0A1929] px-4 py-3.5 text-white">
                    <div>
                        <h3 class="font-serif text-sm font-bold text-white">
                            Select Attorney of Record
                        </h3>
                        <p class="text-[10px] text-white/50">
                            Search and assign the case to a directory lawyer.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="isLawyerModalOpen = false"
                        class="cursor-pointer text-white/60 hover:text-white"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <div class="border-b border-neutral-100 bg-neutral-50/50 p-4">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                            <Search class="h-4 w-4" />
                        </span>
                        <input
                            v-model="lawyerSearchQuery"
                            type="text"
                            placeholder="Search by name or firm..."
                            class="w-full rounded-lg border border-black/10 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            autofocus
                        />
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    <div v-if="filteredLawyers.length === 0" class="py-8 text-center text-xs text-neutral-400">
                        No attorneys found matching "{{ lawyerSearchQuery }}"
                    </div>
                    <button
                        v-for="l in filteredLawyers"
                        :key="l.id"
                        type="button"
                        @click="selectLawyer(l)"
                        class="flex w-full items-center justify-between rounded-lg p-2.5 text-xs transition-all text-left hover:bg-neutral-50"
                        :class="{ 'bg-[#C8961E]/5': form.lawyers.some(lawyer => lawyer.id === l.id) }"
                    >
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg font-serif text-[10px] font-bold text-white"
                                :style="{ backgroundColor: l.avatar_color }"
                            >
                                {{ l.initials }}
                            </div>
                            <div>
                                <div class="font-bold text-neutral-800">{{ l.name }}</div>
                                <div class="text-[10px] text-neutral-400">{{ l.firm }}</div>
                            </div>
                        </div>
                        <Check v-if="form.lawyers.some(lawyer => lawyer.id === l.id)" class="h-4 w-4 text-[#C8961E]" />
                    </button>
                </div>

                <div class="flex items-center justify-between border-t border-neutral-100 bg-neutral-50 px-4 py-3">
                    <div class="text-[10px] text-neutral-400">
                        {{ filteredLawyers.length }} attorneys available
                    </div>
                    <button
                        type="button"
                        @click="isLawyerModalOpen = false"
                        class="cursor-pointer rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scrollbar stylings */
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e5e5e5;
    border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}
</style>
