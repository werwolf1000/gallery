<script setup>
import { onMounted, ref } from 'vue';
import api, { formatDate, formatPrice, orderStatusLabels } from '../../api';

const orders = ref([]);
const stats = ref({ pending: 0, active: 0, completed: 0, cancelled: 0 });
const loading = ref(true);
const saving = ref(false);
const editingOrder = ref(null);
const error = ref('');
const editForm = ref({ status: 'pending', message: '' });

async function load() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/orders');
        orders.value = data.orders;
        stats.value = data.stats;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function openEdit(order) {
    editingOrder.value = order;
    error.value = '';
    editForm.value = {
        status: order.status,
        message: order.message || '',
    };
}

function closeEdit() {
    editingOrder.value = null;
    error.value = '';
}

async function saveEdit() {
    if (!editingOrder.value) return;
    error.value = '';
    saving.value = true;
    try {
        await api.patch(`/admin/orders/${editingOrder.value.id}`, editForm.value);
        closeEdit();
        await load();
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось сохранить заказ';
    } finally {
        saving.value = false;
    }
}

async function removeOrder(order) {
    if (!confirm(`Удалить заказ #${order.number}?`)) return;
    try {
        await api.delete(`/admin/orders/${order.id}`);
        if (editingOrder.value?.id === order.id) closeEdit();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || 'Не удалось удалить заказ';
    }
}
</script>

<template>
    <div class="admin-page active">
        <div class="page-header">
            <h2>Обработка заказов <small>управление и статусы</small></h2>
            <div class="page-actions">
                <button class="btn-gold" type="button" @click="load"><i class="fas fa-sync"></i> Обновить</button>
            </div>
        </div>

        <div class="orders-stats">
            <div class="stat-card pending"><i class="stat-icon fas fa-clock"></i><div class="stat-number">{{ stats.pending }}</div><div class="stat-label">В обработке</div></div>
            <div class="stat-card active"><i class="stat-icon fas fa-spinner"></i><div class="stat-number">{{ stats.active }}</div><div class="stat-label">Активные</div></div>
            <div class="stat-card completed"><i class="stat-icon fas fa-check-circle"></i><div class="stat-number">{{ stats.completed }}</div><div class="stat-label">Завершённые</div></div>
            <div class="stat-card cancelled"><i class="stat-icon fas fa-times-circle"></i><div class="stat-number">{{ stats.cancelled }}</div><div class="stat-label">Отменённые</div></div>
        </div>

        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Заказ</th>
                        <th>Клиент</th>
                        <th>Объект</th>
                        <th>Сумма</th>
                        <th>Бонус</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading"><td colspan="7">Загрузка...</td></tr>
                    <tr v-for="order in orders" v-else :key="order.id">
                        <td><strong>#{{ order.number }}</strong><br><small style="color:#888;">{{ formatDate(order.created_at) }}</small></td>
                        <td>{{ order.user?.name }}</td>
                        <td>{{ order.property?.title || '—' }}</td>
                        <td>{{ order.amount ? formatPrice(order.amount) : 'Бонус' }}</td>
                        <td><span v-if="order.bonus" class="status-badge active">{{ order.bonus }}</span><span v-else>—</span></td>
                        <td><span :class="['status-badge', order.status]">{{ orderStatusLabels[order.status] }}</span></td>
                        <td>
                            <div class="order-actions-small">
                                <button class="btn-edit" type="button" @click="openEdit(order)">Изменить</button>
                                <button class="btn-delete" type="button" @click="removeOrder(order)">Удалить</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="editingOrder" class="admin-modal-overlay" @click.self="closeEdit">
            <div class="admin-modal">
                <div class="admin-modal-header">
                    <div>
                        <h3>Редактирование заказа #{{ editingOrder.number }}</h3>
                        <p style="color:#888;font-size:0.85rem;margin-top:4px;">{{ editingOrder.property?.title }}</p>
                    </div>
                    <button class="admin-modal-close" type="button" @click="closeEdit">&times;</button>
                </div>

                <div class="property-form" style="box-shadow:none;border:none;padding:0;">
                    <div class="form-group">
                        <label>Клиент</label>
                        <input type="text" :value="editingOrder.user?.name" disabled>
                    </div>
                    <div class="form-group">
                        <label>Сумма</label>
                        <input type="text" :value="formatPrice(editingOrder.amount)" disabled>
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <select v-model="editForm.status">
                            <option v-for="(label, key) in orderStatusLabels" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Комментарий</label>
                        <textarea v-model="editForm.message" rows="4" placeholder="Комментарий к заказу..."></textarea>
                    </div>
                    <div v-if="editingOrder.bonus" class="form-group">
                        <label>Бонус</label>
                        <input type="text" :value="editingOrder.bonus" disabled>
                    </div>
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
