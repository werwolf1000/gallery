<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import OrderModal from '../../components/OrderModal.vue';
import BonusActivationModal from '../../components/BonusActivationModal.vue';
import api, { formatPrice } from '../../api';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const router = useRouter();
const modules = [Autoplay, Navigation, Pagination];
const properties = ref([]);
const slides = ref([]);
const showModal = ref(false);
const showBonusModal = ref(false);
const selectedProperty = ref(null);

const bentoItems = [
    { icon: 'fa-building', title: 'Квартиры', to: '/apartments' },
    { icon: 'fa-city', title: 'Апартаменты', to: '/apartments' },
    { icon: 'fa-door-open', title: 'Комнаты', to: '/apartments' },
    { icon: 'fa-home', title: 'Дома с участком', to: '/apartments' },
    { icon: 'fa-parking', title: 'Парковочные места', to: '/apartments' },
    { icon: 'fa-boxes', title: 'Кладовые', to: '/apartments' },
    { icon: 'fa-tree', title: 'Земля', to: '/apartments' },
    { icon: 'fa-chart-line', title: 'Инвестиции', to: '/apartments' },
];

const duties = [
    { icon: 'fa-chart-line', title: 'Оценка стоимости', text: 'Профессионально оцениваем рыночную стоимость объекта с учётом локации и трендов.' },
    { icon: 'fa-file-signature', title: 'Заключение договора', text: 'Составляем юридически безупречный договор с полной защитой ваших интересов.' },
    { icon: 'fa-gavel', title: 'Юридические документы', text: 'Подготавливаем полный пакет документов, проверяем чистоту сделки.' },
    { icon: 'fa-broom', title: 'Приводим объект в порядок', text: 'Предпродажная подготовка, клининг, мелкий ремонт — всё для идеального первого впечатления.' },
    { icon: 'fa-camera-retro', title: 'Рекламные фото', text: 'Профессиональная съёмка, дрон-панорамы, 3D-туры премиум-качества.' },
    { icon: 'fa-bullhorn', title: 'Рекламная кампания', text: 'Выставляем объект во все каналы: топовые площадки, соцсети, таргет, партнёры.' },
    { icon: 'fa-eye', title: 'Показы', text: 'Организуем индивидуальные и групповые показы, сопровождаем на всех этапах.' },
];

const testimonials = [
    {
        text: 'Атмосфера настоящего искусства и абсолютная прозрачность сделки. Бонусная система приятно удивила – мы получили консьерж-сервис на год.',
        initials: 'АК',
        name: 'Анастасия Кравцова',
        meta: 'Покупка апартаментов, декабрь 2025',
    },
    {
        text: 'Сотрудничаем с «Галереей Пространств» второй раз. Помогли с ИЖС и строительством. Отдельное спасибо за персонального бонус-менеджера.',
        initials: 'МГ',
        name: 'Михаил Громов',
        meta: 'Строительство дома, 2026',
    },
    {
        text: 'Редкое сочетание вкуса, профессионализма и человеческого отношения. Бесплатная консультация для друзей стала приятным бонусом, спасибо!',
        initials: 'ЕЛ',
        name: 'Елизавета Демидова',
        meta: 'Инвестиционный портфель',
    },
];

onMounted(async () => {
    const [propertiesRes, slidesRes] = await Promise.all([
        api.get('/properties'),
        api.get('/slider'),
    ]);
    properties.value = propertiesRes.data.slice(0, 6);
    slides.value = slidesRes.data;
});

function openModal(property = null) {
    if (property) {
        router.push({ name: 'property-detail', params: { id: property.id } });
        return;
    }
    selectedProperty.value = null;
    showModal.value = true;
}

function goToProperty(property) {
    router.push({ name: 'property-detail', params: { id: property.id } });
}

function activateBonus() {
    showBonusModal.value = true;
}
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div class="slogan-block">
                <div class="slogan">Пространство · <span>Стиль</span> · Ценность</div>
                <div class="slogan-sub">Агентство недвижимости</div>
            </div>

            <div class="hero">
                <div class="hero-left">
                    <div class="hero-slogan">Пространство · Стиль · Ценность</div>
                    <h2>Пространство,<br>которое вы заслужили</h2>
                    <p>Агентство недвижимости</p>
                    <button class="gold-btn" type="button" @click="openModal()">Выбрать объект</button>
                    <div class="bonus-label-sm">✦ Личный бонус-менеджер</div>
                </div>
                <div class="hero-right">
                    <div class="mock-3d"></div>
                </div>
            </div>

            <div class="bento-section">
                <div class="bento-grid">
                    <div v-for="item in bentoItems" :key="item.title" class="bento-card">
                        <i :class="['fas', item.icon]"></i>
                        <h4><RouterLink :to="item.to">{{ item.title }}</RouterLink></h4>
                        <div class="bonus-badge">Бонусная система</div>
                    </div>
                </div>
            </div>

            <div class="property-section" id="properties">
                <div class="section-title">Эксклюзивные объекты</div>
                <div class="property-grid">
                    <article
                        v-for="property in properties"
                        :key="property.id"
                        class="property-card"
                        @click="goToProperty(property)"
                    >
                        <div
                            class="property-img"
                            :style="{ backgroundImage: `url('${property.image_url}')` }"
                        >
                            <div v-if="property.badge" class="property-ribbon">{{ property.badge }}</div>
                        </div>
                        <div class="property-info">
                            <div class="property-title">{{ property.title }}</div>
                            <div class="property-details">
                                <span v-if="property.area">{{ property.area }} м²</span>
                                <span v-if="property.rooms">{{ property.rooms }} комнаты</span>
                            </div>
                            <div class="property-price">{{ formatPrice(property.price) }}</div>
                            <div v-if="property.bonus" class="property-line">{{ property.bonus }}</div>
                        </div>
                    </article>
                </div>
            </div>

            <div v-if="slides.length" class="slider-wrapper">
                <Swiper
                    class="swiper swiper-full"
                    :modules="modules"
                    :loop="slides.length > 1"
                    :autoplay="{ delay: 5000 }"
                    :navigation="true"
                    :pagination="{ clickable: true }"
                >
                    <SwiperSlide v-for="slide in slides" :key="slide.id">
                        <img :src="slide.image_url" :alt="slide.title">
                        <div class="slide-caption">{{ slide.title }}</div>
                    </SwiperSlide>
                </Swiper>
            </div>

            <div class="duties-section" id="duties">
                <div class="section-title">Наши обязательства перед вами</div>
                <div class="duties-grid">
                    <div v-for="duty in duties" :key="duty.title" class="duty-card">
                        <div class="duty-icon"><i :class="['fas', duty.icon]"></i></div>
                        <h3>{{ duty.title }}</h3>
                        <p>{{ duty.text }}</p>
                    </div>
                </div>
            </div>

            <div class="testimonials-section" id="testimonials">
                <div class="section-title">Отзывы клиентов</div>
                <div class="testimonials-grid">
                    <div v-for="item in testimonials" :key="item.name" class="testimonial-card">
                        <div class="testimonial-text">{{ item.text }}</div>
                        <div class="testimonial-author">
                            <div class="author-avatar">{{ item.initials }}</div>
                            <div class="author-info">
                                <h4>{{ item.name }}</h4>
                                <p>{{ item.meta }}</p>
                                <div class="rating">★★★★★</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="promo-glass">
                <div class="promo-badge-icon">3+</div>
                <div class="promo-text">
                    <h3>Бесплатная консультация по недвижимости для <span>трёх членов семьи или друзей</span> при заключении договора</h3>
                </div>
                <button class="promo-btn" type="button" @click="activateBonus">Активировать бонус</button>
            </div>
        </div>
    </main>
    <SiteFooter />
    <OrderModal v-if="showModal" :property="selectedProperty" @close="showModal = false" />
    <BonusActivationModal v-if="showBonusModal" @close="showBonusModal = false" />
</template>

<style scoped>
.property-card {
    cursor: pointer;
}
.bento-card h4 a {
    color: inherit;
    text-decoration: none;
}
</style>
