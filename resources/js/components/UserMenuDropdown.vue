<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const auth = useAuthStore();
const router = useRouter();
const open = ref(false);
const root = ref(null);

const visibleItems = computed(() =>
    props.items.filter((item) => !item.adminOnly || auth.isAdmin),
);

function toggle() {
    open.value = !open.value;
}

function close() {
    open.value = false;
}

async function handleLogout() {
    close();
    await auth.logout();
    router.push('/');
}

function onDocumentClick(event) {
    if (root.value && !root.value.contains(event.target)) {
        close();
    }
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        close();
    }
}

onMounted(() => {
    document.addEventListener('click', onDocumentClick);
    document.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', onDocumentClick);
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div ref="root" class="user-menu">
        <button
            class="user-avatar"
            type="button"
            :title="auth.user?.name"
            :aria-expanded="open"
            aria-haspopup="true"
            @click.stop="toggle"
        >
            {{ auth.initials }}
        </button>

        <Transition name="user-menu-fade">
            <div v-if="open" class="user-menu-dropdown" @click.stop>
                <div class="user-menu-header">
                    <div class="user-menu-name">{{ auth.user?.name }}</div>
                    <div class="user-menu-email">{{ auth.user?.email }}</div>
                </div>
                <div class="user-menu-items">
                    <template v-for="item in visibleItems" :key="item.label">
                        <RouterLink
                            v-if="item.to"
                            :to="item.to"
                            class="user-menu-item"
                            @click="close"
                        >
                            <i v-if="item.icon" :class="['fas', item.icon]"></i>
                            {{ item.label }}
                        </RouterLink>
                        <button
                            v-else
                            class="user-menu-item user-menu-item-danger"
                            type="button"
                            @click="handleLogout"
                        >
                            <i v-if="item.icon" :class="['fas', item.icon]"></i>
                            {{ item.label }}
                        </button>
                    </template>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.user-menu {
    position: relative;
}

.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 100%;
    background: var(--gold-matte);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--graphite);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.2s;
    border: 2px solid transparent;
    padding: 0;
    font-family: inherit;
}

.user-avatar:hover,
.user-avatar[aria-expanded="true"] {
    border-color: var(--graphite);
    transform: scale(1.05);
}

.user-menu-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 220px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    border: 1px solid rgba(0, 0, 0, 0.06);
    overflow: hidden;
    z-index: 200;
}

.user-menu-header {
    padding: 16px 18px;
    border-bottom: 1px solid #f0eee9;
    background: #fafaf8;
}

.user-menu-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--graphite);
}

.user-menu-email {
    font-size: 0.8rem;
    color: #888;
    margin-top: 4px;
    word-break: break-word;
}

.user-menu-items {
    padding: 8px;
}

.user-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 12px;
    border: none;
    border-radius: 12px;
    background: transparent;
    color: var(--graphite);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: 0.15s;
    font-family: inherit;
    text-align: left;
}

.user-menu-item i {
    width: 16px;
    color: var(--gold-matte);
}

.user-menu-item:hover {
    background: rgba(197, 162, 103, 0.12);
}

.user-menu-item-danger {
    color: #b42318;
}

.user-menu-item-danger i {
    color: #b42318;
}

.user-menu-item-danger:hover {
    background: rgba(180, 35, 24, 0.08);
}

.user-menu-fade-enter-active,
.user-menu-fade-leave-active {
    transition: opacity 0.15s ease, transform 0.15s ease;
}

.user-menu-fade-enter-from,
.user-menu-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>
