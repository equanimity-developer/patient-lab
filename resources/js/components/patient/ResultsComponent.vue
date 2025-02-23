<template>
    <div class="results-container">
        <div v-if="patient" class="patient-info">
            <div class="patient-header">
                <h2>{{ t('patient.information') }}</h2>
                <button @click="logout" class="logout-btn">
                    {{ t('auth.logout') }}
                </button>
            </div>
            <div class="patient-details">
                <p><strong>{{ t('patient.name') }}:</strong> {{ patient.name }} {{ patient.surname }}</p>
                <p><strong>{{ t('patient.sex.label') }}:</strong> {{ t(`patient.sex.${patient.sex}`) }}</p>
                <p><strong>{{ t('patient.birthDate') }}:</strong> {{ formatDate(patient.birthDate) }}</p>
            </div>
        </div>

        <h2>{{ t('results.title') }}</h2>

        <div v-if="loading" class="loading">
            {{ t('common.loading') }}
        </div>

        <div v-else-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <div v-else-if="!orders?.length" class="no-results">
            {{ t('results.noResults') }}
        </div>

        <div v-else class="orders-list">
            <div v-for="order in orders" :key="order.orderId" class="order-card">
                <h3>{{ t('results.orderNumber', {number: order.orderId}) }}</h3>
                <div class="results-table">
                    <table>
                        <thead>
                        <tr>
                            <th>{{ t('results.testName') }}</th>
                            <th>{{ t('results.value') }}</th>
                            <th>{{ t('results.referenceRange') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(result, index) in order.results" :key="index">
                            <td>{{ result.name }}</td>
                            <td>{{ result.value }}</td>
                            <td>{{ result.reference }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="pagination" class="pagination">
                <button
                    :disabled="!pagination.prev_page_url"
                    @click="changePage(pagination.current_page - 1)"
                    class="pagination-btn"
                >
                    {{ t('common.previous') }}
                </button>

                <span class="pagination-info">
          {{
                        t('pagination.page', {
                            current: pagination.current_page,
                            total: pagination.last_page
                        })
                    }}
        </span>

                <button
                    :disabled="!pagination.next_page_url"
                    @click="changePage(pagination.current_page + 1)"
                    class="pagination-btn"
                >
                    {{ t('common.next') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import axios from '@/plugins/axios.js';
import {handleLogout} from '@/utils/auth.js';
import {useI18n} from "vue-i18n";

const {t} = useI18n();
const patient = ref(null);
const orders = ref([]);
const loading = ref(true);
const error = ref(null);
const pagination = ref(null);

const fetchResults = async (page = 1) => {
    try {
        const response = await axios.get(`/api/results?page=${page}`);
        patient.value = response.data.patient;
        orders.value = response.data.orders;
        pagination.value = response.data.pagination;
    } catch (err) {
        error.value = err.response?.data?.message || 'Error fetching results';
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    loading.value = true;
    fetchResults(page);
};

const formatDate = (date) => {
    if (!date) return '';
    return date.split('T')[0];
};

const logout = () => {
    handleLogout();
};

onMounted(() => {
    fetchResults();
});
</script>

<style scoped>
.results-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 1rem;
}

.loading, .no-results {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.patient-info {
    margin-bottom: 2rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.patient-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.patient-details {
    display: grid;
    gap: 0.5rem;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.order-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
}

.results-table {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th, td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
}

tr:hover {
    background-color: #f8f9fa;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 0.75rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.pagination-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    cursor: pointer;
}

.pagination-btn:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
    opacity: 0.6;
}

.pagination-btn:not(:disabled):hover {
    background-color: #f8f9fa;
}

.pagination-info {
    color: #666;
}

.logout-btn {
    padding: 0.5rem 1rem;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.logout-btn:hover {
    background-color: #c82333;
}
</style>
