<script setup>
import { RouterLink, useRoute } from 'vue-router';
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import UserMenuDropdown from './UserMenuDropdown.vue';

const route = useRoute();
const auth = useAuthStore();

const userMenuItems = [
    { to: '/orders', label: 'Мои заказы', icon: 'fa-list' },
    { to: '/admin', label: 'Админка', icon: 'fa-sliders-h', adminOnly: true },
    { label: 'Выйти', icon: 'fa-sign-out-alt' },
];

const navItems = [
    { to: '/', label: 'Главная', match: (path) => path === '/' && !route.hash },
    { to: '/apartments', label: 'Квартиры', match: (path) => path.startsWith('/apartments') },
    { to: '/purchase', label: 'Покупка', match: (path) => path.startsWith('/purchase') },
    { to: '/rent', label: 'Аренда', match: (path) => path.startsWith('/rent') },
    { to: '/izhs', label: 'Строительство ИЖС', match: (path) => path.startsWith('/izhs') },
    { to: '/home-staging', label: 'Хоумстейджинг', match: (path) => path.startsWith('/home-staging') },
    { to: '/renovation', label: 'Ремонт', match: (path) => path.startsWith('/renovation') },
    { to: '/', hash: '#duties', label: 'Наши обязательства', match: (path) => path === '/' && route.hash === '#duties' },
    { to: '/about', label: 'О нас', match: (path) => path.startsWith('/about') },
    { to: '/', hash: '#testimonials', label: 'Отзывы', match: (path) => path === '/' && route.hash === '#testimonials' },
    { to: '/orders', label: 'Мои заказы', match: (path) => path.startsWith('/orders'), auth: true },
];

const visibleNav = computed(() => navItems.filter((item) => !item.auth || auth.isAuthenticated));

function isActive(item) {
    return item.match(route.path);
}

function navTarget(item) {
    return item.hash ? { path: item.to, hash: item.hash } : item.to;
}

function navKey(item) {
    return item.hash ? `${item.to}${item.hash}` : item.to;
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
                        <UserMenuDropdown :items="userMenuItems" />
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
                    :key="navKey(item)"
                    :to="navTarget(item)"
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

.right-actions .btn-gold-circle {
    vertical-align: middle;
}
</style>
