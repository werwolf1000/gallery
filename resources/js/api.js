import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

export default api;

export function formatPrice(value) {
    return new Intl.NumberFormat('ru-RU').format(value) + ' ₽';
}

export function formatDate(value) {
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(value));
}

export const orderStatusLabels = {
    pending: 'В обработке',
    active: 'Активный',
    completed: 'Завершён',
    cancelled: 'Отменён',
};

export const PROMO_BONUS = 'Бесплатная консультация для 3-х членов семьи или друзей при заключении договора';

export const propertyStatusLabels = {
    for_sale: 'В продаже',
    sold: 'Продано',
    archive: 'Архив',
};
