<template>
  <nav class="navbar">
    <div class="container nav-inner">
      <router-link to="/" class="nav-brand">🛒 ShopVue</router-link>
      <div class="nav-links">
        <router-link to="/">Home</router-link>
        <div class="dropdown">
          <router-link to="/products" class="dropdown-toggle">Products ▾</router-link>
          <div class="dropdown-menu dropdown-menu-products">
            <div v-if="loadingProducts" class="dropdown-loading">Loading...</div>
            <div v-else-if="featuredProducts.length === 0" class="dropdown-empty">No products</div>
            <div v-else class="dropdown-products">
              <router-link 
                v-for="product in featuredProducts" 
                :key="product.id"
                :to="`/products/${product.id}`"
                class="dropdown-product-item"
              >
                <img 
                  :src="product.image_url || 'https://via.placeholder.com/40x40?text=No+Image'" 
                  :alt="product.name"
                  class="dropdown-product-img"
                />
                <div class="dropdown-product-info">
                  <span class="dropdown-product-name">{{ product.name }}</span>
                  <span class="dropdown-product-price">${{ product.price }}</span>
                </div>
              </router-link>
            </div>
          </div>
        </div>
        <div class="dropdown">
          <a href="#" class="dropdown-toggle">Categories ▾</a>
          <div class="dropdown-menu">
            <router-link to="/products">All Categories</router-link>
            <div v-for="cat in categories" :key="cat.id">
              <router-link :to="`/products?category=${cat.id}`">{{ cat.name }}</router-link>
            </div>
          </div>
        </div>
        <template v-if="isAuth">
          <router-link to="/wishlist">❤️ Wishlist</router-link>
          <router-link to="/cart">
            🛒 Cart <span v-if="cartCount" class="cart-badge">{{ cartCount }}</span>
          </router-link>
          <router-link to="/orders">Orders</router-link>
          <router-link to="/profile">Profile</router-link>
          <div class="dropdown">
            <button class="btn btn-sm btn-outline">{{ user?.name }} ▾</button>
            <div class="dropdown-menu">
              <router-link to="/profile">Profile</router-link>
              <a href="#" @click.prevent="handleLogout">Logout</a>
            </div>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn-outline btn-sm">Login</router-link>
          <router-link to="/register" class="btn btn-primary btn-sm">Register</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import categoriesService from '../services/categories'

const store     = useStore()
const router    = useRouter()
const isAuth    = computed(() => store.getters.isAuthenticated)
const user      = computed(() => store.getters.currentUser)
const cartCount = computed(() => store.getters.cartCount)
const featuredProducts = computed(() => store.getters.products)
const loadingProducts = computed(() => store.getters.productsLoading)
const categories = ref([])

onMounted(async () => {
  // Fetch products for the Navbar dropdown
  if (!loadingProducts.value) {
    await store.dispatch('fetchProducts', 5)
  }
  // Fetch categories for the categories dropdown
  try {
    const { data } = await categoriesService.getAll()
    categories.value = data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
})

const handleLogout = async () => {
  await store.dispatch('logout')
  router.push('/login')
}
</script>

<style scoped>
.navbar { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.1); position: sticky; top: 0; z-index: 100; }
.nav-inner { display: flex; align-items: center; justify-content: space-between; height: 64px; }
.nav-brand { font-size: 1.4rem; font-weight: 700; color: var(--primary); text-decoration: none; }
.nav-links { display: flex; align-items: center; gap: 20px; }
.nav-links a { color: var(--text); text-decoration: none; font-weight: 500; transition: color .2s; }
.nav-links a:hover, .nav-links a.router-link-active { color: var(--primary); }
.cart-badge { background: var(--danger); color: #fff; border-radius: 50%; padding: 2px 7px; font-size: .75rem; margin-left: 3px; font-weight: 600; }
.dropdown { position: relative; }
.dropdown-toggle { color: var(--text); text-decoration: none; font-weight: 500; cursor: pointer; }
.dropdown-toggle:hover { color: var(--primary); }
.dropdown-menu { display: none; position: absolute; top: 110%; left: 0; background: white; box-shadow: var(--shadow-xl); border-radius: var(--radius); min-width: 200px; padding: 8px 0; z-index: 200; border: 1px solid var(--border); }
.dropdown:hover .dropdown-menu { display: block; animation: fadeIn .2s ease; }
.dropdown-menu a { display: block; padding: 10px 20px; color: var(--text); text-decoration: none; font-size: .9rem; }
.dropdown-menu a:hover { background: var(--primary-light); color: var(--primary); }
.dropdown-menu-products { min-width: 320px; padding: 12px; max-height: 500px; overflow-y: auto; }
.dropdown-loading { padding: 20px; text-align: center; color: var(--text-light); }
.dropdown-empty { padding: 20px; text-align: center; color: var(--text-light); }
.dropdown-products { display: flex; flex-direction: column; gap: 8px; }
.dropdown-product-item { display: flex; align-items: center; gap: 12px; padding: 10px; border-radius: 8px; text-decoration: none; transition: background 0.2s; }
.dropdown-product-item:hover { background: #f9fafb; }
.dropdown-product-img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb; }
.dropdown-product-info { display: flex; flex-direction: column; gap: 2px; flex: 1; }
.dropdown-product-name { font-size: 0.9rem; font-weight: 600; color: #1f2937; }
.dropdown-product-price { font-size: 0.85rem; font-weight: 700; color: var(--primary); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
</style>