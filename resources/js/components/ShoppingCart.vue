<template>
    <div class="container mx-auto p-4">
        <!-- Banner -->
        <div class="mb-4">
            <img
                src="/images/banners/banner1.png"
                alt="Promotional Banner"
                class="banner-image"
            />
        </div>

        <!-- Dropdown Filter -->
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Product Lists</h1>
            <select
                v-model="selectedFilter"
                @change="filterProducts"
                class="border rounded p-2"
            >
                <option value="all">All Products</option>
                <option value="lowPrice">Lower Price</option>
                <option value="highPrice">Higher Price</option>
                <option value="latest">Latest Products</option>
                <option value="mostPurchased">Most Purchased</option>
            </select>
        </div>

        <div
            class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
        >
            <div
                v-for="(item, index) in displayedProducts"
                :key="index"
                class="bg-white border rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer"
                @click="openCanvas(item.id)"
            >
                <img
                    :src="item.image"
                    alt="Product Image"
                    class="w-full h-48 object-cover rounded-t-md"
                />
                <div class="p-4">
                    <h2 class="text-lg font-semibold title">
                        {{ item.title }}
                    </h2>
                    <div class="flex justify-between mt-1">
                        <span
                            v-if="!isMobile"
                            class="text-gray-500 line-through mr-2 original-price"
                        >
                            {{ formatRupiah(item.originalPrice) }}
                        </span>
                        <span class="text-green-500 font-bold discounted-price">
                            {{ formatRupiah(item.discountedPrice) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="flex justify-center mt-4" v-if="hasMoreItems">
            <button
                @click="loadMoreItems"
                class="w-64 p-2 bg-green-600 text-white rounded-lg shadow-lg hover:bg-green-700 hover:shadow-xl transition-all duration-300 ease-in-out transform hover:scale-105"
            >
                Load More
            </button>
        </div>

        <!-- Canvas for Product Details -->
        <Canvas
            :isOpen="isCanvasOpen"
            :productId="selectedProductId"
            @close="closeCanvas"
        />
    </div>
</template>

<script>
import Canvas from "./Canvas.vue";

export default {
    name: "ShoppingCart",
    components: {
        Canvas,
    },
    data() {
        return {
            products: [],
            displayedProducts: [],
            isMobile: false,
            selectedFilter: "all",
            itemsToShow: 8,
            currentIndex: 0,
            hasMoreItems: true,
            isCanvasOpen: false,
            selectedProductId: null,
            selectedCategories: [], // Menyimpan kategori yang dipilih dari query
        };
    },
    mounted() {
        this.checkIfMobile();
        this.fetchProducts();
        this.selectedCategories = this.getCategoriesFromRoute(); // Ambil kategori dari URL
        window.addEventListener("resize", this.checkIfMobile);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.checkIfMobile);
    },
    methods: {
        checkIfMobile() {
            this.isMobile = window.innerWidth <= 640;
        },
        fetchProducts() {
            fetch("/products")
                .then((response) => response.json())
                .then((data) => {
                    this.products = data.sort((a, b) => a.id - b.id);
                    this.filterProducts(); // Filter produk setelah diambil
                })
                .catch((error) => {
                    console.error("Error fetching products:", error);
                });
        },
        formatRupiah(amount) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(amount);
        },
        loadMoreItems() {
            const nextIndex = this.currentIndex + this.itemsToShow;
            this.displayedProducts = this.products.slice(0, nextIndex);
            this.currentIndex = nextIndex;
            this.hasMoreItems = nextIndex < this.products.length;
        },
        filterProducts() {
            this.currentIndex = 0;
            let filteredProducts = [...this.products];

            // Filter berdasarkan kategori
            if (this.selectedCategories.length) {
                filteredProducts = filteredProducts.filter(
                    (product) =>
                        this.selectedCategories.includes(product.category) // Ganti 'category' dengan nama field kategori di data Anda
                );
            }

            // Filter berdasarkan dropdown
            if (this.selectedFilter === "lowPrice") {
                filteredProducts.sort(
                    (a, b) => a.discountedPrice - b.discountedPrice
                );
            } else if (this.selectedFilter === "highPrice") {
                filteredProducts.sort(
                    (a, b) => b.discountedPrice - a.discountedPrice
                );
            } else if (this.selectedFilter === "latest") {
                filteredProducts.sort(
                    (a, b) => new Date(b.inputDate) - new Date(a.inputDate)
                );
            } else if (this.selectedFilter === "mostPurchased") {
                filteredProducts.sort(
                    (a, b) => b.transactionCount - a.transactionCount
                );
            }

            this.products = filteredProducts;
            this.loadMoreItems();
        },
        openCanvas(productId) {
            this.selectedProductId = productId;
            this.isCanvasOpen = true;
        },
        closeCanvas() {
            this.isCanvasOpen = false;
        },
        getCategoriesFromRoute() {
            // Ambil kategori dari query parameter di route
            return this.$route.query.categories
                ? this.$route.query.categories.split(",")
                : [];
        },
    },
    watch: {
        // Ketika query kategori diubah, perbarui selectedCategories dan filter produk
        "$route.query.categories": function (newCategories) {
            // Pastikan newCategories adalah string sebelum memanggil split
            if (typeof newCategories === "string") {
                this.selectedCategories = newCategories.split(",");
            } else if (Array.isArray(newCategories)) {
                // Jika newCategories adalah array, cukup assign langsung
                this.selectedCategories = newCategories;
            } else {
                // Jika tidak ada kategori yang dipilih, set ke array kosong
                this.selectedCategories = [];
            }
            this.filterProducts(); // Filter produk saat kategori diubah
        },
    },
};
</script>

<style scoped>
/* Custom style */
</style>
