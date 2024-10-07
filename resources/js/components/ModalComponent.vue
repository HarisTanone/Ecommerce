<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
        @click="$emit('close-modal')"
    >
        <div
            class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4"
            @click.stop
        >
            <h2 class="text-2xl font-bold mb-6 text-center">
                {{ isRegister ? "Create Account" : "Sign In" }}
            </h2>

            <!-- Form Login/Register -->
            <form @submit.prevent="handleSubmit">
                <div v-if="isRegister">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium mb-2"
                            >Name:</label
                        >
                        <input
                            type="text"
                            id="name"
                            v-model="formData.name"
                            required
                            class="border border-gray-300 p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-green-300"
                        />
                        <span v-if="errors.name" class="text-red-500 text-sm">{{
                            errors.name[0]
                        }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2"
                        >Email:</label
                    >
                    <input
                        type="email"
                        id="email"
                        v-model="formData.email"
                        required
                        class="border border-gray-300 p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-green-300"
                    />
                    <span v-if="errors.email" class="text-red-500 text-sm">{{
                        errors.email[0]
                    }}</span>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2"
                        >Password:</label
                    >
                    <input
                        type="password"
                        id="password"
                        v-model="formData.password"
                        required
                        class="border border-gray-300 p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-green-300"
                    />
                    <span v-if="errors.password" class="text-red-500 text-sm">{{
                        errors.password[0]
                    }}</span>
                </div>

                <div v-if="isRegister" class="mb-4">
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium mb-2"
                        >Confirm Password:</label
                    >
                    <input
                        type="password"
                        id="password_confirmation"
                        v-model="formData.password_confirmation"
                        required
                        class="border border-gray-300 p-3 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-green-300"
                    />
                </div>

                <button
                    type="submit"
                    class="bg-green-500 text-white py-3 rounded-md hover:bg-green-600 transition duration-200 w-full"
                >
                    {{ isRegister ? "Sign Up" : "Login" }}
                </button>
            </form>

            <!-- Social Login Buttons -->
            <div class="flex flex-col mt-6 space-y-2">
                <button
                    @click="loginWithGoogle"
                    class="bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition duration-200 flex items-center justify-center"
                >
                    Sign in with Google
                </button>
            </div>

            <div class="text-center mt-4">
                <p class="text-sm">
                    {{
                        isRegister
                            ? "Already have an account?"
                            : "Don't have an account?"
                    }}
                    <button
                        class="text-green-500 hover:text-green-600 font-medium"
                        @click="toggleModalType"
                    >
                        {{ isRegister ? "Login" : "Sign up" }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        },
        isRegister: Boolean,
    },
    data() {
        return {
            formData: {
                email: "",
                password: "",
                name: "",
                password_confirmation: "",
            },
            errors: {},
        };
    },
    methods: {
        async handleSubmit() {
            this.errors = {};
            try {
                if (this.isRegister) {
                    // Implementasi register
                    await axios.post("/register", this.formData);
                } else {
                    // Login
                    await axios.post("/login", this.formData);
                }
                // Jika berhasil, redirect ke dashboard
                window.location.href = "/";
            } catch (error) {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.errors
                ) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error("An error occurred:", error);
                }
            }
        },
        async loginWithGoogle() {
            try {
                const response = await axios.get("/auth/google");
                // Redirect ke URL yang diberikan oleh server untuk memulai proses OAuth
                window.location.href = response.data.redirect_url;
            } catch (error) {
                console.error("An error occurred during Google login:", error);
            }
        },
        toggleModalType() {
            this.$emit("toggle-modal-type");
        },
    },
};
</script>

<style scoped>
.error {
    color: red;
    font-size: 0.875rem; /* 14px */
}
</style>
