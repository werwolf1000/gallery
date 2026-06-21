<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import OrderModal from '../../components/OrderModal.vue';
import api, { formatPrice, propertyStatusLabels } from '../../api';

const route = useRoute();
const router = useRouter();
const property = ref(null);
const loading = ref(true);
const showModal = ref(false);

async function loadProperty() {
    loading.value = true;
    try {
        const { data } = await api.get(`/properties/${route.params.id}`);
        property.value = data;
    } catch {
        property.value = null;
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    await loadProperty();
    if (route.query.order === '1' && property.value) {
        showModal.value = true;
    }
});

function openOrder() {
    showModal.value = true;
}
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div v-if="loading" class="property-detail-loading">Загрузка...</div>

            <template v-else-if="property">
                <div class="page-header">
                    <div class="breadcrumbs">
                        <RouterLink to="/">Главная</RouterLink>
                        <span>/</span>
                        <RouterLink to="/apartments">Квартиры</RouterLink>
                        <span>/</span>
                        <span style="color: #999;">{{ property.title }}</span>
                    </div>
                </div>

                <div class="property-detail">
                    <div
                        class="property-detail-hero"
                        :style="{ backgroundImage: property.image_url ? `url('${property.image_url}')` : 'none' }"
                    >
                        <span v-if="property.badge" class="apartment-badge sale">{{ property.badge }}</span>
                    </div>

                    <div class="property-detail-body">
                        <div class="property-detail-main">
                            <h1>{{ property.title }}</h1>
                            <p class="property-detail-address">
                                <i class="fas fa-map-pin"></i> {{ property.address }}
                            </p>

                            <div class="apartment-details property-detail-specs">
                                <span v-if="property.type"><i class="fas fa-home"></i> {{ property.type }}</span>
                                <span v-if="property.area"><i class="fas fa-ruler-combined"></i> {{ property.area }} м²</span>
                                <span v-if="property.rooms"><i class="fas fa-bed"></i> {{ property.rooms }} комн.</span>
                                <span v-if="property.floor"><i class="fas fa-floor"></i> {{ property.floor }} этаж</span>
                            </div>

                            <div class="property-detail-price">
                                {{ formatPrice(property.price) }} <small>с НДС</small>
                            </div>

                            <div v-if="property.bonus" class="apartment-bonus">{{ property.bonus }}</div>

                            <p v-if="property.description" class="property-detail-description">
                                {{ property.description }}
                            </p>
                            <p v-else class="property-detail-description">
                                Эксклюзивный объект премиум-класса от агентства Gallery of Spaces.
                                Индивидуальный показ, полное юридическое сопровождение сделки.
                            </p>
                        </div>

                        <aside class="property-detail-sidebar">
                            <div class="property-detail-card">
                                <div class="detail-status">
                                    {{ propertyStatusLabels[property.status] || property.status }}
                                </div>
                                <button class="btn-gold-small detail-order-btn" type="button" @click="openOrder">
                                    Записаться на просмотр
                                </button>
                                <button class="btn-outline-gold detail-back-btn" type="button" @click="router.push('/apartments')">
                                    ← Все объекты
                                </button>
                            </div>
                        </aside>
                    </div>
                </div>
            </template>

            <div v-else class="property-detail-empty">
                <h2>Объект не найден</h2>
                <RouterLink to="/apartments" class="gold-btn">Вернуться к каталогу</RouterLink>
            </div>
        </div>
    </main>
    <SiteFooter />
    <OrderModal v-if="showModal && property" :property="property" @close="showModal = false" />
</template>

<style scoped>
.property-detail-loading,
.property-detail-empty {
    text-align: center;
    padding: 80px 20px;
    color: #888;
}
.property-detail-empty h2 {
    margin-bottom: 20px;
    color: var(--graphite);
}
.property-detail-hero {
    height: 480px;
    border-radius: 28px;
    background-size: cover;
    background-position: center;
    position: relative;
    margin-bottom: 32px;
}
.property-detail-hero .apartment-badge {
    position: absolute;
    top: 24px;
    left: 24px;
}
.property-detail-body {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 40px;
    margin-bottom: 60px;
}
.property-detail-main h1 {
    font-size: 2.2rem;
    font-weight: 600;
    margin-bottom: 12px;
}
.property-detail-address {
    color: #666;
    margin-bottom: 20px;
    font-size: 1rem;
}
.property-detail-address i {
    color: var(--gold-matte);
}
.property-detail-specs {
    margin-bottom: 24px;
}
.property-detail-price {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 16px;
}
.property-detail-price small {
    font-size: 0.9rem;
    font-weight: 400;
    color: #888;
}
.property-detail-description {
    margin-top: 24px;
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.property-detail-card {
    background: white;
    border-radius: 28px;
    padding: 28px;
    box-shadow: var(--shadow-hard);
    position: sticky;
    top: 120px;
}
.detail-status {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 40px;
    background: var(--gold-matte-light);
    color: var(--gold-matte);
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 20px;
}
.detail-order-btn {
    width: 100%;
    padding: 14px;
    font-size: 0.95rem;
    margin-bottom: 12px;
    cursor: pointer;
}
.detail-back-btn {
    width: 100%;
    padding: 12px;
    cursor: pointer;
}
@media (max-width: 900px) {
    .property-detail-body {
        grid-template-columns: 1fr;
    }
    .property-detail-hero {
        height: 280px;
    }
    .property-detail-card {
        position: static;
    }
}
</style>
