<script setup>
import { computed, onMounted, ref } from 'vue';
import api from '../../api';

const slides = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const previewIndex = ref(0);
const editingId = ref(null);

const form = ref(emptyForm());

function emptyForm() {
    return {
        title: '',
        subtitle: '',
        image_url: '',
        link: '',
        button_text: '',
        is_active: true,
        sort_order: 1,
    };
}

const previewSlide = computed(() => {
    if (form.value.image_url) {
        return form.value;
    }
    return slides.value[previewIndex.value] || null;
});

async function load() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/slides');
        slides.value = data;
        if (slides.value.length && !editingId.value) {
            selectSlide(slides.value[0]);
        }
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function selectSlide(slide) {
    editingId.value = slide?.id ?? 'new';
    error.value = '';
    if (slide) {
        form.value = { ...slide };
        previewIndex.value = slides.value.findIndex((s) => s.id === slide.id);
    } else {
        form.value = { ...emptyForm(), sort_order: slides.value.length + 1 };
        previewIndex.value = slides.value.length;
    }
}

function newSlide() {
    selectSlide(null);
}

function prevPreview() {
    if (!slides.value.length) return;
    previewIndex.value = (previewIndex.value - 1 + slides.value.length) % slides.value.length;
    selectSlide(slides.value[previewIndex.value]);
}

function nextPreview() {
    if (!slides.value.length) return;
    previewIndex.value = (previewIndex.value + 1) % slides.value.length;
    selectSlide(slides.value[previewIndex.value]);
}

async function save() {
    error.value = '';
    saving.value = true;
    try {
        if (editingId.value === 'new') {
            const { data } = await api.post('/admin/slides', form.value);
            editingId.value = data.id;
        } else {
            await api.patch(`/admin/slides/${editingId.value}`, form.value);
        }
        await load();
        const current = slides.value.find((s) => s.id === editingId.value);
        if (current) selectSlide(current);
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось сохранить слайд';
    } finally {
        saving.value = false;
    }
}

async function remove(slide) {
    if (!confirm(`Удалить слайд «${slide.title}»?`)) return;
    try {
        await api.delete(`/admin/slides/${slide.id}`);
        if (editingId.value === slide.id) {
            editingId.value = null;
            form.value = emptyForm();
        }
        await load();
        if (slides.value.length) {
            selectSlide(slides.value[0]);
        } else {
            newSlide();
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'Не удалось удалить слайд';
    }
}
</script>

<template>
    <div class="admin-page active">
        <div class="page-header">
            <h2>Настройка слайдера <small>управление изображениями</small></h2>
            <div class="page-actions">
                <button class="btn-gold" type="button" @click="newSlide"><i class="fas fa-plus"></i> Добавить слайд</button>
            </div>
        </div>

        <div class="slider-settings">
            <div class="slider-form">
                <h3 style="margin-bottom: 20px; font-weight: 600;">Редактор слайда</h3>
                <div class="form-group">
                    <label>Изображение (URL)</label>
                    <input v-model="form.image_url" type="text" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Заголовок</label>
                    <input v-model="form.title" type="text" placeholder="Заголовок слайда">
                </div>
                <div class="form-group">
                    <label>Подпись (необязательно)</label>
                    <input v-model="form.subtitle" type="text" placeholder="Подпись под заголовком">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Порядок</label>
                        <input v-model.number="form.sort_order" type="number">
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <select v-model="form.is_active">
                            <option :value="true">Активен</option>
                            <option :value="false">Скрыт</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ссылка (необязательно)</label>
                    <input v-model="form.link" type="text" placeholder="/apartments">
                </div>
                <p v-if="error" class="form-error-text">{{ error }}</p>
                <button
                    class="btn-gold"
                    type="button"
                    style="width:100%; justify-content:center;"
                    :disabled="saving"
                    @click="save"
                >
                    <i class="fas fa-save"></i> {{ saving ? 'Сохранение...' : 'Сохранить слайд' }}
                </button>
            </div>

            <div class="slider-preview">
                <h3 style="margin-bottom: 20px; font-weight: 600;">Предпросмотр слайдера</h3>
                <div
                    class="preview-slider"
                    :style="{ backgroundImage: previewSlide?.image_url ? `url('${previewSlide.image_url}')` : 'none' }"
                >
                    <div v-if="previewSlide?.title" class="slide-overlay">{{ previewSlide.title }}</div>
                    <button v-if="slides.length > 1" class="slide-nav prev" type="button" @click="prevPreview"><i class="fas fa-chevron-left"></i></button>
                    <button v-if="slides.length > 1" class="slide-nav next" type="button" @click="nextPreview"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div v-if="slides.length" class="preview-dots">
                    <button
                        v-for="(slide, index) in slides"
                        :key="slide.id"
                        type="button"
                        :class="['dot', { active: previewIndex === index }]"
                        @click="selectSlide(slide)"
                    ></button>
                </div>

                <div class="slider-items">
                    <h4 style="margin-bottom:12px; font-weight:500; color:#888; font-size:0.85rem;">Список слайдов</h4>
                    <div v-if="loading">Загрузка...</div>
                    <div v-else-if="!slides.length" style="color:#888;font-size:0.85rem;">Слайдов пока нет. Добавьте первый слайд.</div>
                    <div
                        v-for="slide in slides"
                        v-else
                        :key="slide.id"
                        :class="['slider-item', { 'is-active': editingId === slide.id }]"
                        @click="selectSlide(slide)"
                    >
                        <div class="si-preview" :style="{ backgroundImage: `url('${slide.image_url}')` }"></div>
                        <div class="si-info">
                            <div class="si-title">{{ slide.title }}</div>
                            <div class="si-sub">{{ slide.subtitle || '—' }}</div>
                        </div>
                        <div class="si-order">#{{ slide.sort_order }}</div>
                        <div class="si-actions">
                            <button class="si-edit" type="button" @click.stop="selectSlide(slide)">Изм.</button>
                            <button class="si-delete" type="button" @click.stop="remove(slide)">Уд.</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.slider-item.is-active {
    border-color: var(--gold-matte);
    box-shadow: 0 0 0 2px rgba(197, 162, 103, 0.2);
}
.slider-item {
    cursor: pointer;
}
</style>
