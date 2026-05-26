<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import {
    isAuthModalOpen,
    authModalTab,
    authUserRole,
    closeAuthModal,
} from '@/composables/useAuthModal';

const loginForm = useForm({
    email: '',
    password: '',
    remember: true,
});

const signupForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const handleLogin = () => {
    loginForm.post('/login', {
        onSuccess: () => {
            closeAuthModal();
            window.location.reload();
        },
    });
};

const handleSignup = () => {
    signupForm.password_confirmation = signupForm.password;
    signupForm.post('/register', {
        onSuccess: () => {
            closeAuthModal();
            window.location.reload();
        },
    });
};

const selectRole = (role: 'client' | 'attorney') => {
    authUserRole.value = role;
};
</script>

<template>
    <div
        v-if="isAuthModalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <!-- Overlay backdrop -->
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-xs"
            @click="closeAuthModal"
        ></div>

        <!-- Modal Dialog Box -->
        <div
            class="relative z-10 w-full max-w-[400px] animate-[fadeUp_0.2s_ease-out] rounded-2xl border border-black/5 bg-white p-6 text-[#16161A] shadow-2xl"
        >
            <button
                @click="closeAuthModal"
                class="absolute top-4 right-4 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-neutral-100 text-neutral-400 transition-all hover:bg-neutral-200 hover:text-[#16161A]"
            >
                <X class="h-4 w-4" />
            </button>

            <!-- Branding & Title Header -->
            <div
                class="mb-1 text-center font-serif text-xl font-bold text-[#0A1929]"
            >
                Lex<span class="text-[#C8961E]">Find</span>
            </div>
            <h3
                class="mb-1 text-center font-serif text-base font-bold text-neutral-800"
            >
                {{
                    authModalTab === 'login'
                        ? 'Welcome back'
                        : 'Create an account'
                }}
            </h3>
            <p class="mb-6 text-center text-xs text-neutral-400">
                {{
                    authModalTab === 'login'
                        ? 'Sign in to access your dashboard'
                        : 'Join the verified legal intelligence platform'
                }}
            </p>

            <!-- Sign In vs Register Toggle Tab -->
            <div class="mb-6 flex rounded-lg bg-neutral-100 p-1 text-sm">
                <button
                    @click="authModalTab = 'login'"
                    :class="[
                        'flex-1 cursor-pointer rounded-md py-1.5 text-xs font-medium transition-all',
                        authModalTab === 'login'
                            ? 'bg-white font-semibold text-[#0A1929] shadow-sm'
                            : 'text-neutral-500',
                    ]"
                >
                    Sign In
                </button>
                <button
                    @click="authModalTab = 'signup'"
                    :class="[
                        'flex-1 cursor-pointer rounded-md py-1.5 text-xs font-medium transition-all',
                        authModalTab === 'signup'
                            ? 'bg-white font-semibold text-[#0A1929] shadow-sm'
                            : 'text-neutral-500',
                    ]"
                >
                    Register
                </button>
            </div>

            <!-- Login Submission Form -->
            <form
                v-if="authModalTab === 'login'"
                @submit.prevent="handleLogin"
                class="space-y-4 text-left"
            >
                <div>
                    <label
                        class="mb-1.5 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >Email address</label
                    >
                    <input
                        type="email"
                        v-model="loginForm.email"
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-transparent bg-neutral-100 px-3 py-2 text-xs transition-all outline-none focus:border-neutral-300 focus:bg-white"
                        required
                    />
                    <span
                        v-if="loginForm.errors.email"
                        class="mt-1 block text-xs text-red-500"
                        >{{ loginForm.errors.email }}</span
                    >
                </div>
                <div>
                    <label
                        class="mb-1.5 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >Password</label
                    >
                    <input
                        type="password"
                        v-model="loginForm.password"
                        placeholder="••••••••"
                        class="w-full rounded-lg border border-transparent bg-neutral-100 px-3 py-2 text-xs transition-all outline-none focus:border-neutral-300 focus:bg-white"
                        required
                    />
                    <span
                        v-if="loginForm.errors.password"
                        class="mt-1 block text-xs text-red-500"
                        >{{ loginForm.errors.password }}</span
                    >
                </div>
                <button
                    type="submit"
                    :disabled="loginForm.processing"
                    class="flex w-full cursor-pointer items-center justify-center rounded-xl bg-[#0A1929] py-2.5 text-xs font-semibold text-[#DBA93E] transition-all hover:bg-[#1E3A54]"
                >
                    <span
                        v-if="loginForm.processing"
                        class="h-4 w-4 animate-spin rounded-full border-2 border-[#DBA93E] border-t-transparent"
                    ></span>
                    <span v-else>Sign In to LexFind →</span>
                </button>
            </form>

            <!-- Signup Submission Form -->
            <form
                v-else
                @submit.prevent="handleSignup"
                class="space-y-4 text-left"
            >
                <div>
                    <label
                        class="mb-1.5 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >Full Name</label
                    >
                    <input
                        type="text"
                        v-model="signupForm.name"
                        placeholder="John Doe"
                        class="w-full rounded-lg border border-transparent bg-neutral-100 px-3 py-2 text-xs transition-all outline-none focus:border-neutral-300 focus:bg-white"
                        required
                    />
                    <span
                        v-if="signupForm.errors.name"
                        class="mt-1 block text-xs text-red-500"
                        >{{ signupForm.errors.name }}</span
                    >
                </div>
                <div>
                    <label
                        class="mb-1.5 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >Email address</label
                    >
                    <input
                        type="email"
                        v-model="signupForm.email"
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-transparent bg-neutral-100 px-3 py-2 text-xs transition-all outline-none focus:border-neutral-300 focus:bg-white"
                        required
                    />
                    <span
                        v-if="signupForm.errors.email"
                        class="mt-1 block text-xs text-red-500"
                        >{{ signupForm.errors.email }}</span
                    >
                </div>
                <div>
                    <label
                        class="mb-1.5 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >Password</label
                    >
                    <input
                        type="password"
                        v-model="signupForm.password"
                        placeholder="Min. 8 characters"
                        class="w-full rounded-lg border border-transparent bg-neutral-100 px-3 py-2 text-xs transition-all outline-none focus:border-neutral-300 focus:bg-white"
                        required
                    />
                    <span
                        v-if="signupForm.errors.password"
                        class="mt-1 block text-xs text-red-500"
                        >{{ signupForm.errors.password }}</span
                    >
                </div>

                <div>
                    <label
                        class="mb-2 block text-[9px] font-bold tracking-wider text-neutral-400 uppercase"
                        >I am a:</label
                    >
                    <div class="mb-2 grid grid-cols-2 gap-3">
                        <div
                            @click="selectRole('client')"
                            :class="[
                                'cursor-pointer rounded-xl border-2 p-3 text-center transition-all',
                                authUserRole === 'client'
                                    ? 'border-[#0A1929] bg-[#0A1929]/5'
                                    : 'border-neutral-200 hover:border-neutral-300',
                            ]"
                        >
                            <div class="mb-1 text-lg">👤</div>
                            <div class="text-xs font-bold text-neutral-800">
                                Client
                            </div>
                            <div
                                class="mt-0.5 text-[9px] leading-tight text-neutral-400"
                            >
                                Find a lawyer for my case
                            </div>
                        </div>
                        <div
                            @click="selectRole('attorney')"
                            :class="[
                                'cursor-pointer rounded-xl border-2 p-3 text-center transition-all',
                                authUserRole === 'attorney'
                                    ? 'border-[#0A1929] bg-[#0A1929]/5'
                                    : 'border-neutral-200 hover:border-neutral-300',
                            ]"
                        >
                            <div class="mb-1 text-lg">⚖️</div>
                            <div class="text-xs font-bold text-neutral-800">
                                Attorney
                            </div>
                            <div
                                class="mt-0.5 text-[9px] leading-tight text-neutral-400"
                            >
                                Manage my profile & cases
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="signupForm.processing"
                    class="flex w-full cursor-pointer items-center justify-center rounded-xl bg-[#0A1929] py-2.5 text-xs font-semibold text-[#DBA93E] transition-all hover:bg-[#1E3A54]"
                >
                    <span
                        v-if="signupForm.processing"
                        class="h-4 w-4 animate-spin rounded-full border-2 border-[#DBA93E] border-t-transparent"
                    ></span>
                    <span v-else>Create Account →</span>
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
