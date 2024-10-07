<template>
    <header class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-10">
        <div class="container mx-auto flex justify-between items-center p-4">
            <!-- Logo untuk desktop, hilangkan di mobile -->
            <a class="flex items-center lg:block hidden" href="/">
                <img
                    src="https://www.hvdig.co.uk/wp-content/uploads/2023/08/logo-laravel.svg"
                    alt="Logo"
                    class="h-10"
                />
            </a>

            <!-- Icon Menu untuk Mobile -->
            <div class="flex items-center lg:hidden">
                <button
                    @click="$emit('toggle-sidebar')"
                    class="focus:outline-none"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="h-8 w-8 text-gray-700 dark:text-gray-300"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        />
                    </svg>
                </button>
            </div>

            <!-- Pencarian -->
            <div class="flex-grow mx-4">
                <input
                    type="text"
                    placeholder="Search products..."
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-300 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Icon Cart dengan jumlah item -->
            <div class="flex items-center lg:mx-4 sm:mx-0 relative">
                <button @click="onCartClick" class="focus:outline-none">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        class="h-8 w-8 text-gray-700 dark:text-gray-300"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 10V7a5 5 0 0110 0v3m1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9m4 0h10"
                        />
                    </svg>
                    <span
                        class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1"
                    >
                        {{ cartItemCount }}
                    </span>
                </button>
            </div>

            <!-- Tombol Join dan Login untuk Desktop -->
            <div class="hidden lg:flex items-center space-x-2">
                <AuthButtons
                    :isLoggedIn="isLoggedIn"
                    :userName="userName"
                    @login-click="onLoginClick"
                    @register-click="onRegisterClick"
                    @logout-click="onLogoutClick"
                />
            </div>
        </div>

        <!-- ModalComponent -->
        <ModalComponent
            :isOpen="isModalOpen"
            :isRegister="isRegister"
            @close-modal="closeModal"
            @toggle-modal-type="toggleModalType"
        />
    </header>
</template>

<script>
import AuthButtons from "./AuthButtons.vue";
import ModalComponent from "./ModalComponent.vue";
import { UserMixin } from "../UserMixin"; // Import mixin

export default {
    name: "HeaderComponent",
    components: {
        AuthButtons,
        ModalComponent,
    },
    mixins: [UserMixin], // Tambahkan mixin di sini
    data() {
        return {
            cartItemCount: 0, // New property to track the total items in the cart
        };
    },

    props: {
        isModalOpen: {
            type: Boolean,
            default: false,
        },
        isRegister: {
            type: Boolean,
            default: false,
        },
    },
    created() {
        this.fetchCartData();
    },
    methods: {
        async fetchCartData() {
            try {
                const response = await fetch("/cart"); // Ambil data keranjang dari API
                const data = await response.json();

                // Hitung total jumlah item dalam keranjang
                this.cartItemCount = data.items.reduce(
                    (total, item) => total + item.quantity,
                    0
                );

                // Simpan jumlah item ke localStorage
                localStorage.setItem("cartItemCount", this.cartItemCount);
            } catch (error) {
                console.error("Error fetching cart data:", error);
            }
        },

        // Call this method to update the cart item count after adding a product to the cart
        updateCartCount() {
            this.fetchCartData();
        },
        closeModal() {
            this.$emit("close-modal"); // Emit event untuk menutup modal
        },
        toggleModalType() {
            this.$emit("toggle-modal-type"); // Emit event untuk toggle tipe modal
        },
        onRegisterClick() {
            this.$emit("register-click"); // Emit event untuk register
        },
        onLoginClick() {
            this.$emit("login-click"); // Emit event untuk login
        },
        // Method untuk handle klik pada Cart
        onCartClick() {
            if (this.isLoggedIn) {
                window.location.href = "/cart-view";
            } else {
                this.$emit("login-click");
            }
        },
    },
};
</script>

<style scoped>
@media (prefers-color-scheme: dark) {
    .dark\:bg-gray-800 {
        background-color: #1f2937;
    }
    .dark\:text-gray-300 {
        color: #d1d5db;
    }
    .dark\:bg-gray-700 {
        background-color: #374151;
    }
    .dark\:hover\:bg-gray-600 {
        background-color: #4b5563;
    }
}
</style>
