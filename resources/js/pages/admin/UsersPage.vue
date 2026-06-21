<script setup>
import { onMounted, ref } from 'vue';
import api, { formatDate } from '../../api';

const users = ref([]);
const loading = ref(true);
const saving = ref(false);
const showCreateForm = ref(false);
const editingUser = ref(null);
const error = ref('');

const createForm = ref({ name: '', email: '', phone: '', password: '', role: 'user' });
const editForm = ref({ name: '', email: '', phone: '', role: 'user', is_blocked: false, password: '' });

async function load() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/users');
        users.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function initials(name) {
    return name.split(' ').map((p) => p[0]).join('').slice(0, 2).toUpperCase();
}

function openEdit(user) {
    editingUser.value = user;
    error.value = '';
    editForm.value = {
        name: user.name,
        email: user.email,
        phone: user.phone || '',
        role: user.role,
        is_blocked: user.is_blocked,
        password: '',
    };
}

function closeEdit() {
    editingUser.value = null;
    error.value = '';
}

async function saveEdit() {
    error.value = '';
    saving.value = true;
    try {
        const payload = { ...editForm.value };
        if (!payload.password) delete payload.password;
        await api.patch(`/admin/users/${editingUser.value.id}`, payload);
        closeEdit();
        await load();
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось сохранить пользователя';
    } finally {
        saving.value = false;
    }
}

async function toggleBlock(user) {
    await api.patch(`/admin/users/${user.id}`, { is_blocked: !user.is_blocked });
    await load();
}

async function createUser() {
    error.value = '';
    saving.value = true;
    try {
        await api.post('/admin/users', createForm.value);
        showCreateForm.value = false;
        createForm.value = { name: '', email: '', phone: '', password: '', role: 'user' };
        await load();
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Ошибка создания';
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="admin-page active">
        <div class="page-header">
            <h2>Пользователи <small>управление аккаунтами</small></h2>
            <div class="page-actions">
                <button class="btn-gold" type="button" @click="showCreateForm = !showCreateForm"><i class="fas fa-user-plus"></i> Добавить</button>
            </div>
        </div>

        <div v-if="showCreateForm" class="user-editor-panel">
            <h3 style="margin-bottom: 20px; font-weight: 600;">Новый пользователь</h3>
            <div class="property-form" style="box-shadow:none;border:none;padding:0;">
                <div class="form-row">
                    <div class="form-group"><label>Имя</label><input v-model="createForm.name" type="text"></div>
                    <div class="form-group"><label>Email</label><input v-model="createForm.email" type="email"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Телефон</label><input v-model="createForm.phone" type="text"></div>
                    <div class="form-group"><label>Пароль</label><input v-model="createForm.password" type="password"></div>
                </div>
                <div class="form-group">
                    <label>Роль</label>
                    <select v-model="createForm.role"><option value="user">Пользователь</option><option value="admin">Администратор</option></select>
                </div>
            </div>
            <p v-if="error && showCreateForm" class="form-error-text">{{ error }}</p>
            <div class="admin-modal-actions" style="margin-top:0;">
                <button class="btn-outline" type="button" @click="showCreateForm = false">Отмена</button>
                <button class="btn-success" type="button" :disabled="saving" @click="createUser">Сохранить</button>
            </div>
        </div>

        <div class="users-grid">
            <div v-if="loading" class="user-card">Загрузка...</div>
            <div v-for="user in users" v-else :key="user.id" class="user-card">
                <div class="user-avatar-big">{{ initials(user.name) }}</div>
                <div class="user-name">{{ user.name }}</div>
                <div class="user-email">{{ user.email }}</div>
                <div v-if="user.is_blocked" style="color:var(--danger);font-size:0.75rem;margin-bottom:8px;">Заблокирован</div>
                <div class="user-info">
                    <span><i class="fas fa-calendar"></i> {{ formatDate(user.created_at) }}</span>
                    <span><i class="fas fa-shopping-bag"></i> {{ user.orders_count }} заказов</span>
                </div>
                <div class="user-actions">
                    <button class="btn-edit-user" type="button" @click="openEdit(user)">Редактировать</button>
                    <button class="btn-block" type="button" @click="toggleBlock(user)">
                        {{ user.is_blocked ? 'Разблокировать' : 'Заблокировать' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="editingUser" class="admin-modal-overlay" @click.self="closeEdit">
            <div class="admin-modal">
                <div class="admin-modal-header">
                    <h3>Редактирование пользователя</h3>
                    <button class="admin-modal-close" type="button" @click="closeEdit">&times;</button>
                </div>
                <div class="property-form" style="box-shadow:none;border:none;padding:0;">
                    <div class="form-group">
                        <label>Имя</label>
                        <input v-model="editForm.name" type="text">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="editForm.email" type="email">
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input v-model="editForm.phone" type="text">
                    </div>
                    <div class="form-group">
                        <label>Роль</label>
                        <select v-model="editForm.role">
                            <option value="user">Пользователь</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль (необязательно)</label>
                        <input v-model="editForm.password" type="password" placeholder="Оставьте пустым, чтобы не менять">
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;">
                        <input v-model="editForm.is_blocked" type="checkbox"> Заблокирован
                    </label>
                </div>
                <p v-if="error" class="form-error-text">{{ error }}</p>
                <div class="admin-modal-actions">
                    <button class="btn-outline" type="button" @click="closeEdit">Отмена</button>
                    <button class="btn-success" type="button" :disabled="saving" @click="saveEdit">
                        {{ saving ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
