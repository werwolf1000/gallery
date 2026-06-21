<script setup>
import { computed, onMounted, ref } from 'vue';
import api, { formatDate, formatPrice, propertyStatusLabels } from '../../api';
import { getCategoryLabel, propertyCategoryOptions } from '../../config/propertyCategories';

const properties = ref([]);
const loading = ref(true);
const saving = ref(false);
const editing = ref(null);
const error = ref('');
const form = ref(emptyForm());

function emptyForm() {
    return {
        title: '',
        address: '',
        category: 'apartment',
        type: '',
        area: null,
        rooms: null,
        floor: null,
        price: 0,
        bonus: '',
        image_url: '',
        badge: 'VIP',
        status: 'for_sale',
        description: '',
        is_active: true,
        sort_order: 0,
    };
}

const previewStatus = computed(() => propertyStatusLabels[form.value.status] || form.value.status);

async function load() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/properties');
        properties.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function openEditor(property = null) {
    error.value = '';
    if (property) {
        editing.value = property.id;
        form.value = { ...property };
    } else {
        editing.value = 'new';
        form.value = emptyForm();
    }
}

function closeEditor() {
    editing.value = null;
    error.value = '';
}

async function save() {
    error.value = '';
    saving.value = true;
    try {
        if (editing.value === 'new') {
            await api.post('/admin/properties', form.value);
        } else {
            await api.patch(`/admin/properties/${editing.value}`, form.value);
        }
        closeEditor();
        await load();
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось сохранить карточку';
    } finally {
        saving.value = false;
    }
}

async function remove(property) {
    if (!confirm(`Удалить «${property.title}»?`)) return;
    try {
        await api.delete(`/admin/properties/${property.id}`);
        if (editing.value === property.id) closeEditor();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || 'Не удалось удалить карточку';
    }
}
</script>

<template>
    <div class="admin-page active">
        <div v-show="!editing" id="propertiesList">
            <div class="page-header">
                <h2>Карточки недвижимости <small>управление объектами</small></h2>
                <div class="page-actions">
                    <button class="btn-gold" type="button" @click="openEditor()"><i class="fas fa-plus"></i> Создать карточку</button>
                </div>
            </div>

            <div class="properties-grid">
                <div v-if="loading">Загрузка...</div>
                <div
                    v-for="property in properties"
                    v-else
                    :key="property.id"
                    class="property-card-item"
                    @click="openEditor(property)"
                >
                    <div class="pci-img" :style="{ backgroundImage: `url('${property.image_url}')` }">
                        <span :class="['pci-status', property.status === 'sold' ? 'sold' : '']">
                            {{ propertyStatusLabels[property.status] || property.status }}
                        </span>
                    </div>
                    <div class="pci-body">
                        <div class="pci-title">{{ property.title }}</div>
                        <div class="pci-address"><i class="fas fa-map-pin" style="color:var(--gold-matte);"></i> {{ property.address }}</div>
                        <div class="pci-details">
                            <span><i class="fas fa-folder"></i> {{ getCategoryLabel(property.category) }}</span>
                            <span v-if="property.area"><i class="fas fa-ruler-combined"></i> {{ property.area }} м²</span>
                            <span v-if="property.rooms"><i class="fas fa-bed"></i> {{ property.rooms }} комн.</span>
                        </div>
                        <div class="pci-price">{{ formatPrice(property.price) }}</div>
                        <div class="pci-footer">
                            <span class="pci-date">Обновлено: {{ formatDate(property.updated_at) }}</span>
                            <div class="pci-actions">
                                <button class="pci-edit" type="button" @click.stop="openEditor(property)"><i class="fas fa-edit"></i> Настроить</button>
                                <button class="pci-delete" type="button" @click.stop="remove(property)"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="editing" class="property-editor active">
            <div class="page-header">
                <div>
                    <button class="btn-back" type="button" @click="closeEditor"><i class="fas fa-arrow-left"></i> Назад к списку</button>
                    <h2 style="margin-top:12px;">
                        {{ editing === 'new' ? 'Новая карточка' : 'Редактирование карточки' }}
                        <small v-if="editing !== 'new'">#{{ editing }}</small>
                    </h2>
                </div>
                <div class="page-actions">
                    <button v-if="editing !== 'new'" class="btn-danger" type="button" @click="remove({ id: editing, title: form.title })"><i class="fas fa-trash"></i> Удалить</button>
                    <button class="btn-success" type="button" :disabled="saving" @click="save"><i class="fas fa-save"></i> {{ saving ? 'Сохранение...' : 'Сохранить' }}</button>
                </div>
            </div>

            <div class="property-editor-grid">
                <div class="property-form">
                    <h3 style="margin-bottom: 20px; font-weight: 600;">Основная информация</h3>
                    <div class="form-group">
                        <label>Название объекта</label>
                        <input v-model="form.title" type="text" placeholder="Введите название">
                    </div>
                    <div class="form-group">
                        <label>Адрес</label>
                        <input v-model="form.address" type="text" placeholder="Введите адрес">
                    </div>
                    <div class="form-group">
                        <label>Группа (раздел сайта)</label>
                        <select v-model="form.category">
                            <option v-for="option in propertyCategoryOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Площадь (м²)</label>
                            <input v-model.number="form.area" type="number">
                        </div>
                        <div class="form-group">
                            <label>Комнат</label>
                            <input v-model.number="form.rooms" type="number">
                        </div>
                        <div class="form-group">
                            <label>Этаж</label>
                            <input v-model.number="form.floor" type="number">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Цена (₽)</label>
                            <input v-model.number="form.price" type="number">
                        </div>
                        <div class="form-group">
                            <label>Статус</label>
                            <select v-model="form.status">
                                <option value="for_sale">В продаже</option>
                                <option value="sold">Продано</option>
                                <option value="archive">Архив</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Описание</label>
                        <textarea v-model="form.description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Изображение (URL)</label>
                        <input v-model="form.image_url" type="text">
                    </div>
                    <div class="form-group">
                        <label>Бонусная строка</label>
                        <input v-model="form.bonus" type="text" placeholder="Например: ✦ Бонус 0.7% токенами">
                    </div>
                    <p v-if="error" class="form-error-text">{{ error }}</p>
                </div>

                <div class="property-preview">
                    <h3 style="margin-bottom: 20px; font-weight: 600;">Предпросмотр</h3>
                    <div class="preview-card">
                        <div
                            class="preview-img"
                            :style="{ backgroundImage: form.image_url ? `url('${form.image_url}')` : 'none' }"
                        >
                            <div class="preview-ribbon">{{ previewStatus }}</div>
                        </div>
                        <div class="preview-info">
                            <div class="preview-title">{{ form.title || 'Название объекта' }}</div>
                            <div class="preview-details">
                                <span v-if="form.area">{{ form.area }} м²</span>
                                <span v-if="form.rooms">{{ form.rooms }} комнаты</span>
                            </div>
                            <div class="preview-price">{{ form.price ? formatPrice(form.price) : '—' }}</div>
                            <div v-if="form.bonus" class="preview-bonus">{{ form.bonus }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 20px; padding: 16px; background: #f8f7f5; border-radius: 16px;">
                        <p style="font-size:0.8rem; color:#888;"><i class="fas fa-info-circle" style="color:var(--gold-matte);"></i> Изменения сохраняются после нажатия кнопки «Сохранить»</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
