<script setup>
import { computed, onMounted, ref } from 'vue';
import api, { formatDate, surveyCategoryLabels, surveyStatusLabels } from '../../api';

const surveys = ref([]);
const loading = ref(true);
const saving = ref(false);
const editing = ref(null);
const error = ref('');
const resultsSurvey = ref(null);
const form = ref(emptyForm());

function emptyQuestion() {
    return { text: '', options: ['', '', ''] };
}

function emptyForm() {
    return {
        title: '',
        description: '',
        status: 'draft',
        category: 'service',
        questions: [emptyQuestion()],
    };
}

const editorTitle = computed(() => (editing.value === 'new' ? 'Новый опрос' : 'Редактирование опроса'));

const previewQuestions = computed(() =>
    form.value.questions
        .map((question, index) => ({
            index: index + 1,
            text: question.text.trim(),
            options: question.options.map((option) => option.trim()).filter(Boolean),
        }))
        .filter((question) => question.text && question.options.length),
);

function formatResponses(count) {
    const n = Math.abs(count) % 100;
    const n1 = n % 10;
    if (n > 10 && n < 20) return `${count} ответов`;
    if (n1 > 1 && n1 < 5) return `${count} ответа`;
    if (n1 === 1) return `${count} ответ`;
    return `${count} ответов`;
}

function normalizeQuestions(questions) {
    return questions.map((question) => ({
        text: question.text.trim(),
        options: question.options.map((option) => option.trim()).filter(Boolean),
    })).filter((question) => question.text && question.options.length);
}

async function load() {
    loading.value = true;
    try {
        const { data } = await api.get('/admin/surveys');
        surveys.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function openEditor(survey = null) {
    error.value = '';
    if (survey) {
        editing.value = survey.id;
        form.value = {
            title: survey.title,
            description: survey.description || '',
            status: survey.status,
            category: survey.category,
            questions: survey.questions.map((question) => ({
                text: question.text,
                options: [...question.options],
            })),
        };
    } else {
        editing.value = 'new';
        form.value = emptyForm();
    }
}

function closeEditor() {
    editing.value = null;
    error.value = '';
}

function addQuestion() {
    form.value.questions.push(emptyQuestion());
}

function removeQuestion(index) {
    if (form.value.questions.length <= 1) return;
    form.value.questions.splice(index, 1);
}

function addOption(questionIndex) {
    form.value.questions[questionIndex].options.push('');
}

function removeOption(questionIndex, optionIndex) {
    const options = form.value.questions[questionIndex].options;
    if (options.length <= 1) return;
    options.splice(optionIndex, 1);
}

async function save() {
    error.value = '';
    const payload = {
        title: form.value.title.trim(),
        description: form.value.description.trim(),
        status: form.value.status,
        category: form.value.category,
        questions: normalizeQuestions(form.value.questions),
    };

    if (!payload.title) {
        error.value = 'Укажите название опроса';
        return;
    }

    if (!payload.questions.length) {
        error.value = 'Добавьте хотя бы один вопрос с вариантами ответов';
        return;
    }

    saving.value = true;
    try {
        if (editing.value === 'new') {
            await api.post('/admin/surveys', payload);
        } else {
            await api.patch(`/admin/surveys/${editing.value}`, payload);
        }
        closeEditor();
        await load();
    } catch (e) {
        error.value = Object.values(e.response?.data?.errors || {}).flat().join(' ')
            || e.response?.data?.message
            || 'Не удалось сохранить опрос';
    } finally {
        saving.value = false;
    }
}

async function remove(survey) {
    if (!confirm(`Удалить опрос «${survey.title}»?`)) return;
    try {
        await api.delete(`/admin/surveys/${survey.id}`);
        if (editing.value === survey.id) closeEditor();
        await load();
    } catch (e) {
        error.value = e.response?.data?.message || 'Не удалось удалить опрос';
    }
}

function showResults(survey) {
    resultsSurvey.value = survey;
}
</script>

<template>
    <div class="admin-page active">
        <div v-show="!editing" id="surveysList">
            <div class="page-header">
                <h2>Опросы клиентов <small>управление и анализ</small></h2>
                <div class="page-actions">
                    <button class="btn-gold" type="button" @click="openEditor()">
                        <i class="fas fa-plus"></i> Создать опрос
                    </button>
                </div>
            </div>

            <div v-if="loading" class="surveys-grid">Загрузка...</div>
            <div v-else class="surveys-grid">
                <div
                    v-for="survey in surveys"
                    :key="survey.id"
                    class="survey-card"
                    @click="openEditor(survey)"
                >
                    <div class="survey-header">
                        <div class="survey-title">{{ survey.title }}</div>
                        <span :class="['survey-status', survey.status]">
                            {{ surveyStatusLabels[survey.status] || survey.status }}
                        </span>
                    </div>
                    <div class="survey-desc">{{ survey.description || '—' }}</div>
                    <div class="survey-meta">
                        <span><i class="fas fa-calendar-alt"></i> Создан: {{ formatDate(survey.created_at) }}</span>
                        <span><i class="fas fa-users"></i> {{ formatResponses(survey.responses_count) }}</span>
                    </div>
                    <div class="survey-actions">
                        <button class="btn-edit-survey" type="button" @click.stop="openEditor(survey)">
                            <i class="fas fa-edit"></i> Редактировать
                        </button>
                        <button class="btn-results" type="button" @click.stop="showResults(survey)">
                            <i class="fas fa-chart-bar"></i> Результаты
                        </button>
                        <button class="btn-delete-survey" type="button" @click.stop="remove(survey)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <p v-if="!loading && !surveys.length" style="text-align:center;color:#888;padding:40px 0;">
                Опросов пока нет. Создайте первый опрос.
            </p>
        </div>

        <div v-if="editing" class="survey-editor active">
            <div class="page-header">
                <div>
                    <button class="btn-back" type="button" @click="closeEditor">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </button>
                    <h2 style="margin-top:12px;">
                        {{ editorTitle }}
                        <small v-if="editing !== 'new'">#{{ editing }}</small>
                    </h2>
                </div>
                <div class="page-actions">
                    <button
                        v-if="editing !== 'new'"
                        class="btn-danger"
                        type="button"
                        @click="remove({ id: editing, title: form.title })"
                    >
                        <i class="fas fa-trash"></i> Удалить
                    </button>
                    <button class="btn-success" type="button" :disabled="saving" @click="save">
                        <i class="fas fa-save"></i> {{ saving ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                </div>
            </div>

            <div class="survey-editor-grid">
                <div class="survey-form">
                    <h3 style="margin-bottom: 20px; font-weight: 600;">Основная информация</h3>
                    <div class="form-group">
                        <label>Название опроса</label>
                        <input v-model="form.title" type="text" placeholder="Введите название опроса">
                    </div>
                    <div class="form-group">
                        <label>Описание</label>
                        <textarea v-model="form.description" rows="3" placeholder="Краткое описание для респондентов"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Статус</label>
                            <select v-model="form.status">
                                <option value="active">Активен</option>
                                <option value="draft">Черновик</option>
                                <option value="inactive">Неактивен</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Категория</label>
                            <select v-model="form.category">
                                <option v-for="(label, value) in surveyCategoryLabels" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin: 24px 0 16px; font-weight: 600;">Вопросы</h3>
                    <div id="questionsContainer">
                        <div
                            v-for="(question, qIndex) in form.questions"
                            :key="qIndex"
                            class="form-group question-item"
                        >
                            <label>Вопрос {{ qIndex + 1 }}</label>
                            <input
                                v-model="question.text"
                                type="text"
                                placeholder="Введите вопрос"
                            >
                            <div class="form-group" style="margin-top: 8px;">
                                <label>Варианты ответов</label>
                                <div class="options-container">
                                    <div
                                        v-for="(option, oIndex) in question.options"
                                        :key="oIndex"
                                        class="option-row"
                                    >
                                        <input
                                            v-model="question.options[oIndex]"
                                            type="text"
                                            placeholder="Вариант ответа"
                                        >
                                        <button
                                            class="btn-remove-option"
                                            type="button"
                                            @click="removeOption(qIndex, oIndex)"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                    <button class="btn-add-option" type="button" @click="addOption(qIndex)">
                                        + Добавить вариант
                                    </button>
                                </div>
                            </div>
                            <button
                                class="btn-remove-question"
                                type="button"
                                @click="removeQuestion(qIndex)"
                            >
                                ✕ Удалить вопрос
                            </button>
                        </div>
                    </div>

                    <button class="btn-gold" style="width:100%; margin-top:12px;" type="button" @click="addQuestion">
                        <i class="fas fa-plus"></i> Добавить вопрос
                    </button>

                    <p v-if="error" class="form-error-text">{{ error }}</p>
                </div>

                <div class="survey-preview">
                    <h3 style="margin-bottom: 20px; font-weight: 600;">Предпросмотр</h3>
                    <div class="preview-survey">
                        <div class="ps-title">{{ form.title || 'Название опроса' }}</div>
                        <div class="ps-desc">{{ form.description || 'Описание опроса' }}</div>
                        <div v-if="previewQuestions.length">
                            <div v-for="question in previewQuestions" :key="question.index" class="ps-question">
                                <div class="pq-text">{{ question.index }}. {{ question.text }}</div>
                                <div class="pq-options">
                                    <label v-for="(option, optionIndex) in question.options" :key="optionIndex">
                                        <input type="radio" :name="`preview_q${question.index}`" disabled>
                                        {{ option }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p v-else style="color:#888;font-size:0.9rem;">Добавьте вопросы и варианты ответов</p>
                    </div>
                    <div class="survey-preview-note">
                        <i class="fas fa-info-circle"></i>
                        Изменения сохраняются после нажатия кнопки «Сохранить»
                    </div>
                </div>
            </div>
        </div>

        <div v-if="resultsSurvey" class="survey-results-modal" @click.self="resultsSurvey = null">
            <div class="survey-results-window">
                <h3>Результаты опроса</h3>
                <p>{{ resultsSurvey.title }}</p>
                <div class="survey-results-stat">
                    <strong>{{ resultsSurvey.responses_count }}</strong>
                    {{ formatResponses(resultsSurvey.responses_count) }}
                </div>
                <button class="btn-gold" type="button" @click="resultsSurvey = null">Закрыть</button>
            </div>
        </div>
    </div>
</template>
