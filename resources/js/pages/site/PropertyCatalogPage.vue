<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import PropertyCard from '../../components/PropertyCard.vue';
import OrderModal from '../../components/OrderModal.vue';
import { getCategoryConfig } from '../../config/propertyCategories';
import api from '../../api';

const route = useRoute();

const properties = ref([]);
const showModal = ref(false);
const selectedProperty = ref(null);

const config = computed(() => getCategoryConfig(route.meta.categorySlug));

const filters = reactive({
    type: '',
    min_area: '',
    max_area: '',
    min_price: '',
    max_price: '',
});

async function loadProperties() {
    if (!config.value) return;

    const params = { category: config.value.slug };
    if (filters.type) params.type = filters.type;
    if (filters.min_area) params.min_area = filters.min_area;
    if (filters.max_area) params.max_area = filters.max_area;
    if (filters.min_price) params.min_price = filters.min_price;
    if (filters.max_price) params.max_price = filters.max_price;

    const { data } = await api.get('/properties', { params });
    properties.value = data;
}

onMounted(loadProperties);

watch(() => route.meta.categorySlug, loadProperties);

function applyFilters() {
    loadProperties();
}

function openOrder(property) {
    selectedProperty.value = property;
    showModal.value = true;
}
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div class="page-header">
                <div class="breadcrumbs">
                    <RouterLink to="/">Главная</RouterLink>
                    <span>/</span>
                    <span style="color: #999;">{{ config?.label }}</span>
                </div>
                <h1>{{ config?.title }}</h1>
                <p>{{ config?.description }}</p>
            </div>

            <div class="filters">
                <label>Тип</label>
                <select v-model="filters.type">
                    <option value="">Все типы</option>
                    <option value="Студия">Студия</option>
                    <option value="1-комнатная">1-комнатная</option>
                    <option value="2-комнатная">2-комнатная</option>
                    <option value="3-комнатная">3-комнатная</option>
                    <option value="4-комнатная">4-комнатная</option>
                </select>

                <label>Площадь</label>
                <select v-model="filters.max_area">
                    <option value="">Любая</option>
                    <option value="50">до 50 м²</option>
                    <option value="100">до 100 м²</option>
                    <option value="150">до 150 м²</option>
                </select>

                <label>Цена</label>
                <select v-model="filters.max_price">
                    <option value="">Любая</option>
                    <option value="10000000">до 10 млн</option>
                    <option value="30000000">до 30 млн</option>
                    <option value="60000000">до 60 млн</option>
                </select>

                <button class="filter-btn" type="button" @click="applyFilters">Применить фильтры</button>
            </div>

            <div class="apartments-grid">
                <PropertyCard
                    v-for="property in properties"
                    :key="property.id"
                    :property="property"
                    @order="openOrder"
                />
            </div>

            <p v-if="!properties.length" style="text-align:center;color:#888;padding:40px 0;">
                Объекты не найдены. Попробуйте изменить фильтры.
            </p>
        </div>
    </main>
    <SiteFooter />
    <OrderModal v-if="showModal" :property="selectedProperty" @close="showModal = false" />
</template>
