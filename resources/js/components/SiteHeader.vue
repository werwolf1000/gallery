<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router';
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const auth = useAuthStore();

const navItems = [
    { to: '/', label: 'Главная', match: (path) => path === '/' },
    { to: '/apartments', label: 'Квартиры', match: (path) => path.startsWith('/apartments') },
    { to: '/purchase', label: 'Покупка', match: (path) => path.startsWith('/purchase') },
    { to: '/rent', label: 'Аренда', match: (path) => path.startsWith('/rent') },
    { to: '/izhs', label: 'Строительство ИЖС', match: (path) => path.startsWith('/izhs') },
    { to: '/home-staging', label: 'Хоумстейджинг', match: (path) => path.startsWith('/home-staging') },
    { to: '/renovation', label: 'Ремонт', match: (path) => path.startsWith('/renovation') },
    { to: '/orders', label: 'Мои заказы', match: (path) => path.startsWith('/orders'), auth: true },
];

const visibleNav = computed(() => navItems.filter((item) => !item.auth || auth.isAuthenticated));

function isActive(item) {
    return item.match(route.path);
}
</script>

<template>
    <div class="header">
        <div class="container">
            <div class="header-row">
                <div class="logo-area">
                    <RouterLink to="/">
                        <h1>GALLERY OF SPACES</h1>
                        <p>Галерея Пространств</p>
                    </RouterLink>
                </div>
                <div class="right-actions">
                    <template v-if="auth.isAuthenticated">
                        <RouterLink v-if="auth.isAdmin" to="/admin" class="btn-ghost">Админка</RouterLink>
                        <RouterLink to="/orders" class="user-avatar" :title="auth.user?.name">{{ auth.initials }}</RouterLink>
                        <button class="btn-ghost" type="button" @click="auth.logout()">Выйти</button>
                    </template>
                    <template v-else>
                        <RouterLink to="/login" class="btn-ghost">Вход</RouterLink>
                        <RouterLink to="/register" class="btn-gold-circle">+</RouterLink>
                    </template>
                </div>
            </div>
            <div class="nav-menu">
                <RouterLink
                    v-for="item in visibleNav"
                    :key="item.to"
                    :to="item.to"
                    :class="{ active: isActive(item) }"
                >
                    {{ item.label }}
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo-area a {
    text-decoration: none;
    color: inherit;
}
</style>
