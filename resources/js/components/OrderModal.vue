<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../api';

const props = defineProps({
    property: { type: Object, default: null },
});

const emit = defineEmits(['close']);

const router = useRouter();
const auth = useAuthStore();
const message = ref('');
const loading = ref(false);
const error = ref('');

onMounted(() => {
    document.body.style.overflow = 'hidden';
});

onUnmounted(() => {
    document.body.style.overflow = '';
});

async function submit() {
    error.value = '';

    if (!auth.isAuthenticated) {
        emit('close');
        router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } });
        return;
    }

    if (!props.property) {
        emit('close');
        return;
    }

    loading.value = true;
    try {
        await api.get('/sanctum/csrf-cookie');
        await api.post('/orders', {
            property_id: props.property.id,
            message: message.value || null,
        });
        emit('close');
        router.push({ name: 'orders' });
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось создать заказ';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="modal-overlay active" @click.self="emit('close')">
        <div class="modal-window">
            <button class="modal-close" type="button" @click="emit('close')">&times;</button>
            <div class="modal-title">{{ property ? 'Записаться на просмотр' : 'Связаться с нами' }}</div>
            <div class="modal-subtitle">
                <template v-if="property">
                    {{ property.title }} — оставьте заявку, и мы организуем индивидуальный показ
                </template>
                <template v-else>
                    Оставьте заявку, и мы свяжемся с вами в ближайшее время
                </template>
            </div>
            <form @submit.prevent="submit">
                <div v-if="property" class="form-group">
                    <label>Комментарий</label>
                    <textarea v-model="message" rows="4" placeholder="Удобное время, пожелания..."></textarea>
                </div>
                <div v-else class="form-group">
                    <label>Сообщение</label>
                    <textarea v-model="message" rows="4" placeholder="Опишите ваш запрос..."></textarea>
                </div>
                <p v-if="error" class="form-error">{{ error }}</p>
                <button class="form-btn" type="submit" :disabled="loading">
                    {{ loading ? 'Отправка...' : property ? 'Отправить заявку' : 'Отправить' }}
                </button>
                <div v-if="!auth.isAuthenticated" class="form-hint">
                    <i class="fas fa-lock"></i> Для оформления заявки потребуется вход в аккаунт
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.form-error {
    color: #e74c3c;
    margin-bottom: 12px;
    font-size: 0.85rem;
}
</style>
