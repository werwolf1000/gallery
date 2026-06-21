<script setup>
import { onMounted, ref } from 'vue';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import api, { formatDate, formatPrice, orderStatusLabels } from '../../api';

const orders = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await api.get('/orders');
        orders.value = data;
    } finally {
        loading.value = false;
    }
});

async function repeatOrder(order) {
    await api.post('/orders', {
        property_id: order.property_id,
        bonus: order.bonus,
        message: 'Повторный заказ',
    });
    const { data } = await api.get('/orders');
    orders.value = data;
}
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div class="cabinet-header">
                <h1>Мои заказы</h1>
                <p>История заявок и активированных бонусов</p>
            </div>

            <div v-if="loading" class="empty-orders">
                <p>Загрузка...</p>
            </div>

            <div v-else-if="!orders.length" class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h3>Заказов пока нет</h3>
                <p>Выберите объект на странице квартир или активируйте бонус на главной</p>
                <RouterLink to="/apartments" class="gold-btn">Смотреть объекты</RouterLink>
            </div>

            <div v-else class="orders-list">
                <article v-for="order in orders" :key="order.id" class="order-card">
                    <div class="order-card-header">
                        <div>
                            <strong>#{{ order.number }}</strong>
                            <small>{{ formatDate(order.created_at) }}</small>
                        </div>
                        <span :class="['order-status', order.status]">
                            {{ orderStatusLabels[order.status] }}
                        </span>
                    </div>
                    <div class="order-card-body">
                        <div v-if="order.property" class="item">
                            <i class="fas fa-building"></i> <strong>{{ order.property.title }}</strong>
                        </div>
                        <div v-if="order.property" class="item">
                            <i class="fas fa-map-pin"></i> {{ order.property.address }}
                        </div>
                        <div v-if="order.bonus" class="item order-bonus">
                            <i class="fas fa-gift"></i> <strong>{{ order.bonus }}</strong>
                        </div>
                        <div v-if="order.message" class="item"><i class="fas fa-comment"></i> {{ order.message }}</div>
                    </div>
                    <div class="order-card-footer">
                        <div class="price">
                            <template v-if="order.amount">{{ formatPrice(order.amount) }} <small>с НДС</small></template>
                            <template v-else><span class="bonus-only-label">Активация бонуса</span></template>
                        </div>
                        <div class="order-actions">
                            <button class="btn-repeat" type="button" @click="repeatOrder(order)">Повторить</button>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </main>
    <SiteFooter />
</template>

<style scoped>
.order-bonus {
    color: var(--gold-matte);
}
.bonus-only-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--gold-matte);
}
</style>
