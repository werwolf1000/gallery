<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api, { PROMO_BONUS } from '../api';

const emit = defineEmits(['close']);

const router = useRouter();
const auth = useAuthStore();
const message = ref('');
const loading = ref(false);
const error = ref('');
const success = ref(false);

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
        router.push({ name: 'login', query: { redirect: '/' } });
        return;
    }

    loading.value = true;
    try {
        await api.get('/sanctum/csrf-cookie');
        await api.post('/orders', {
            bonus: PROMO_BONUS,
            message: message.value || null,
        });
        success.value = true;
        setTimeout(() => {
            emit('close');
            router.push({ name: 'orders' });
        }, 1500);
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось активировать бонус';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="modal-overlay active" @click.self="emit('close')">
        <div class="modal-window">
            <button class="modal-close" type="button" @click="emit('close')">&times;</button>
            <div class="modal-title">Активация бонуса</div>
            <div class="modal-subtitle">
                {{ PROMO_BONUS }}
            </div>

            <div v-if="success" class="bonus-success">
                <i class="fas fa-gift"></i>
                Бонус активирован! Наш эксперт свяжется с вами в течение часа.
            </div>

            <form v-else @submit.prevent="submit">
                <div class="form-group">
                    <label>Комментарий (необязательно)</label>
                    <textarea v-model="message" rows="3" placeholder="Имена членов семьи или друзей, удобное время..."></textarea>
                </div>
                <p v-if="error" class="form-error">{{ error }}</p>
                <button class="form-btn" type="submit" :disabled="loading">
                    {{ loading ? 'Активация...' : 'Подтвердить активацию' }}
                </button>
                <div v-if="!auth.isAuthenticated" class="form-hint">
                    <i class="fas fa-lock"></i> Для активации бонуса потребуется вход в аккаунт
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
.bonus-success {
    text-align: center;
    padding: 24px;
    color: var(--gold-matte);
    font-weight: 500;
    line-height: 1.5;
}
.bonus-success i {
    display: block;
    font-size: 2.5rem;
    margin-bottom: 16px;
}
</style>
