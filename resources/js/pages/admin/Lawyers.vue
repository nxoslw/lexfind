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
    Briefcase,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface UserItem {
    id: number;
    name: string;
    email: string;
    system: string;
}

interface LawyerCase {
    id?: number;
    slug?: string;
    name: string;
    case_number: string;
    jurisdiction: string;
    type: string;
    type_label: string;
    status: string;
    year: number;
    court: string;
    won_party?: string;
    motions_count?: number;
    motion_success_rate?: string;
    summary?: string;
    key_finding?: string;
    timeline?: any[];
    motions?: any[];
    parties?: any[];
    issues?: any[];
    documents?: any[];
    rate_boxes?: any;
    next_steps?: any[];
    lawyers?: any[];
}

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
    trial_style: string | null;
    practice_areas: string[];
    user?: UserItem | null;
    cases?: LawyerCase[];
}

const props = defineProps<{
    lawyers: Lawyer[];
    users: UserItem[];
}>();

const page = usePage();

// Search filter state
const searchQuery = ref('');

const filteredLawyers = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.lawyers;
    }

    const query = searchQuery.value.toLowerCase();

    return props.lawyers.filter(
        (l) =>
            l.name.toLowerCase().includes(query) ||
            l.firm.toLowerCase().includes(query) ||
            l.city.toLowerCase().includes(query) ||
            l.state.toLowerCase().includes(query) ||
            l.specialty.toLowerCase().includes(query),
    );
});

// Modal / Slide-over state
const isFormModalOpen = ref(false);
const editingLawyer = ref<Lawyer | null>(null);

const form = useForm({
    name: '',
    title: '',
    firm: '',
    city: '',
    state: '',
    specialty: '',
    bio: '',
    avatar_color: '#1E3A54',
    initials: '',
    email: '',
    phone: '',
    website: '',
    linkedin: '',
    years_experience: 10,
    cases_count: 50,
    cases_won: 45,
    cases_lost: 5,
    cases_settled: 0,
    cases_active: 5,
    financial_recovery: '',
    fee_structure: '',
    is_certified: false,
    rating: 5.0,
    availability: 'available',
    user_id: '' as string | number,
    practice_areas: [] as string[],
    criminal_record: 'CLEARED',
    bar_discipline: 'CLEARED',
    trial_style: '',
    peer_reviews: {
        rating: '5.0 / 5.0',
        source: 'Martindale-Hubbell Peer Review',
        quote: '',
        author: '',
    },
    trial_style_details: {
        approach: '',
        forensics: '',
        global: '',
    },
    recent_activity: [] as Array<{ date: string; title: string; desc: string }>,
    cases: [] as any[],
});

// Case Sub-modal states and actions
const isCaseModalOpen = ref(false);
const isLawyerModalOpen = ref(false);
const lawyerSearchQuery = ref('');
const editingCaseIndex = ref<number | null>(null);
const tempCase = ref<any>({});

const filteredLawyersForCase = computed(() => {
    if (!lawyerSearchQuery.value.trim()) {
        return props.lawyers;
    }
    const q = lawyerSearchQuery.value.toLowerCase();
    return props.lawyers.filter(
        (l) => l.name.toLowerCase().includes(q) || l.firm.toLowerCase().includes(q),
    );
});

const openLawyerSelectForCase = () => {
    lawyerSearchQuery.value = '';
    isLawyerModalOpen.value = true;
};

const selectLawyerForCase = (l: any) => {
    if (!tempCase.value.lawyers) {
        tempCase.value.lawyers = [];
    }
    if (tempCase.value.lawyers.some((lawyer: any) => lawyer.id === l.id)) {
        alert(`${l.name} is already attached to this case.`);
        return;
    }
    tempCase.value.lawyers.push({
        id: l.id,
        name: l.name,
        firm: l.firm,
        initials: l.initials,
        avatar_color: l.avatar_color,
        outcome: 'active'
    });
    isLawyerModalOpen.value = false;
};

const openAddCase = () => {
    editingCaseIndex.value = null;
    tempCase.value = {
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
        timeline: [],
        motions: [],
        parties: [],
        issues: [],
        documents: [],
        rate_boxes: { fail: '0%', failSub: 'at Trial Level', win: '100%', winSub: 'on Appeal' },
        next_steps: [],
        lawyers: [{
            id: editingLawyer.value?.id || 0,
            name: form.name || 'Current Attorney',
            firm: form.firm || '',
            initials: form.initials || '',
            avatar_color: form.avatar_color || '#1E3A54',
            outcome: 'active'
        }]
    };
    isCaseModalOpen.value = true;
};

const openEditCase = (index: number) => {
    editingCaseIndex.value = index;
    const item = JSON.parse(JSON.stringify(form.cases[index]));
    if (!item.timeline) item.timeline = [];
    if (!item.motions) item.motions = [];
    if (!item.parties) item.parties = [];
    if (!item.issues) item.issues = [];
    if (!item.documents) item.documents = [];
    if (!item.next_steps) item.next_steps = [];
    if (!item.rate_boxes) {
        item.rate_boxes = { fail: '0%', failSub: 'at Trial Level', win: '100%', winSub: 'on Appeal' };
    }
    
    // Populate lawyers pivot mapping
    if (item.lawyers && item.lawyers.length > 0) {
        item.lawyers = item.lawyers.map((l: any) => ({
            id: l.id,
            name: l.name,
            firm: l.firm,
            initials: l.initials,
            avatar_color: l.avatar_color,
            outcome: l.pivot?.outcome ?? item.status
        }));
    } else {
        item.lawyers = [{
            id: editingLawyer.value?.id || 0,
            name: form.name || 'Current Attorney',
            firm: form.firm || '',
            initials: form.initials || '',
            avatar_color: form.avatar_color || '#1E3A54',
            outcome: item.status
        }];
    }
    
    tempCase.value = item;
    isCaseModalOpen.value = true;
};

// Sub-element inline form refs and actions
const newParty = ref({ name: '', role: '', status: '', isWinner: false });
const addParty = () => {
    if (!newParty.value.name || !newParty.value.role) {
        alert('Party Name and Role are required.');
        return;
    }
    if (!tempCase.value.parties) tempCase.value.parties = [];
    tempCase.value.parties.push({ ...newParty.value });
    newParty.value = { name: '', role: '', status: '', isWinner: false };
};

const newTimeline = ref({ date: '', title: '', action: '', status: 'initiated', badgeLabel: '', context: '' });
const addTimeline = () => {
    if (!newTimeline.value.date || !newTimeline.value.title || !newTimeline.value.action) {
        alert('Date, Event Title, and Action/Description are required.');
        return;
    }
    if (!tempCase.value.timeline) tempCase.value.timeline = [];
    tempCase.value.timeline.push({
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
    if (!tempCase.value.motions) tempCase.value.motions = [];
    tempCase.value.motions.push({
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
    if (!tempCase.value.issues) tempCase.value.issues = [];
    const nextNum = tempCase.value.issues.length + 1;
    tempCase.value.issues.push({
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
    if (!tempCase.value.documents) tempCase.value.documents = [];
    tempCase.value.documents.push({
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
    if (!tempCase.value.next_steps) tempCase.value.next_steps = [];
    tempCase.value.next_steps.push({ ...newNextStep.value });
    newNextStep.value = { icon: '⚖️', title: '', desc: '' };
};

const removeCase = (index: number) => {
    const caseName = form.cases[index]?.name || 'this case';
    if (confirm(`Are you sure you want to remove "${caseName}" from this profile?`)) {
        form.cases.splice(index, 1);
    }
};

const saveCase = () => {
    if (!tempCase.value.name || !tempCase.value.case_number) {
        alert('Case name and case number are required.');
        return;
    }
    if (!tempCase.value.lawyers || tempCase.value.lawyers.length === 0) {
        alert('Please attach at least one Attorney to this case.');
        return;
    }
    if (editingCaseIndex.value !== null) {
        form.cases[editingCaseIndex.value] = JSON.parse(JSON.stringify(tempCase.value));
    } else {
        form.cases.push(JSON.parse(JSON.stringify(tempCase.value)));
    }
    isCaseModalOpen.value = false;
};
const isUserModalOpen = ref(false);
const userSearchQuery = ref('');

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

const filteredUsers = computed(() => {
    if (!userSearchQuery.value.trim()) {
        return props.users;
    }
    const q = userSearchQuery.value.toLowerCase();
    return props.users.filter((u) => {
        const roleName = getRoleName(u.system).toLowerCase();
        return (
            u.name.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q) ||
            roleName.includes(q)
        );
    });
});

const selectedUserLabel = computed(() => {
    if (form.user_id === '' || form.user_id === null || form.user_id === undefined) {
        return 'None / Unassigned (Only admins can edit)';
    }
    const u = props.users.find((u) => u.id === Number(form.user_id));
    return u ? `${u.name} (${u.email}) [Role: ${getRoleName(u.system)}]` : 'None / Unassigned (Only admins can edit)';
});

const openUserSelect = () => {
    userSearchQuery.value = '';
    isUserModalOpen.value = true;
};

const selectUser = (userId: number | string) => {
    form.user_id = userId;
    isUserModalOpen.value = false;
};

// Available practice areas for selection
const availablePracticeAreas = [
    'Business Litigation',
    'Corporate Law',
    'Real Estate',
    'International Arbitration',
    'Intellectual Property',
    'Employment Law',
    'Criminal Defense',
    'Appellate Practice',
];

const openCreateModal = () => {
    editingLawyer.value = null;
    form.reset();
    form.clearErrors();

    // Set defaults
    form.name = '';
    form.title = '';
    form.firm = '';
    form.city = '';
    form.state = '';
    form.specialty = '';
    form.bio = '';
    form.avatar_color = '#1E3A54';
    form.initials = '';
    form.email = '';
    form.phone = '';
    form.website = '';
    form.linkedin = '';
    form.years_experience = 10;
    form.cases_count = 50;
    form.cases_won = 40;
    form.cases_lost = 5;
    form.cases_settled = 5;
    form.cases_active = 5;
    form.financial_recovery = '$5M+';
    form.fee_structure = 'Hourly Retainer';
    form.is_certified = false;
    form.rating = 5.0;
    form.availability = 'available';
    form.user_id = '';
    form.practice_areas = [];
    form.criminal_record = 'CLEARED';
    form.bar_discipline = 'CLEARED';
    form.trial_style = '';
    form.peer_reviews = {
        rating: '5.0 / 5.0',
        source: 'Martindale-Hubbell Peer Review',
        quote: '',
        author: '',
    };
    form.trial_style_details = {
        approach: '',
        forensics: '',
        global: '',
    };
    form.recent_activity = [];
    form.cases = [];

    isFormModalOpen.value = true;
};

const openEditModal = (lawyer: Lawyer) => {
    editingLawyer.value = lawyer;
    form.clearErrors();

    form.name = lawyer.name;
    form.title = lawyer.title;
    form.firm = lawyer.firm;
    form.city = lawyer.city;
    form.state = lawyer.state;
    form.specialty = lawyer.specialty;
    form.bio = lawyer.bio;
    form.avatar_color = lawyer.avatar_color;
    form.initials = lawyer.initials;
    form.email = lawyer.email;
    form.phone = lawyer.phone;
    form.website = lawyer.website;
    form.linkedin = lawyer.linkedin || '';
    form.years_experience = lawyer.years_experience;
    form.cases_count = lawyer.cases_count;
    form.cases_won = lawyer.cases_won;
    form.cases_lost = lawyer.cases_lost;
    form.cases_settled = lawyer.cases_settled;
    form.cases_active = lawyer.cases_active;
    form.financial_recovery = lawyer.financial_recovery || '';
    form.fee_structure = lawyer.fee_structure || '';
    form.is_certified = lawyer.is_certified;
    form.rating = lawyer.rating;
    form.availability = lawyer.availability;
    form.user_id = lawyer.user_id !== null ? lawyer.user_id : '';
    form.practice_areas = [...(lawyer.practice_areas || [])];
    form.criminal_record = lawyer.criminal_record;
    form.bar_discipline = lawyer.bar_discipline;
    form.trial_style = lawyer.trial_style || '';
    form.peer_reviews = lawyer.peer_reviews || {
        rating: '5.0 / 5.0',
        source: 'Martindale-Hubbell Peer Review',
        quote: '',
        author: '',
    };
    form.trial_style_details = lawyer.trial_style_details || {
        approach: '',
        forensics: '',
        global: '',
    };
    form.recent_activity = lawyer.recent_activity || [];
    form.cases = lawyer.cases ? JSON.parse(JSON.stringify(lawyer.cases)) : [];

    isFormModalOpen.value = true;
};

const submitForm = () => {
    // Auto generate initials if empty
    if (!form.initials && form.name) {
        const parts = form.name.split(' ');
        form.initials = parts
            .map((p) => p[0])
            .join('')
            .substring(0, 3)
            .toUpperCase();
    }

    // Normalize user_id
    const payload = {
        ...form.data(),
        user_id: form.user_id === '' ? null : Number(form.user_id),
    };

    if (editingLawyer.value) {
        form.transform(() => payload).post(
            `/admin/lawyers/${editingLawyer.value.id}`,
            {
                onSuccess: () => {
                    isFormModalOpen.value = false;
                    form.reset();
                },
            },
        );
    } else {
        form.transform(() => payload).post('/admin/lawyers', {
            onSuccess: () => {
                isFormModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const deleteLawyer = (lawyer: Lawyer) => {
    if (
        confirm(
            `Are you sure you want to permanently delete the profile for ${lawyer.name}?`,
        )
    ) {
        useForm({}).post(`/admin/lawyers/${lawyer.id}/delete`, {
            onSuccess: () => {
                alert('Lawyer profile deleted successfully.');
            },
        });
    }
};

// Generate HSL color options for selection
const colorPresets = [
    '#1E3A54', // Navy
    '#065F46', // Emerald
    '#7C2D12', // Rust
    '#1E1B4B', // Indigo
    '#581C87', // Purple
    '#701A75', // Fuchsia
    '#0F172A', // Slate
    '#0369A1', // Sky
];

const totalLawyersCount = computed(() => props.lawyers.length);
const totalAssignedCount = computed(
    () => props.lawyers.filter((l) => l.user_id !== null).length,
);
const totalUnassignedCount = computed(
    () => props.lawyers.filter((l) => l.user_id === null).length,
);
</script>

<template>
    <Head title="Attorneys Directory Management" />

    <div class="space-y-6">
        <!-- Toast Notification Summary -->
        <div
            v-if="$page.props.flash?.success"
            class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-left text-xs text-emerald-800 shadow-xs"
        >
            <CheckCircle class="h-5 w-5 shrink-0 text-emerald-600" />
            <div>{{ $page.props.flash.success }}</div>
        </div>

        <!-- Page Header Action Control -->
        <div
            class="flex flex-col justify-between gap-4 rounded-2xl border border-black/5 bg-white p-6 shadow-xs sm:flex-row sm:items-center"
        >
            <div class="text-left">
                <h1 class="font-serif text-xl font-bold text-neutral-800">
                    Attorney Registry
                </h1>
                <p class="mt-1 text-xs text-neutral-400">
                    Manage public profile metrics, bio data, and user portal
                    permissions.
                </p>
            </div>
            <div>
                <button
                    @click="openCreateModal"
                    class="flex cursor-pointer items-center gap-1.5 rounded-xl bg-[#C8961E] px-4 py-2 text-xs font-bold tracking-wider text-[#0A1929] uppercase shadow-sm transition-all hover:bg-[#DBA93E] hover:shadow"
                >
                    <Plus class="h-4 w-4" /> Add Lawyer Profile
                </button>
            </div>
        </div>

        <!-- KPI Mini Board -->
        <div class="grid grid-cols-3 gap-4">
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-[#C8961E]">
                    {{ totalLawyersCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Total Directory
                </div>
            </div>
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-emerald-600">
                    {{ totalAssignedCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Linked Profiles
                </div>
            </div>
            <div
                class="rounded-xl border border-black/5 bg-white p-4 text-left"
            >
                <div class="font-serif text-2xl font-bold text-neutral-500">
                    {{ totalUnassignedCount }}
                </div>
                <div
                    class="mt-1 text-[9px] font-semibold tracking-wider text-neutral-400 uppercase"
                >
                    Unassigned Profiles
                </div>
            </div>
        </div>

        <!-- Directory Registry Table Panel -->
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
                        placeholder="Filter by name, firm, location or specialties..."
                        class="w-full rounded-lg border border-black/8 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                    />
                </div>
                <div class="text-xs font-medium text-neutral-400">
                    Showing {{ filteredLawyers.length }} of
                    {{ lawyers.length }} profiles
                </div>
            </div>

            <!-- Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr
                            class="border-b border-neutral-100 bg-neutral-50/30 text-[9px] tracking-wider text-neutral-400 uppercase"
                        >
                            <th class="px-4 py-3">Attorney</th>
                            <th class="px-4 py-3">Firm / Specialty</th>
                            <th class="px-4 py-3">Bar Status</th>
                            <th class="px-4 py-3">Assigned User</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr
                            v-if="filteredLawyers.length === 0"
                            class="text-center text-neutral-400"
                        >
                            <td colspan="5" class="py-12">
                                No lawyer profiles match the search parameters.
                            </td>
                        </tr>
                        <tr
                            v-for="lawyer in filteredLawyers"
                            :key="lawyer.id"
                            class="transition-all hover:bg-neutral-50/40"
                        >
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg font-serif text-sm font-bold text-white shadow-inner"
                                        :style="{
                                            backgroundColor:
                                                lawyer.avatar_color,
                                        }"
                                    >
                                        {{ lawyer.initials }}
                                    </div>
                                    <div>
                                        <div
                                            class="text-xs font-bold text-neutral-800"
                                        >
                                            <Link
                                                :href="`/lawyers/${lawyer.slug}`"
                                                class="hover:text-[#DBA93E] hover:underline"
                                            >
                                                {{ lawyer.name }}
                                            </Link>
                                        </div>
                                        <div
                                            class="mt-0.5 text-[10px] text-neutral-400"
                                        >
                                            {{ lawyer.city }},
                                            {{ lawyer.state }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    class="leading-snug font-semibold text-neutral-700"
                                >
                                    {{ lawyer.firm }}
                                </div>
                                <div
                                    class="mt-0.5 max-w-[180px] truncate text-[10px] text-neutral-400"
                                    :title="lawyer.specialty"
                                >
                                    {{ lawyer.specialty }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-0.5">
                                    <span
                                        class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-600 uppercase"
                                    >
                                        ✓ Board Cert:
                                        {{ lawyer.is_certified ? 'Yes' : 'No' }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 font-mono text-[9px] text-neutral-400 uppercase"
                                    >
                                        Discipline: {{ lawyer.bar_discipline }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    v-if="lawyer.user"
                                    class="max-w-[160px] rounded-lg border border-indigo-100 bg-indigo-50 p-1.5"
                                >
                                    <div
                                        class="truncate leading-snug font-bold text-indigo-900"
                                    >
                                        {{ lawyer.user.name }}
                                    </div>
                                    <div
                                        class="mt-0.5 truncate text-[9px] leading-snug text-indigo-700/60"
                                    >
                                        {{ lawyer.user.email }}
                                    </div>
                                </div>
                                <div v-else class="text-neutral-400 italic">
                                    Unassigned (Admin only)
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div
                                    class="flex items-center justify-end gap-1.5"
                                >
                                    <button
                                        @click="openEditModal(lawyer)"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-neutral-100 hover:text-[#C8961E]"
                                        title="Edit Profile Details"
                                    >
                                        <Edit class="h-4.5 w-4.5" />
                                    </button>
                                    <!-- Delete option is hidden for bat (Moderators) and only visible to ghost/simp -->
                                    <button
                                        v-if="
                                            $page.props.auth.user?.system ===
                                                'ghost' ||
                                            $page.props.auth.user?.system ===
                                                'simp'
                                        "
                                        @click="deleteLawyer(lawyer)"
                                        class="cursor-pointer rounded p-1.5 text-neutral-600 transition-all hover:bg-red-50 hover:text-red-600"
                                        title="Delete Profile"
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

        <!-- Create & Edit Slideover Modal dialog -->
        <div
            v-if="isFormModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-sm"
        >
            <div
                class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white text-left shadow-2xl"
            >
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between bg-[#0A1929] px-6 py-4 text-white"
                >
                    <div>
                        <h3 class="font-serif text-base font-bold text-white">
                            {{
                                editingLawyer
                                    ? `Edit Profile: ${editingLawyer.name}`
                                    : 'Create New Lawyer Profile'
                            }}
                        </h3>
                        <p class="text-[10px] text-white/50">
                            Ensure all parameters comply with database
                            requirements.
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
                    class="flex-1 space-y-6 overflow-y-auto p-6"
                >
                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            1. Professional Details
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Full Attorney Name *</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
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
                                    >Title / Role *</label
                                >
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="e.g. Founding Shareholder"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                                <div
                                    v-if="form.errors.title"
                                    class="mt-1 text-[10px] text-red-500"
                                >
                                    {{ form.errors.title }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="md:col-span-1">
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Firm Name *</label
                                >
                                <input
                                    v-model="form.firm"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >City *</label
                                >
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >State Code (e.g. FL) *</label
                                >
                                <input
                                    v-model="form.state"
                                    type="text"
                                    maxlength="4"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs uppercase outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-xs font-bold text-neutral-700"
                                >Specialties (comma separated list) *</label
                            >
                            <input
                                v-model="form.specialty"
                                type="text"
                                placeholder="Complex Litigation, Corporate Separations"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Avatar Color HSL Preset *</label
                                >
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <button
                                        v-for="color in colorPresets"
                                        :key="color"
                                        type="button"
                                        @click="form.avatar_color = color"
                                        class="h-6 w-6 shrink-0 cursor-pointer rounded-full border border-black/10 focus:ring-2 focus:ring-[#C8961E] focus:ring-offset-2"
                                        :style="{ backgroundColor: color }"
                                    >
                                        <Check
                                            v-if="form.avatar_color === color"
                                            class="mx-auto h-3.5 w-3.5 text-white"
                                        />
                                    </button>
                                    <input
                                        v-model="form.avatar_color"
                                        type="text"
                                        class="w-24 rounded-lg border border-black/10 px-2 py-1 font-mono text-xs outline-none"
                                        required
                                    />
                                </div>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Avatar Initials *</label
                                >
                                <input
                                    v-model="form.initials"
                                    type="text"
                                    maxlength="4"
                                    placeholder="e.g. BB"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-xs font-bold text-neutral-700"
                                >Biography *</label
                            >
                            <textarea
                                v-model="form.bio"
                                rows="4"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            ></textarea>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            2. Contact details
                        </h4>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Email Address *</label
                                >
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Phone Number *</label
                                >
                                <input
                                    v-model="form.phone"
                                    type="text"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Website Domain *</label
                                >
                                <input
                                    v-model="form.website"
                                    type="text"
                                    placeholder="e.g. b2b.legal"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >LinkedIn Profile (optional)</label
                                >
                                <input
                                    v-model="form.linkedin"
                                    type="url"
                                    placeholder="https://linkedin.com/in/username"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            3. Professional Metrics & Credentials
                        </h4>

                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Years Active *</label
                                >
                                <input
                                    v-model.number="form.years_experience"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Martindale Rating *</label
                                >
                                <input
                                    v-model.number="form.rating"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="5"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                    required
                                />
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Availability *</label
                                >
                                <select
                                    v-model="form.availability"
                                    class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none"
                                >
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="unavailable">
                                        Unavailable
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-bold text-neutral-700"
                                    >Total Cases *</label
                                >
                                <input
                                    v-model.number="form.cases_count"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-1.5 text-xs outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-bold text-neutral-700"
                                    >Cases Won *</label
                                >
                                <input
                                    v-model.number="form.cases_won"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-1.5 text-xs outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-bold text-neutral-700"
                                    >Cases Lost *</label
                                >
                                <input
                                    v-model.number="form.cases_lost"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-1.5 text-xs outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-bold text-neutral-700"
                                    >Cases Settled *</label
                                >
                                <input
                                    v-model.number="form.cases_settled"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-1.5 text-xs outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-[10px] font-bold text-neutral-700"
                                    >Cases Active *</label
                                >
                                <input
                                    v-model.number="form.cases_active"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-lg border border-black/10 p-1.5 text-xs outline-none"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Financial Recoveries</label
                                >
                                <input
                                    v-model="form.financial_recovery"
                                    type="text"
                                    placeholder="e.g. $50M+"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Fee Structure</label
                                >
                                <input
                                    v-model="form.fee_structure"
                                    type="text"
                                    placeholder="e.g. Hourly + Contingency"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                class="flex items-center justify-between rounded-xl border border-neutral-100 bg-neutral-50 p-3.5"
                            >
                                <div>
                                    <label
                                        class="block text-xs font-bold text-neutral-700"
                                        >Florida Board Certification</label
                                    >
                                    <p class="text-[10px] text-neutral-400">
                                        Board-Certified in Business Litigation
                                    </p>
                                </div>
                                <input
                                    v-model="form.is_certified"
                                    type="checkbox"
                                    class="h-4 w-4 cursor-pointer rounded accent-[#C8961E]"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Courtroom Trial Style</label
                                >
                                <input
                                    v-model="form.trial_style"
                                    type="text"
                                    placeholder="Precise, evidence-first litigator"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            4. Verification & Background Logs
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Criminal Record Clearance Status *</label
                                >
                                <input
                                    v-model="form.criminal_record"
                                    type="text"
                                    placeholder="CLEARED"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Professional Bar Discipline Status *</label
                                >
                                <input
                                    v-model="form.bar_discipline"
                                    type="text"
                                    placeholder="CLEARED"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            5. Practice Areas Registry
                        </h4>
                        <div>
                            <label
                                class="mb-2 block text-xs font-bold text-neutral-700"
                                >Select Active Practice Areas</label
                            >
                            <div class="grid grid-cols-2 gap-2">
                                <label
                                    v-for="area in availablePracticeAreas"
                                    :key="area"
                                    class="flex cursor-pointer items-center gap-2 rounded-lg border border-black/5 bg-neutral-50/50 p-2.5 text-xs hover:border-neutral-200"
                                >
                                    <input
                                        type="checkbox"
                                        :value="area"
                                        v-model="form.practice_areas"
                                        class="h-3.5 w-3.5 rounded accent-[#C8961E]"
                                    />
                                    <span>{{ area }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Peer Reviews & Trial Style Details -->
                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            6. Peer Reviews & Trial Style Details
                        </h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Peer Review Rating</label
                                >
                                <input
                                    v-model="form.peer_reviews.rating"
                                    type="text"
                                    placeholder="e.g. 5.0 / 5.0"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Peer Review Source</label
                                >
                                <input
                                    v-model="form.peer_reviews.source"
                                    type="text"
                                    placeholder="e.g. Martindale-Hubbell Peer Review"
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Peer Review Author</label
                                >
                                <input
                                    v-model="form.peer_reviews.author"
                                    type="text"
                                    placeholder="e.g. CEO, Fortune 500 Co."
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Peer Review Quote</label
                                >
                                <textarea
                                    v-model="form.peer_reviews.quote"
                                    rows="2"
                                    placeholder="e.g. An exceptional litigator with template-setting work..."
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                ></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Trial Style: Approach</label
                                >
                                <textarea
                                    v-model="form.trial_style_details.approach"
                                    rows="2"
                                    placeholder="e.g. Methodical, evidence-driven..."
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Trial Style: Forensics</label
                                >
                                <textarea
                                    v-model="form.trial_style_details.forensics"
                                    rows="2"
                                    placeholder="e.g. Technical analysis, data-backed..."
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                ></textarea>
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold text-neutral-700"
                                    >Trial Style: Global</label
                                >
                                <textarea
                                    v-model="form.trial_style_details.global"
                                    rows="2"
                                    placeholder="e.g. Strong command of courtroom dynamics..."
                                    class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: Cases Manager -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b pb-1">
                            <h4
                                class="text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                            >
                                7. Cases Manager
                            </h4>
                            <button
                                type="button"
                                @click="openAddCase"
                                class="flex cursor-pointer items-center gap-1 rounded bg-[#C8961E] px-2 py-0.5 text-[10px] font-bold text-[#0A1929] uppercase hover:bg-[#DBA93E]"
                            >
                                <Plus class="h-3 w-3" /> Add Case
                            </button>
                        </div>

                        <div v-if="form.cases.length === 0" class="rounded-lg border border-dashed border-neutral-200 p-6 text-center text-xs text-neutral-400">
                            No cases added to this profile yet.
                        </div>

                        <div v-else class="space-y-2">
                            <div
                                v-for="(c, idx) in form.cases"
                                :key="idx"
                                class="flex items-center justify-between rounded-lg border border-neutral-100 bg-neutral-50/50 p-3"
                            >
                                <div>
                                    <div class="font-bold text-neutral-800 text-xs">{{ c.name }}</div>
                                    <div class="text-[10px] text-neutral-400">
                                        {{ c.case_number }} | {{ c.jurisdiction }} | {{ c.year }} | Status: <span class="capitalize font-semibold text-neutral-700">{{ c.status }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        @click="openEditCase(idx)"
                                        class="cursor-pointer rounded p-1 text-neutral-600 hover:bg-neutral-100 hover:text-[#C8961E]"
                                    >
                                        <Edit class="h-3.5 w-3.5" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeCase(idx)"
                                        class="cursor-pointer rounded p-1 text-neutral-600 hover:bg-red-50 hover:text-red-600"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 8: User Assignment (Profile Owner) -->
                    <div class="space-y-4">
                        <h4
                            class="border-b pb-1 text-[10px] font-bold tracking-wider text-neutral-400 uppercase"
                        >
                            8. User Assignment (Profile Owner)
                        </h4>

                        <div>
                            <label
                                class="mb-1 block text-xs font-bold text-neutral-700"
                                >Assigned User Account</label
                            >
                            <button
                                type="button"
                                @click="openUserSelect"
                                class="flex w-full items-center justify-between cursor-pointer rounded-lg border border-black/10 bg-white p-2.5 text-xs outline-none transition-all hover:border-[#C8961E] focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E] text-left"
                            >
                                <span class="truncate pr-4" :class="form.user_id ? 'text-neutral-800 font-medium' : 'text-neutral-400 italic'">
                                    {{ selectedUserLabel }}
                                </span>
                                <span class="flex items-center justify-center shrink-0 text-neutral-400 w-4 h-4">
                                    <User class="w-3.5 h-3.5" />
                                </span>
                            </button>
                            <p
                                class="mt-1.5 flex items-start gap-1 text-[10px] text-neutral-400"
                            >
                                <AlertCircle
                                    class="mt-0.5 h-3 w-3 shrink-0 text-amber-500"
                                />
                                When assigned, this specific user will be
                                authorized to log in and manage their biography,
                                telephone, and social profiles directly.
                                Restricted statistics remain read-only.
                            </p>
                        </div>
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
                            form.processing
                                ? 'Saving...'
                                : 'Save Profile Record'
                        }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Case Sub-modal dialog -->
        <div
            v-if="isCaseModalOpen"
            class="fixed inset-0 z-60 flex items-center justify-center overflow-y-auto bg-black/60 p-4 backdrop-blur-xs"
        >
            <div
                class="flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white text-left shadow-2xl"
            >
                <div class="flex items-center justify-between bg-neutral-800 px-4 py-3 text-white">
                    <h3 class="font-serif text-sm font-bold text-white">
                        {{ editingCaseIndex !== null ? 'Edit Case Record' : 'Add Case Record' }}
                    </h3>
                    <button
                        type="button"
                        @click="isCaseModalOpen = false"
                        class="cursor-pointer text-white/60 hover:text-white"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto p-4">
                    <!-- Attorneys of Record -->
                    <div class="space-y-3 border-b pb-3.5">
                        <label class="block text-[10px] font-bold text-neutral-700 uppercase">1. Attorneys of Record & Outcomes</label>
                        
                        <div v-if="tempCase.lawyers && tempCase.lawyers.length > 0" class="space-y-2">
                            <div
                                v-for="(lawyer, idx) in tempCase.lawyers"
                                :key="lawyer.id"
                                class="flex items-center justify-between gap-3 rounded-lg border border-neutral-100 bg-neutral-50 p-2 text-xs"
                            >
                                <div class="flex items-center gap-2 min-w-0">
                                    <div
                                        class="flex h-7.5 w-7.5 shrink-0 items-center justify-center rounded-lg font-serif text-[10px] font-bold text-white shadow-inner"
                                        :style="{ backgroundColor: lawyer.avatar_color || '#1E3A54' }"
                                    >
                                        {{ lawyer.initials || 'AT' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ lawyer.name }}</div>
                                        <div class="text-[9px] text-neutral-400 truncate">{{ lawyer.firm }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select
                                        v-model="lawyer.outcome"
                                        class="cursor-pointer rounded border border-neutral-200 bg-white px-1.5 py-0.5 text-xs outline-none focus:border-[#C8961E]"
                                    >
                                        <option value="won">Won</option>
                                        <option value="lost">Lost</option>
                                        <option value="settled">Settled</option>
                                        <option value="active">Active</option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="tempCase.lawyers.splice(idx, 1)"
                                        class="cursor-pointer font-bold text-red-500 hover:text-red-700 p-1"
                                    >
                                        <X class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-neutral-400 italic text-[11px] py-1">
                            No attorneys attached. Please attach at least one below.
                        </div>

                        <div>
                            <button
                                type="button"
                                @click="openLawyerSelectForCase"
                                class="flex items-center justify-center gap-1.5 cursor-pointer rounded-lg border border-dashed border-[#C8961E]/40 hover:border-[#C8961E] bg-[#C8961E]/5 hover:bg-[#C8961E]/10 p-2 w-full text-xs font-semibold text-[#C8961E] transition-all"
                            >
                                <Plus class="w-3.5 h-3.5" /> Attach Attorney to Case
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Case Name *</label>
                            <input
                                v-model="tempCase.name"
                                type="text"
                                placeholder="e.g. Acme Corp v. Beta LLC"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Case Number *</label>
                            <input
                                v-model="tempCase.case_number"
                                type="text"
                                placeholder="e.g. 2026-CA-001234"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Jurisdiction *</label>
                            <input
                                v-model="tempCase.jurisdiction"
                                type="text"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Court Name *</label>
                            <input
                                v-model="tempCase.court"
                                type="text"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Type *</label>
                            <select
                                v-model="tempCase.type"
                                class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            >
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="appellate">Appellate</option>
                                <option value="arbitration">Arbitration</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Type Label *</label>
                            <input
                                v-model="tempCase.type_label"
                                type="text"
                                placeholder="e.g. Commercial Litigation"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Year *</label>
                            <input
                                v-model.number="tempCase.year"
                                type="number"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Status *</label>
                            <select
                                v-model="tempCase.status"
                                class="w-full cursor-pointer rounded-lg border border-black/10 bg-white p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            >
                                <option value="decided">Decided</option>
                                <option value="settled">Settled</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Winning Party (optional)</label>
                            <input
                                v-model="tempCase.won_party"
                                type="text"
                                placeholder="e.g. Plaintiff (Acme Corp)"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Motions Filed Count</label>
                            <input
                                v-model.number="tempCase.motions_count"
                                type="number"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-bold text-neutral-700">Motion Success Rate</label>
                            <input
                                v-model="tempCase.motion_success_rate"
                                type="text"
                                placeholder="e.g. 75%"
                                class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold text-neutral-700">Case Summary</label>
                        <textarea
                            v-model="tempCase.summary"
                            rows="2"
                            placeholder="Brief description of the dispute and outcome..."
                            class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                        ></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-[10px] font-bold text-neutral-700">Key Finding/Ruling</label>
                        <textarea
                            v-model="tempCase.key_finding"
                            rows="2"
                            placeholder="Key legal precedent or ruling..."
                            class="w-full rounded-lg border border-black/10 p-2 text-xs outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                        ></textarea>
                    </div>

                    <!-- Sub-elements: Parties, Timeline, Motions, Issues, Documents, Next Steps -->
                    <div class="border-t pt-3 space-y-4">
                        <!-- Litigation Parties -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Litigation Parties</label>
                                <span class="text-[9px] text-neutral-400">Total: {{ tempCase.parties?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.parties && tempCase.parties.length > 0" class="space-y-1 mb-2">
                                <div v-for="(p, pIdx) in tempCase.parties" :key="pIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div>
                                        <span class="font-bold text-neutral-800">{{ p.name }}</span>
                                        <span class="text-[9px] text-neutral-400 ml-1">({{ p.role }}) - {{ p.status }} {{ p.isWinner ? '🏆' : '' }}</span>
                                    </div>
                                    <button type="button" @click="tempCase.parties.splice(pIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold">Remove</button>
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
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Docket Timeline History</label>
                                <span class="text-[9px] text-neutral-400">Events: {{ tempCase.timeline?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.timeline && tempCase.timeline.length > 0" class="space-y-1 mb-2">
                                <div v-for="(t, tIdx) in tempCase.timeline" :key="tIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ t.title }}</div>
                                        <div class="text-[9px] text-neutral-400">{{ t.date }} | Type: {{ t.badgeLabel }} | Status: {{ t.status }}</div>
                                    </div>
                                    <button type="button" @click="tempCase.timeline.splice(tIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
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

                        <!-- Motion Tracker & Rate Boxes -->
                        <div>
                            <label class="text-[10px] font-bold text-neutral-500 uppercase block mb-1.5">Motion Success Rates</label>
                            <div class="grid grid-cols-2 gap-2 bg-neutral-50 p-2 rounded-lg border border-neutral-100 mb-3">
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Trial Result %</label>
                                    <input v-model="tempCase.rate_boxes.fail" type="text" placeholder="0%" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Trial Result Sub-label</label>
                                    <input v-model="tempCase.rate_boxes.failSub" type="text" placeholder="at Trial Level" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Appellate Result %</label>
                                    <input v-model="tempCase.rate_boxes.win" type="text" placeholder="100%" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-neutral-600">Appellate Sub-label</label>
                                    <input v-model="tempCase.rate_boxes.winSub" type="text" placeholder="on Appeal" class="w-full rounded border bg-white p-1 text-[11px]" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Motion List</label>
                                <span class="text-[9px] text-neutral-400">Motions: {{ tempCase.motions?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.motions && tempCase.motions.length > 0" class="space-y-1 mb-2">
                                <div v-for="(m, mIdx) in tempCase.motions" :key="mIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ m.motion }}</div>
                                        <div class="text-[9px] text-neutral-400">{{ m.date }} | Ruling: {{ m.resultLabel }}</div>
                                    </div>
                                    <button type="button" @click="tempCase.motions.splice(mIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
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
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Core Legal Issues</label>
                                <span class="text-[9px] text-neutral-400">Issues: {{ tempCase.issues?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.issues && tempCase.issues.length > 0" class="space-y-1 mb-2">
                                <div v-for="(i, iIdx) in tempCase.issues" :key="iIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800">{{ i.num }}. {{ i.title }}</div>
                                        <div class="text-[9px] text-neutral-400 truncate">{{ i.desc }}</div>
                                    </div>
                                    <button type="button" @click="tempCase.issues.splice(iIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
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

                        <!-- Court Documents on Record -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Court Documents on Record</label>
                                <span class="text-[9px] text-neutral-400">Docs: {{ tempCase.documents?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.documents && tempCase.documents.length > 0" class="space-y-1 mb-2">
                                <div v-for="(d, dIdx) in tempCase.documents" :key="dIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800 truncate">{{ d.name }}</div>
                                        <div class="text-[9px] text-neutral-400">Filed: {{ d.date }} | Type: {{ d.typeLabel }}</div>
                                    </div>
                                    <button type="button" @click="tempCase.documents.splice(dIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
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

                        <!-- Post-Opinion Actions & Next Steps -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-neutral-500 uppercase">Post-Opinion Actions & Next Steps</label>
                                <span class="text-[9px] text-neutral-400">Steps: {{ tempCase.next_steps?.length || 0 }}</span>
                            </div>
                            <div v-if="tempCase.next_steps && tempCase.next_steps.length > 0" class="space-y-1 mb-2">
                                <div v-for="(n, nIdx) in tempCase.next_steps" :key="nIdx" class="flex items-center justify-between rounded-lg bg-neutral-50 p-2 text-xs border border-neutral-100">
                                    <div class="min-w-0">
                                        <div class="font-bold text-neutral-800">{{ n.icon }} {{ n.title }}</div>
                                        <div class="text-[9px] text-neutral-400 truncate">{{ n.desc }}</div>
                                    </div>
                                    <button type="button" @click="tempCase.next_steps.splice(nIdx, 1)" class="text-red-500 hover:text-red-700 text-[10px] font-semibold shrink-0 ml-2">Remove</button>
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
                </div>

                <div class="flex items-center justify-end gap-2 bg-neutral-50 px-4 py-3 border-t">
                    <button
                        type="button"
                        @click="isCaseModalOpen = false"
                        class="cursor-pointer rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveCase"
                        class="cursor-pointer rounded-lg bg-neutral-800 px-4 py-1.5 text-xs font-bold text-white hover:bg-neutral-900"
                    >
                        Save Case
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom User Selection Modal (z-70) -->
        <div
            v-if="isUserModalOpen"
            class="fixed inset-0 z-70 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        >
            <div
                class="flex max-h-[80vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white text-left shadow-2xl border border-neutral-100"
            >
                <!-- Header -->
                <div class="flex items-center justify-between bg-[#0A1929] px-4 py-3.5 text-white">
                    <div>
                        <h3 class="font-serif text-sm font-bold text-white">
                            Assign User Account
                        </h3>
                        <p class="text-[10px] text-white/50">
                            Search and select a user to link with this profile.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="isUserModalOpen = false"
                        class="cursor-pointer text-white/60 hover:text-white transition-colors"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <!-- Search box -->
                <div class="border-b border-neutral-100 bg-neutral-50/50 p-4">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                            <Search class="h-4 w-4" />
                        </span>
                        <input
                            v-model="userSearchQuery"
                            type="text"
                            placeholder="Search by name, email, or role..."
                            class="w-full rounded-lg border border-black/10 bg-white py-2 pr-4 pl-9 text-xs transition-all outline-none focus:border-[#C8961E] focus:ring-1 focus:ring-[#C8961E]"
                            autofocus
                        />
                    </div>
                </div>

                <!-- User List -->
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    <!-- Option to clear assignment -->
                    <button
                        type="button"
                        @click="selectUser('')"
                        class="flex w-full items-center justify-between rounded-lg p-2.5 text-xs transition-all hover:bg-neutral-50 text-left"
                    >
                        <div class="flex flex-col text-neutral-500 italic">
                            <div class="font-bold text-neutral-600">None / Unassigned</div>
                            <div class="text-[9px] text-neutral-400">Remove profile assignment</div>
                        </div>
                        <Check v-if="form.user_id === '' || form.user_id === null || form.user_id === undefined" class="h-4 w-4 text-[#C8961E]" />
                    </button>

                    <div class="border-t border-neutral-100 my-1"></div>

                    <!-- User items -->
                    <div v-if="filteredUsers.length === 0" class="py-8 text-center text-xs text-neutral-400">
                        No users found matching "{{ userSearchQuery }}"
                    </div>
                    <button
                        v-for="u in filteredUsers"
                        :key="u.id"
                        type="button"
                        @click="selectUser(u.id)"
                        class="flex w-full items-center justify-between rounded-lg p-2.5 text-xs transition-all text-left hover:bg-neutral-50"
                        :class="{ 'bg-[#C8961E]/5': Number(form.user_id) === u.id }"
                    >
                        <div class="min-w-0">
                            <div class="font-bold text-neutral-800 truncate flex items-center gap-1.5">
                                {{ u.name }}
                                <span
                                    class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider"
                                    :class="{
                                        'bg-slate-900 border-slate-700 text-slate-200': u.system === 'ghost',
                                        'bg-purple-50 text-purple-700 border border-purple-200': u.system === 'simp',
                                        'bg-blue-50 text-blue-700 border border-blue-200': u.system === 'bat',
                                        'bg-sky-50 text-sky-700 border border-sky-200': u.system === 'bip',
                                        'bg-neutral-50 text-neutral-600 border border-neutral-200': u.system === 'god',
                                    }"
                                >
                                    {{ getRoleName(u.system) }}
                                </span>
                            </div>
                            <div class="text-[10px] text-neutral-400 truncate">{{ u.email }}</div>
                        </div>
                        <Check v-if="Number(form.user_id) === u.id" class="h-4 w-4 text-[#C8961E]" />
                    </button>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-neutral-100 bg-neutral-50 px-4 py-3">
                    <div class="text-[10px] text-neutral-400">
                        {{ filteredUsers.length }} users available
                    </div>
                    <button
                        type="button"
                        @click="isUserModalOpen = false"
                        class="cursor-pointer rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Attorney Selection Modal for Case -->
        <div
            v-if="isLawyerModalOpen"
            class="fixed inset-0 z-70 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
        >
            <div
                class="flex max-h-[75vh] w-full max-w-md flex-col overflow-hidden rounded-xl bg-white text-left shadow-2xl border border-neutral-100"
            >
                <div class="flex items-center justify-between bg-neutral-800 px-4 py-3 text-white">
                    <div>
                        <h3 class="font-serif text-sm font-bold text-white">
                            Select Attorney to Attach
                        </h3>
                    </div>
                    <button
                        type="button"
                        @click="isLawyerModalOpen = false"
                        class="cursor-pointer text-white/60 hover:text-white"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <div class="border-b border-neutral-100 bg-neutral-50/50 p-3">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                            <Search class="h-3.5 w-3.5" />
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
                    <div v-if="filteredLawyersForCase.length === 0" class="py-6 text-center text-xs text-neutral-400">
                        No attorneys found matching "{{ lawyerSearchQuery }}"
                    </div>
                    <button
                        v-for="l in filteredLawyersForCase"
                        :key="l.id"
                        type="button"
                        @click="selectLawyerForCase(l)"
                        class="flex w-full items-center justify-between rounded-lg p-2 text-xs transition-all text-left hover:bg-neutral-50"
                        :style="{ opacity: tempCase.lawyers && tempCase.lawyers.some((lawyer: any) => lawyer.id === l.id) ? '0.6' : '1.0' }"
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
                        <Check v-if="tempCase.lawyers && tempCase.lawyers.some((lawyer: any) => lawyer.id === l.id)" class="h-3.5 w-3.5 text-[#C8961E]" />
                    </button>
                </div>

                <div class="flex items-center justify-between border-t border-neutral-100 bg-neutral-50 px-4 py-2.5">
                    <button
                        type="button"
                        @click="isLawyerModalOpen = false"
                        class="cursor-pointer rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 ml-auto"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
