<template>
    <div class="login-container">
        <form @submit.prevent="login" class="login-form">
            <h2>{{ t('auth.login.title') }}</h2>

            <div class="form-group">
                <label for="login">{{ t('auth.login.loginLabel') }}</label>
                <input
                    type="text"
                    id="login"
                    v-model="form.login"
                    required
                    class="form-control"
                    :placeholder="t('auth.login.loginPlaceholder')"
                >
            </div>

            <div class="form-group">
                <label for="password">{{ t('auth.login.birthDateLabel') }}</label>
                <input
                    type="text"
                    id="password"
                    v-model="form.password"
                    required
                    class="form-control"
                    :placeholder="t('auth.login.birthDatePlaceholder')"
                    maxlength="10"
                    @input="formatBirthDate"
                >
            </div>

            <div v-if="error" class="alert alert-danger">
                {{ error }}
            </div>

            <button type="submit" class="btn btn-primary" :disabled="loading">
                {{ loading ? t('auth.login.loading') : t('auth.login.submitButton') }}
            </button>
        </form>
    </div>
</template>

<script setup>
import {ref, reactive, onMounted} from 'vue';
import {useRouter} from 'vue-router';
import {useI18n} from 'vue-i18n';
import axios from '@/plugins/axios.js';
import {setAuthToken, isAuthenticated} from '@/utils/auth.js';
import {formatDateString, isValidDateFormat} from "@/utils/dateUtils.js";

const router = useRouter();
const {t} = useI18n();

const form = reactive({
    login: '',
    password: ''
});
const error = ref(null);
const loading = ref(false);

onMounted(() => {
    if (isAuthenticated()) {
        router.push('/patient/results');
    }
});

const formatBirthDate = () => {
    form.password = formatDateString(form.password);
};

const login = async () => {
    loading.value = true;
    error.value = null;

    if (!isValidDateFormat(form.password)) {
        error.value = t('auth.login.errors.invalidDate');
        loading.value = false;
        return;
    }

    try {
        const response = await axios.post('/api/login', form);
        setAuthToken(response.data.token);
        router.push('/patient/results');
    } catch (err) {
        error.value = err.response?.data?.message || t('auth.login.errors.default');
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-container {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-control {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
}

.btn-primary:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

.alert {
    padding: 0.75rem;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
