<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const auth = useAuthStore();

const form = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    agreeTerms: false,
});
const error = ref('');
const loading = ref(false);
const showForm = ref(false);

onMounted(() => {
    setTimeout(() => {
        showForm.value = true;
    }, 100);
});

async function submit() {
    error.value = '';

    if (!form.value.agreeTerms) {
        error.value = 'Необходимо согласиться с условиями использования';
        return;
    }

    if (form.value.password !== form.value.password_confirmation) {
        error.value = 'Пароли не совпадают';
        return;
    }

    loading.value = true;
    try {
        await auth.register({
            name: form.value.name,
            email: form.value.email,
            phone: form.value.phone,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
        });
        router.push({ name: 'orders' });
    } catch (e) {
        const errors = e.response?.data?.errors;
        error.value = errors
            ? Object.values(errors).flat().join(' ')
            : e.response?.data?.message || 'Ошибка регистрации';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="auth-page">
        <SiteHeader />

        <div class="register-wrapper">
            <div class="container auth-form-center">
                <div class="register-container" :class="{ show: showForm }">
                    <div class="register-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2>Создать аккаунт</h2>
                        <p>Уже есть аккаунт? <RouterLink to="/login">Войдите</RouterLink></p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label>Полное имя <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input v-model="form.name" type="text" required placeholder="Александр Иванов">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input v-model="form.email" type="email" required placeholder="ivanov@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Телефон</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone"></i>
                                <input v-model="form.phone" type="tel" placeholder="+7 (999) 123-45-67">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Пароль <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input v-model="form.password" type="password" required placeholder="Минимум 8 символов" minlength="8">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Подтверждение пароля <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-check-circle"></i>
                                <input v-model="form.password_confirmation" type="password" required placeholder="Повторите пароль">
                            </div>
                        </div>

                        <div class="form-options">
                            <label>
                                <input v-model="form.agreeTerms" type="checkbox">
                                Я согласен с <a href="#">условиями использования</a>
                            </label>
                        </div>

                        <p v-if="error" class="form-error">{{ error }}</p>

                        <button class="register-btn" type="submit" :disabled="loading">
                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                            <i v-else class="fas fa-user-plus"></i>
                            {{ loading ? 'Регистрация...' : 'Зарегистрироваться' }}
                        </button>
                    </form>

                    <div class="register-divider">
                        <span>или через соцсети</span>
                    </div>

                    <div class="social-register">
                        <button class="social-btn google" type="button">
                            <i class="fab fa-google"></i> Google
                        </button>
                        <button class="social-btn facebook" type="button">
                            <i class="fab fa-facebook"></i> Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <SiteFooter />
    </div>
</template>

<style scoped>
.register-container {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.register-container.show {
    opacity: 1;
    transform: translateY(0);
}
.form-error {
    color: #e74c3c;
    font-size: 0.85rem;
    margin-bottom: 16px;
    text-align: center;
}
.register-header p a {
    color: var(--gold-matte);
    text-decoration: none;
    font-weight: 600;
}
.register-header p a:hover {
    text-decoration: underline;
}
</style>
