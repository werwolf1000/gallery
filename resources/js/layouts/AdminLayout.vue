<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router';
import UserMenuDropdown from '../components/UserMenuDropdown.vue';
import '../../css/admin.css';

const route = useRoute();

const userMenuItems = [
    { to: '/', label: 'На сайт', icon: 'fa-globe' },
    { label: 'Выйти', icon: 'fa-sign-out-alt' },
];

const menu = [
    { to: '/admin/orders', icon: 'fa-shopping-bag', label: 'Заказы' },
    { to: '/admin/users', icon: 'fa-users', label: 'Пользователи' },
    { to: '/admin/surveys', icon: 'fa-poll', label: 'Опросы' },
    { to: '/admin/properties', icon: 'fa-building', label: 'Недвижимость' },
    { to: '/admin/slider', icon: 'fa-images', label: 'Слайдер' },
];
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
                    <UserMenuDropdown :items="userMenuItems" />
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
