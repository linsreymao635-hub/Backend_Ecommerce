<template>
  <div class="products-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <div class="header-content">
          <div class="header-text">
            <h1 class="page-title">All Products</h1>
            <p class="page-subtitle">Discover our curated collection</p>
          </div>
          <!-- Search Bar -->
          <div class="search-bar">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              v-model="filters.search"
              class="search-input"
              placeholder="Search products..."
              @input="debounceSearch"
            />
            <button v-if="filters.search" class="search-clear" @click="clearFilters">&times;</button>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <!-- Products Grid (always visible, no spinner delay) -->
      <div class="results-bar">
        <p class="results-count" v-if="products.length || !loading">
          {{ pagination.total ? pagination.total + ' product' + (pagination.total !== 1 ? 's' : '') + ' found' : '' }}&nbsp;
        </p>
      </div>

      <!-- Products Grid -->
      <div class="product-grid" :class="{ 'is-loading': loading && products.length === 0 }">
        <template v-if="products.length > 0">
          <ProductCard
            v-for="p in products"
            :key="p.id"
            :product="p"
            @view="viewProduct"
          />
        </template>
        <!-- Skeleton cards while loading -->
        <template v-else-if="loading">
          <div v-for="n in 6" :key="n" class="skeleton-card">
            <div class="skeleton-img"></div>
            <div class="skeleton-body">
              <div class="skeleton-line w-40"></div>
              <div class="skeleton-line w-80"></div>
              <div class="skeleton-line w-30"></div>
            </div>
          </div>
        </template>
      </div>

      <!-- Empty state -->
      <div v-if="!loading && products.length === 0" class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <h3>No products found</h3>
        <p>Try adjusting your search terms</p>
        <button class="btn btn-primary btn-sm" @click="clearFilters">Clear Search</button>
      </div>

      <!-- Pagination -->
      <div class="pagination-wrapper" v-if="pagination.last_page > 1">
        <div class="pagination">
          <button
            class="page-btn prev"
            :disabled="pagination.current_page <= 1"
            @click="fetchProducts(pagination.current_page - 1)"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
            Prev
          </button>

          <template v-for="p in pagination.last_page" :key="p">
            <button
              v-if="
                p === 1 ||
                p === pagination.last_page ||
                (p >= pagination.current_page - 1 && p <= pagination.current_page + 1)
              "
              class="page-btn"
              :class="{ active: p === pagination.current_page }"
              @click="fetchProducts(p)"
            >{{ p }}</button>
            <span
              v-else-if="
                p === pagination.current_page - 2 || p === pagination.current_page + 2
              "
              class="page-dots"
            >&hellip;</span>
          </template>

          <button
            class="page-btn next"
            :disabled="pagination.current_page >= pagination.last_page"
            @click="fetchProducts(pagination.current_page + 1)"
          >
            Next
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import ProductCard from '../components/ProductCard.vue'

const route       = useRoute()
const router      = useRouter()
const products    = ref([])
const loading     = ref(true)
const filters     = reactive({ search: '' })
const pagination  = ref({})
let searchTimer   = null

const fetchProducts = async (page = 1) => {
  loading.value = true
  const params  = { page, per_page: 12 }
  if (filters.search) params.search = filters.search
  try {
    const { data } = await api.get('/products', { params })
    products.value   = data.data
    pagination.value = { total: data.total, last_page: data.last_page, current_page: data.current_page }
  } catch (error) {
    console.error('Error fetching products:', error)
    products.value = []
  }
  loading.value    = false
}

const debounceSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => fetchProducts(1), 400)
}

const clearFilters = () => {
  filters.search = ''
  fetchProducts(1)
}

const viewProduct = (id) => {
  router.push(`/products/${id}`)
}

onMounted(async () => {
  if (route.query.search) {
    filters.search = route.query.search
  }
  fetchProducts()
})
</script>

<style scoped>
.products-page {
  padding: 0 0 60px;
}

/* ── Header ── */
.page-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 48px 0;
  margin-bottom: 32px;
  position: relative;
  overflow: hidden;
}
.page-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 50%);
}
.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  position: relative;
  z-index: 1;
}
.header-text h1 {
  font-size: 2rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 4px;
}
.page-subtitle {
  color: rgba(255,255,255,0.75);
  font-size: 1rem;
}

/* ── Search ── */
.search-bar {
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 12px;
  padding: 0 16px;
  min-width: 320px;
  transition: all 0.2s;
  backdrop-filter: blur(4px);
}
.search-bar:focus-within {
  background: rgba(255,255,255,0.25);
  border-color: rgba(255,255,255,0.5);
}
.search-icon {
  width: 20px;
  height: 20px;
  color: rgba(255,255,255,0.6);
  flex-shrink: 0;
}
.search-input {
  flex: 1;
  background: none;
  border: none;
  padding: 14px 12px;
  font-size: 0.95rem;
  color: #fff;
  outline: none;
}
.search-input::placeholder { color: rgba(255,255,255,0.5); }
.search-clear {
  background: rgba(255,255,255,0.2);
  border: none;
  color: #fff;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  flex-shrink: 0;
}
.search-clear:hover { background: rgba(255,255,255,0.35); }

/* ── Results bar ── */
.results-bar {
  margin-bottom: 24px;
}
.results-count {
  color: var(--text-light);
  font-size: 0.9rem;
  font-weight: 500;
}
.results-count-skeleton {
  height: 1.35rem;
}

/* ── Product Grid ── */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 24px;
  opacity: 1;
  transition: opacity 0.3s ease;
}
.product-grid.is-loading {
  opacity: 0.6;
}

/* ── Skeleton Cards ── */
.skeleton-card {
  background: white;
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
}
.skeleton-img {
  height: 200px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s ease-in-out infinite;
}
.skeleton-body {
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.skeleton-line {
  height: 14px;
  border-radius: 6px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s ease-in-out infinite;
}
.w-30 { width: 30%; }
.w-40 { width: 40%; }
.w-80 { width: 80%; }
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ── Empty State ── */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-icon {
  width: 64px;
  height: 64px;
  color: var(--text-light);
  opacity: 0.4;
  margin-bottom: 20px;
}
.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 8px;
}
.empty-state p {
  color: var(--text-light);
  margin-bottom: 20px;
}

/* ── Pagination ── */
.pagination-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 48px;
}
.pagination {
  display: flex;
  align-items: center;
  gap: 4px;
  background: white;
  padding: 6px;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}
.page-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 8px 14px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text);
  transition: all 0.2s;
}
.page-btn:hover:not(:disabled):not(.active) {
  background: var(--primary-light);
  color: var(--primary);
}
.page-btn.active {
  background: var(--primary);
  color: white;
  font-weight: 600;
}
.page-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}
.page-btn.prev,
.page-btn.next {
  font-weight: 600;
  padding: 8px 16px;
}
.page-dots {
  padding: 8px 4px;
  color: var(--text-light);
  letter-spacing: 2px;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    align-items: stretch;
    gap: 16px;
  }
  .page-header {
    padding: 32px 0;
  }
  .search-bar {
    min-width: 0;
  }
  .product-grid {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
  }
  .pagination {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
