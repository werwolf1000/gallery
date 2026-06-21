<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import { useAuthStore } from '../../stores/auth';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const form = ref({
    email: '',
    password: '',
    remember: false,
});
const error = ref('');
const loading = ref(false);
const showForm = ref(false);

onMounted(() => {
    setTimeout(() => {
        showForm.value = true;
    }, 100);
});

function fillDemo() {
    form.value.email = 'alex@example.com';
    form.value.password = 'password';
}

async function submit() {
    error.value = '';
    loading.value = true;
    try {
        await auth.login(form.value);
        const redirect = route.query.redirect || (auth.isAdmin ? '/admin' : '/orders');
        router.push(redirect);
    } catch (e) {
        error.value = e.response?.data?.message || e.response?.data?.errors?.email?.[0] || 'Ошибка входа';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="auth-page">
        <SiteHeader />

        <div class="login-wrapper">
            <div class="container auth-form-center">
                <div class="login-container" :class="{ show: showForm }">
                    <div class="login-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h2>Добро пожаловать</h2>
                        <p>Войдите в свой аккаунт или <RouterLink to="/register">зарегистрируйтесь</RouterLink></p>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input v-model="form.email" type="email" required placeholder="ivanov@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Пароль <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input v-model="form.password" type="password" required placeholder="Введите пароль">
                            </div>
                        </div>

                        <div class="form-options">
                            <label>
                                <input v-model="form.remember" type="checkbox">
                                Запомнить меня
                            </label>
                            <a href="#">Забыли пароль?</a>
                        </div>

                        <p v-if="error" class="form-error">{{ error }}</p>

                        <button class="login-btn" type="submit" :disabled="loading">
                            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                            <i v-else class="fas fa-sign-in-alt"></i>
                            {{ loading ? 'Вход...' : 'Войти' }}
                        </button>
                    </form>

                    <div class="login-divider">
                        <span>или через соцсети</span>
                    </div>

                    <div class="social-login">
                        <button class="social-btn google" type="button">
                            <i class="fab fa-google"></i> Google
                        </button>
                        <button class="social-btn facebook" type="button">
                            <i class="fab fa-facebook"></i> Facebook
                        </button>
                    </div>

                    <div class="demo-hint" role="button" tabindex="0" @click="fillDemo" @keyup.enter="fillDemo">
                        <p>🔑 Демо-данные для входа:</p>
                        <div class="demo-credentials">
                            <span><strong>Email:</strong> alex@example.com</span>
                            <span><strong>Пароль:</strong> password</span>
                        </div>
                        <p style="margin-top: 8px; font-size: 0.7rem; color: #aaa;">Нажмите, чтобы заполнить автоматически</p>
                    </div>
                </div>
            </div>
        </div>

        <SiteFooter />
    </div>
</template>

<style scoped>
.login-container {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.login-container.show {
    opacity: 1;
    transform: translateY(0);
}
.form-error {
    color: #e74c3c;
    font-size: 0.85rem;
    margin-bottom: 16px;
    text-align: center;
}
.login-header p a {
    color: var(--gold-matte);
    text-decoration: none;
    font-weight: 600;
}
.login-header p a:hover {
    text-decoration: underline;
}
.demo-hint {
    cursor: pointer;
}
</style>
