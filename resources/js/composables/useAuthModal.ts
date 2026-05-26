import { ref } from 'vue';

export const isAuthModalOpen = ref(false);
export const authModalTab = ref<'login' | 'signup'>('login');
export const authUserRole = ref<'client' | 'attorney'>('client');

export const openAuthModal = (tab: 'login' | 'signup' = 'login') => {
    authModalTab.value = tab;
    isAuthModalOpen.value = true;
};

export const closeAuthModal = () => {
    isAuthModalOpen.value = false;
};
