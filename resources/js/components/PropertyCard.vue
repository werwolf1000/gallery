<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { formatPrice } from '../api';

const props = defineProps({
    property: { type: Object, required: true },
});

const emit = defineEmits(['order']);

const router = useRouter();
const fav = ref(false);

function goToDetail() {
    router.push({ name: 'property-detail', params: { id: props.property.id } });
}
</script>

<template>
    <article class="apartment-card" @click="goToDetail">
        <div
            class="apartment-img"
            :style="{ backgroundImage: property.image_url ? `url('${property.image_url}')` : 'none' }"
        >
            <span v-if="property.badge" class="apartment-badge sale">{{ property.badge }}</span>
            <button class="apartment-fav" type="button" @click.stop="fav = !fav">
                <i :class="fav ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>
        </div>
        <div class="apartment-info">
            <div class="apartment-title">{{ property.title }}</div>
            <div class="apartment-address">
                <i class="fas fa-map-pin"></i> {{ property.address }}
            </div>
            <div class="apartment-details">
                <span v-if="property.area"><i class="fas fa-ruler-combined"></i> {{ property.area }} м²</span>
                <span v-if="property.rooms"><i class="fas fa-bed"></i> {{ property.rooms }} комн.</span>
                <span v-if="property.floor"><i class="fas fa-floor"></i> {{ property.floor }} этаж</span>
            </div>
            <div class="apartment-price">{{ formatPrice(property.price) }} <small>с НДС</small></div>
            <div v-if="property.bonus" class="apartment-bonus">{{ property.bonus }}</div>
            <div class="apartment-actions">
                <RouterLink
                    :to="{ name: 'property-detail', params: { id: property.id } }"
                    class="btn-outline-gold"
                    @click.stop
                >
                    Подробнее
                </RouterLink>
                <button class="btn-gold-small" type="button" @click.stop="emit('order', property)">Записаться</button>
            </div>
        </div>
    </article>
</template>

<style scoped>
.apartment-card {
    cursor: pointer;
}
.apartment-actions .btn-outline-gold {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
