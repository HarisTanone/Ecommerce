<template>
    <transition name="slide">
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-50"
            @click="closeCanvas"
        >
            <div
                class="bg-white fixed z-60 overflow-y-auto"
                :class="{
                    'right-0 top-0 bottom-0 w-1/3 shadow-lg': !isMobile,
                    'left-0 right-0 bottom-0 rounded-t-3xl': isMobile,
                }"
                @click.stop
            >
                <!-- Content -->
                <div class="p-4">
                    <!-- Product details -->
                    <img
                        :src="product.image"
                        alt="Product Image"
                        class="w-full object-cover rounded-md mb-4"
                    />
                    <h2 class="text-xl font-bold mb-2">{{ product.title }}</h2>

                    <p class="mb-2">
                        Price:
                        <b class="text-green-500 font-bold discounted-price">{{
                            formatRupiah(product.discountedPrice)
                        }}</b>
                    </p>
                    <p class="text-gray-500 line-through mb-2">
                        Original Price:
                        {{ formatRupiah(product.originalPrice) }}
                    </p>
                    <p class="mb-4">{{ product.description }}</p>
                    <button
                        @click="addToCart(product)"
                        class="bg-green-600 text-white py-2 px-4 rounded-lg w-full"
                    >
                        Add to Cart
                    </button>
                </div>

                <!-- Most purchased products for desktop view -->
                <div v-if="!isMobile" class="p-4">
                    <h3 class="text-lg font-bold mb-2">
                        Most Purchased Products
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div
                            v-for="item in mostPurchasedProducts"
                            :key="item.id"
                            class="flex items-center space-x-4 cursor-pointer"
                            @click="showProduct(item.id)"
                        >
                            <img
                                :src="item.image"
                                alt="Product Image"
                                class="w-16 h-16 object-cover rounded-md"
                            />
                            <div>
                                <p class="font-semibold">{{ item.title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ formatRupiah(item.discountedPrice) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "Canvas",
    props: {
        isOpen: Boolean,
        productId: Number,
    },
    data() {
        return {
            product: {},
            mostPurchasedProducts: [],
            isMobile: false,
        };
    },
    watch: {
        productId(newVal) {
            if (newVal) {
                this.fetchProductById(newVal);
                this.fetchMostPurchasedProducts();
            }
        },
    },
    mounted() {
        this.checkIfMobile();
        window.addEventListener("resize", this.checkIfMobile);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.checkIfMobile);
    },
    methods: {
        checkIfMobile() {
            this.isMobile = window.innerWidth <= 640;
        },
        fetchProductById(id) {
            fetch("/products")
                .then((response) => response.json())
                .then((data) => {
                    this.product = data.find((item) => item.id === id);
                });
        },
        fetchMostPurchasedProducts() {
            fetch("/products")
                .then((response) => response.json())
                .then((data) => {
                    this.mostPurchasedProducts = data
                        .sort((a, b) => b.transactionCount - a.transactionCount)
                        .slice(0, 3);
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
        closeCanvas() {
            this.$emit("close");
        },
        updateCartItem() {
            let currentCount = parseInt(localStorage.getItem("cartItemCount")) || 0;
            currentCount += 1;
            localStorage.setItem("cartItemCount", currentCount);
        },
        addToCart(product) {
            axios
                .post("/cart", {
                    product_id: product.id,
                    quantity: 1,
                })
                .then((response) => {
                    Swal.fire({
                        icon: "success",
                        title: "Product added to your cart.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    this.updateCartItem();
                })
                .catch((error) => {
                    Swal.fire({
                        icon: "error",
                        title: "Login to add product to cart.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                    setTimeout(() => {
                        this.closeCanvas();
                    }, 1000);
                });
        },

        showProduct(id) {
            this.fetchProductById(id);
        },
    },
};
</script>

<style scoped>
/* Transitions for slide animations */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter,
.slide-leave-to {
    transform: translateX(100%);
}
@media (max-width: 640px) {
    .slide-enter,
    .slide-leave-to {
        transform: translateY(100%);
    }
}
</style>
