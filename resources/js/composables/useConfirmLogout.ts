import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const isConfirmOpen = ref(false);
const onConfirm = ref<(() => void) | null>(null);

export function useConfirmLogout() {
    const triggerLogout = (customCallback?: () => void) => {
        isConfirmOpen.value = true;
        onConfirm.value = () => {
            if (customCallback) {
                customCallback();
            } else {
                useForm({}).post('/logout', {
                    onSuccess: () => {
                        window.location.reload();
                    },
                });
            }

            isConfirmOpen.value = false;
        };
    };

    const confirm = () => {
        if (onConfirm.value) {
            onConfirm.value();
        }
    };

    const cancel = () => {
        isConfirmOpen.value = false;
        onConfirm.value = null;
    };

    return {
        isConfirmOpen,
        triggerLogout,
        confirm,
        cancel,
    };
}
