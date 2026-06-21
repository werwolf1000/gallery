export const PROPERTY_CATEGORIES = {
    purchase: {
        slug: 'purchase',
        path: '/purchase',
        routeName: 'purchase',
        label: 'Покупка',
        title: 'Покупка недвижимости',
        description: 'Эксклюзивные предложения для покупки премиальной недвижимости',
    },
    rent: {
        slug: 'rent',
        path: '/rent',
        routeName: 'rent',
        label: 'Аренда',
        title: 'Аренда недвижимости',
        description: 'Премиальные объекты для аренды — квартиры, дома и апартаменты',
    },
    izhs: {
        slug: 'izhs',
        path: '/izhs',
        routeName: 'izhs',
        label: 'Строительство ИЖС',
        title: 'Строительство ИЖС',
        description: 'Индивидуальное жилищное строительство под ключ от архитектурного бюро',
    },
    renovation: {
        slug: 'renovation',
        path: '/renovation',
        routeName: 'renovation',
        label: 'Ремонт',
        title: 'Ремонт и отделка',
        description: 'Профессиональный ремонт и отделка премиум-класса',
    },
    apartment: {
        slug: 'apartment',
        path: '/apartments',
        routeName: 'apartments',
        label: 'Квартиры',
        title: 'Квартиры в премиум-объектах',
        description: 'Эксклюзивные предложения от архитектурного бюро и агентства недвижимости',
    },
    home_staging: {
        slug: 'home_staging',
        path: '/home-staging',
        routeName: 'home-staging',
        label: 'Хоумстейджинг',
        title: 'Хоумстейджинг премиум-класса',
        description: 'Профессиональная подготовка недвижимости к продаже или аренде',
    },
};

/** Категории с отдельными страницами-каталогами (как /apartments) */
export const CATALOG_CATEGORY_SLUGS = ['purchase', 'rent', 'izhs', 'renovation', 'apartment'];

export function getCategoryConfig(slug) {
    return PROPERTY_CATEGORIES[slug] ?? null;
}

export function getCategoryLabel(slug) {
    return PROPERTY_CATEGORIES[slug]?.label ?? slug;
}

export const propertyCategoryOptions = Object.entries(PROPERTY_CATEGORIES).map(([slug, config]) => ({
    value: slug,
    label: config.label,
}));
