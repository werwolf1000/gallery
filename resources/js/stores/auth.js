import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import api from '../api';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const loaded = ref(false);

    const isAuthenticated = computed(() => !!user.value);
    const isAdmin = computed(() => user.value?.role === 'admin');
    const initials = computed(() => {
        if (!user.value?.name) return '?';
        return user.value.name
            .split(' ')
            .map((part) => part[0])
            .join('')
            .slice(0, 2)
            .toUpperCase();
    });

    async function fetchUser() {
        try {
            const { data } = await api.get('/user');
            user.value = data;
        } catch {
            user.value = null;
        } finally {
            loaded.value = true;
        }
    }

    async function login(credentials) {
        await api.get('/sanctum/csrf-cookie');
        const { data } = await api.post('/login', credentials);
        user.value = data;
        return data;
    }

    async function register(payload) {
        await api.get('/sanctum/csrf-cookie');
        const { data } = await api.post('/register', payload);
        user.value = data;
        return data;
    }

    async function logout() {
        await api.post('/logout');
        user.value = null;
    }

    return {
        user,
        loaded,
        isAuthenticated,
        isAdmin,
        initials,
        fetchUser,
        login,
        register,
        logout,
    };
});
