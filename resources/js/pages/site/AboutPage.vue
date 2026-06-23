<script setup>
import { ref } from 'vue';
import SiteHeader from '../../components/SiteHeader.vue';
import SiteFooter from '../../components/SiteFooter.vue';
import OrderModal from '../../components/OrderModal.vue';

const showModal = ref(false);

const values = [
    {
        icon: 'fa-gem',
        title: 'Премиальный подход',
        text: 'Работаем только с объектами, которые соответствуют высоким стандартам архитектуры, локации и качества.',
    },
    {
        icon: 'fa-handshake',
        title: 'Прозрачность сделок',
        text: 'Юридическая чистота, понятные условия и сопровождение на каждом этапе — от первой консультации до ключей.',
    },
    {
        icon: 'fa-user-tie',
        title: 'Персональный менеджер',
        text: 'За каждым клиентом закреплён бонус-менеджер, который координирует все услуги агентства.',
    },
    {
        icon: 'fa-drafting-compass',
        title: 'Архитектура и дизайн',
        text: 'Объединяем агентство недвижимости и архитектурное бюро: от ИЖС и ремонта до хоумстейджинга.',
    },
];

const stats = [
    { number: '12+', label: 'Лет на рынке премиум-недвижимости' },
    { number: '500+', label: 'Успешно реализованных проектов' },
    { number: '2', label: 'Города — Москва и Санкт-Петербург' },
    { number: '24/7', label: 'Консьерж-сервис для клиентов' },
];

const services = [
    'Покупка и продажа премиальных объектов',
    'Аренда квартир, домов и апартаментов',
    'Строительство ИЖС под ключ',
    'Ремонт и отделка премиум-класса',
    'Хоумстейджинг и подготовка к продаже',
    'Инвестиционный консалтинг',
];
</script>

<template>
    <SiteHeader />
    <main>
        <div class="container">
            <div class="page-header">
                <div class="breadcrumbs">
                    <RouterLink to="/">Главная</RouterLink>
                    <span>/</span>
                    <span style="color: #999;">О нас</span>
                </div>
                <h1>О нас</h1>
                <p>«Галерея Пространств» — агентство премиальной недвижимости и архитектурное бюро в одном лице</p>
            </div>

            <div class="about-hero">
                <div class="about-hero-content">
                    <div class="about-slogan">Пространство · <span>Стиль</span> · Ценность</div>
                    <h2>Мы создаём пространства,<br>которые вдохновляют</h2>
                    <p>
                        С 2014 года помогаем клиентам находить, покупать, строить и оформлять недвижимость
                        премиум-класса. Наш подход сочетает экспертизу рынка, архитектурное видение
                        и персональное сопровождение на каждом этапе.
                    </p>
                    <button class="gold-btn" type="button" @click="showModal = true">Связаться с нами</button>
                </div>
                <div
                    class="about-hero-image"
                    style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&q=80')"
                ></div>
            </div>

            <div class="about-stats">
                <div v-for="stat in stats" :key="stat.label" class="about-stat">
                    <div class="about-stat-number">{{ stat.number }}</div>
                    <div class="about-stat-label">{{ stat.label }}</div>
                </div>
            </div>

            <div class="about-section">
                <div class="section-title">Наши ценности</div>
                <div class="about-values">
                    <div v-for="value in values" :key="value.title" class="about-value-card">
                        <div class="about-value-icon"><i :class="['fas', value.icon]"></i></div>
                        <h3>{{ value.title }}</h3>
                        <p>{{ value.text }}</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <div class="section-title">Чем мы занимаемся</div>
                <ul class="about-services">
                    <li v-for="service in services" :key="service">
                        <i class="fas fa-check-circle"></i>
                        {{ service }}
                    </li>
                </ul>
            </div>

            <div class="about-cta">
                <h2>Готовы обсудить ваш проект?</h2>
                <p>Оставьте заявку — персональный менеджер свяжется с вами в течение 15 минут.</p>
                <button class="gold-btn" type="button" @click="showModal = true">Получить консультацию</button>
            </div>
        </div>
    </main>
    <SiteFooter />
    <OrderModal v-if="showModal" @close="showModal = false" />
</template>

<style scoped>
.about-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: center;
    margin: 40px 0 60px;
}
.about-slogan {
    font-size: 0.85rem;
    letter-spacing: 2px;
    color: var(--gold-matte);
    margin-bottom: 16px;
    text-transform: uppercase;
}
.about-slogan span {
    font-weight: 600;
}
.about-hero-content h2 {
    font-size: 2.4rem;
    font-weight: 700;
    line-height: 1.15;
    margin-bottom: 20px;
}
.about-hero-content p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 28px;
}
.about-hero-image {
    height: 420px;
    border-radius: 28px;
    background-size: cover;
    background-position: center;
    box-shadow: var(--shadow-hard);
}
.about-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 60px;
}
.about-stat {
    background: white;
    border-radius: 24px;
    padding: 28px 24px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.about-stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gold-matte);
}
.about-stat-label {
    font-size: 0.9rem;
    color: #888;
    margin-top: 8px;
    line-height: 1.4;
}
.about-section {
    margin-bottom: 60px;
}
.about-values {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}
.about-value-card {
    background: white;
    border-radius: 28px;
    padding: 32px;
    box-shadow: var(--shadow-hard);
}
.about-value-icon {
    font-size: 1.8rem;
    color: var(--gold-matte);
    margin-bottom: 16px;
}
.about-value-card h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 8px;
}
.about-value-card p {
    color: #666;
    line-height: 1.5;
    font-size: 0.95rem;
}
.about-services {
    list-style: none;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 32px;
    max-width: 900px;
    margin: 0 auto;
}
.about-services li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #444;
    line-height: 1.5;
}
.about-services i {
    color: var(--gold-matte);
    margin-top: 3px;
}
.about-cta {
    background: var(--graphite);
    color: white;
    border-radius: 32px;
    padding: 48px;
    text-align: center;
    margin-bottom: 60px;
    border: 1px solid rgba(197, 162, 103, 0.3);
}
.about-cta h2 {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 12px;
}
.about-cta p {
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 24px;
}
@media (max-width: 900px) {
    .about-hero,
    .about-values,
    .about-services,
    .about-stats {
        grid-template-columns: 1fr;
    }
    .about-hero-content h2 {
        font-size: 1.8rem;
    }
}
</style>
