<script setup>
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import '../../css/admin.css';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const menu = [
    { to: '/admin/orders', icon: 'fa-shopping-bag', label: 'Заказы' },
    { to: '/admin/users', icon: 'fa-users', label: 'Пользователи' },
    { to: '/admin/properties', icon: 'fa-building', label: 'Недвижимость' },
    { to: '/admin/slider', icon: 'fa-images', label: 'Слайдер' },
];

async function logout() {
    await auth.logout();
    router.push('/');
}
</script>

<template>
    <div class="header">
        <div class="container">
            <div class="header-row">
                <div class="logo-area">
                    <RouterLink to="/admin">
                        <h1>GALLERY <span>ADMIN</span></h1>
                        <p>Панель управления</p>
                    </RouterLink>
                </div>
                <div class="right-actions">
                    <span style="font-size: 0.8rem; color: #888;">{{ auth.user?.name }}</span>
                    <div class="user-avatar">{{ auth.initials }}</div>
                    <RouterLink to="/" class="btn-ghost">На сайт</RouterLink>
                    <button class="btn-ghost" type="button" @click="logout">Выйти</button>
                </div>
            </div>
            <div class="admin-menu">
                <RouterLink
                    v-for="item in menu"
                    :key="item.to"
                    :to="item.to"
                    :class="{ active: route.path.startsWith(item.to) }"
                >
                    <i :class="['fas', item.icon]"></i> {{ item.label }}
                </RouterLink>
            </div>
        </div>
    </div>
    <div class="admin-wrapper">
        <div class="container">
            <RouterView />
        </div>
    </div>
</template>

<style scoped>
.logo-area a { text-decoration: none; color: inherit; }
</style>
