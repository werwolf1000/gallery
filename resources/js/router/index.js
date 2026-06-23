import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { CATALOG_CATEGORY_SLUGS, PROPERTY_CATEGORIES } from '../config/propertyCategories';

const catalogRoutes = CATALOG_CATEGORY_SLUGS.map((slug) => {
    const config = PROPERTY_CATEGORIES[slug];
    return {
        path: config.path,
        name: config.routeName,
        component: () => import('../pages/site/PropertyCatalogPage.vue'),
        meta: { categorySlug: slug },
    };
});

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior(to, _from, savedPosition) {
        if (to.hash) {
            return {
                el: to.hash,
                behavior: 'smooth',
                top: 100,
            };
        }
        if (savedPosition) {
            return savedPosition;
        }
        return { top: 0 };
    },
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../pages/site/HomePage.vue'),
        },
        ...catalogRoutes,
        {
            path: '/apartments/:id',
            name: 'property-detail',
            component: () => import('../pages/site/PropertyDetailPage.vue'),
        },
        {
            path: '/home-staging',
            name: 'home-staging',
            component: () => import('../pages/site/HomeStagingPage.vue'),
        },
        {
            path: '/about',
            name: 'about',
            component: () => import('../pages/site/AboutPage.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../pages/site/LoginPage.vue'),
            meta: { guest: true },
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../pages/site/RegisterPage.vue'),
            meta: { guest: true },
        },
        {
            path: '/orders',
            name: 'orders',
            component: () => import('../pages/site/OrdersPage.vue'),
            meta: { requiresAuth: true },
        },
        {
            path: '/admin',
            component: () => import('../layouts/AdminLayout.vue'),
            meta: { requiresAuth: true, requiresAdmin: true },
            children: [
                { path: '', redirect: '/admin/orders' },
                {
                    path: 'orders',
                    name: 'admin-orders',
                    component: () => import('../pages/admin/OrdersPage.vue'),
                },
                {
                    path: 'users',
                    name: 'admin-users',
                    component: () => import('../pages/admin/UsersPage.vue'),
                },
                {
                    path: 'surveys',
                    name: 'admin-surveys',
                    component: () => import('../pages/admin/SurveysPage.vue'),
                },
                {
                    path: 'properties',
                    name: 'admin-properties',
                    component: () => import('../pages/admin/PropertiesPage.vue'),
                },
                {
                    path: 'slider',
                    name: 'admin-slider',
                    component: () => import('../pages/admin/SliderPage.vue'),
                },
            ],
        },
    ],
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.loaded) {
        await auth.fetchUser();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.requiresAdmin && !auth.isAdmin) {
        return { name: 'home' };
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return auth.isAdmin ? { name: 'admin-orders' } : { name: 'orders' };
    }
});

export default router;
