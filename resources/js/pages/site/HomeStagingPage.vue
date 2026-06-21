<script setup>
import { ref } from 'vue';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import OrderModal from '../../components/OrderModal.vue';
import {
    galleryWorks,
    interiorStyles,
    processSteps,
    stats,
    testimonials,
} from '../../data/homeStagingContent';

const showModal = ref(false);

function openOrder() {
    showModal.value = true;
}

function showPortfolio(label) {
    window.alert(`📸 Портфолио: ${label}`);
}
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div class="page-hero">
                <div class="breadcrumbs">
                    <RouterLink to="/">Главная</RouterLink> / Хоумстейджинг
                </div>
                <h1>Хоумстейджинг</h1>
                <p>Профессиональная подготовка объектов к продаже. Создаём интерьеры, которые вдохновляют покупателей на покупку.</p>
            </div>

            <div class="stats-row">
                <div v-for="stat in stats" :key="stat.label" class="stat-item">
                    <div class="stat-icon"><i :class="stat.icon"></i></div>
                    <div class="stat-number">{{ stat.number }}</div>
                    <div class="stat-label">{{ stat.label }}</div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="services-section">
                <div class="section-title">Стили интерьера</div>
                <div class="section-subtitle">Выберите направление, которое подходит вашему объекту</div>

                <div class="services-grid">
                    <div v-for="style in interiorStyles" :key="style.id" class="service-card">
                        <div
                            class="service-img"
                            :style="{ backgroundImage: `url('${style.image}')` }"
                        >
                            <span class="service-tag" :class="style.tagClass">{{ style.tag }}</span>
                        </div>
                        <div class="service-body">
                            <div class="service-title">
                                {{ style.title }} <span>{{ style.titleAccent }}</span>
                            </div>
                            <div class="service-desc">{{ style.desc }}</div>
                            <div class="service-features">
                                <div v-for="feature in style.features" :key="feature" class="feature">
                                    <i class="fas fa-check-circle"></i> {{ feature }}
                                </div>
                            </div>
                            <div class="service-price">{{ style.price }} <small>/ комната</small></div>
                            <div class="service-actions">
                                <button
                                    class="btn-gold-small"
                                    type="button"
                                    @click="openOrder()"
                                >
                                    Заказать
                                </button>
                                <button
                                    class="btn-outline-small"
                                    type="button"
                                    @click="showPortfolio(style.orderLabel)"
                                >
                                    Портфолио
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="process-section">
                <div class="section-title">Как мы работаем</div>
                <div class="section-subtitle">Четыре простых шага к идеальному интерьеру</div>

                <div class="process-grid">
                    <div v-for="step in processSteps" :key="step.number" class="process-step">
                        <div class="step-number">{{ step.number }}</div>
                        <div class="step-title">{{ step.title }}</div>
                        <div class="step-desc">{{ step.desc }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="gallery-section">
                <div class="section-title">Наши работы</div>
                <div class="section-subtitle">Реальные проекты, которые мы реализовали</div>

                <div class="gallery-grid">
                    <div
                        v-for="(item, index) in galleryWorks"
                        :key="index"
                        class="gallery-item"
                        :style="{ backgroundImage: `url('${item.image}')` }"
                        @click="showPortfolio(item.label)"
                    >
                        <div class="gallery-overlay">{{ item.label }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="testimonials-section">
                <div class="section-title">Отзывы клиентов</div>
                <div class="section-subtitle">Что говорят о нашей работе</div>

                <div class="testimonials-grid">
                    <div v-for="item in testimonials" :key="item.name" class="testimonial-card">
                        <div class="testimonial-text">{{ item.text }}</div>
                        <div class="testimonial-author">
                            <div class="avatar">{{ item.initials }}</div>
                            <div class="author-info">
                                <h4>{{ item.name }}</h4>
                                <p>{{ item.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="cta-section">
                <h2>Создайте <span>идеальный интерьер</span> для продажи</h2>
                <p>Оставьте заявку на бесплатную консультацию и получите персональное предложение по хоумстейджингу.</p>
                <div class="cta-buttons">
                    <button class="btn-gold-big" type="button" @click="openOrder()">
                        Получить консультацию
                    </button>
                    <button class="btn-outline-big" type="button" @click="showPortfolio('Каталог стилей')">
                        Скачать каталог стилей
                    </button>
                </div>
            </div>
        </div>
    </main>
    <SiteFooter />
    <OrderModal v-if="showModal" @close="showModal = false" />
</template>
