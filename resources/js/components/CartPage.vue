<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Cart Page</h1>

        <div v-if="cart.items.length === 0" class="text-center mt-8">
            <p class="text-gray-500">Your cart is empty.</p>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="item in cart.items"
                    :key="item.id"
                    class="bg-white border border-gray-300 rounded-lg p-4 shadow-lg flex flex-col transition-transform duration-300 hover:shadow-xl"
                    :class="{
                        'border-green-500 border-4': selectedItems.includes(
                            item.id
                        ),
                    }"
                    @click="toggleSelection(item.id)"
                >
                    <img
                        :src="item.product.image"
                        alt="product image"
                        class="rounded-lg mb-2 h-48 object-cover"
                    />
                    <h2 class="font-semibold text-lg">
                        {{ item.product.title }}
                    </h2>
                    <p class="text-gray-600">
                        Category: {{ item.product.category }}
                    </p>
                    <p class="text-gray-800 mt-2">
                        Amount: {{ item.quantity }}
                    </p>
                    <p class="text-red-500 mt-2">
                        <span class="line-through text-gray-500">
                            {{ formatRupiah(item.product.originalPrice) }}
                        </span>
                        {{ formatRupiah(item.product.discountedPrice) }}
                    </p>
                    <p class="text-gray-700 mt-2">
                        {{ item.product.description }}
                    </p>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <h2 class="font-semibold text-xl">Total Price:</h2>
                <b class="text-lg text-green-600 font-bold">
                    {{ formatRupiah(totalPrice) }}
                </b>
            </div>
            <div class="mt-8 flex justify-end">
                <button
                    @click="checkout"
                    class="bg-green-500 text-white py-2 w-40 px-4 rounded shadow-lg hover:bg-green-700 transition duration-300"
                >
                    Checkout
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
    name: "CartPage",
    data() {
        return {
            cart: { items: [] },
            selectedItems: [],
            stripe: null,
            cartId: null,
        };
    },
    async created() {
        console.log("CartPage created");

        await this.fetchCartData();

        const successMessage = document
            .querySelector('meta[name="flash-message-success"]')
            ?.getAttribute("content");

        if (successMessage) {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: successMessage,
                confirmButtonText: "OK",
            });
        }
    },
    computed: {
        totalPrice() {
            return this.cart.items
                .filter((item) => this.selectedItems.includes(item.id))
                .reduce(
                    (total, item) =>
                        total + item.product.discountedPrice * item.quantity,
                    0
                );
        },
    },
    methods: {
        async fetchCartData() {
            try {
                const response = await fetch("/cart");
                const data = await response.json();
                this.cart = data;
                this.cartId = data.id;
            } catch (error) {
                console.error("Error fetching cart data:", error);
            }
        },
        toggleSelection(itemId) {
            if (this.selectedItems.includes(itemId)) {
                this.selectedItems = this.selectedItems.filter(
                    (id) => id !== itemId
                );
            } else {
                this.selectedItems.push(itemId);
            }
        },
        async checkout() {
            if (this.selectedItems.length === 0) {
                alert("Please select the products you want to checkout.");
                return;
            }

            try {
                console.log({
                    cart_id: this.cartId,
                    selectedItems: this.selectedItems,
                    totalPrice: this.totalPrice,
                });
                const response = await fetch("/checkout", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({
                        cart_id: this.cartId,
                        selectedItems: this.selectedItems,
                        totalPrice: this.totalPrice,
                    }),
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = data.url;
                } else {
                    throw new Error(
                        data.error || "An error occurred during checkout."
                    );
                }
            } catch (error) {
                console.error("Error during checkout:", error);
                alert(error.message);
            }
        },
        formatRupiah(number) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
            }).format(number);
        },
    },
};
</script>
