<!-- AuthButtons.vue -->
<template>
    <div>
        <div v-if="isLoggedIn" class="relative">
            <div
                class="flex items-center space-x-2 cursor-pointer"
                @click="toggleDropdown"
            >
                <!-- Icon Person -->
                <img
                    src="/images/icons/person.png"
                    alt="person icon"
                    class="h-10 rounded-full"
                />
                <!-- Nama Pengguna -->
                <span class="text-gray-600">{{ userName }}</span>
            </div>

            <!-- Dropdown Menu -->
            <div
                v-if="isDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg"
            >
                <ul class="py-1 text-gray-700">
                    <li>
                        <a
                            href="/profile"
                            class="block px-4 py-2 hover:bg-gray-100"
                            >Profile</a
                        >
                    </li>
                    <li>
                        <a
                            href="/orders"
                            class="block px-4 py-2 hover:bg-gray-100"
                            >Order</a
                        >
                    </li>
                    <li>
                        <a
                            href="#"
                            @click="logout"
                            class="block px-4 py-2 hover:bg-gray-100"
                            >Logout</a
                        >
                    </li>
                </ul>
            </div>
        </div>

        <!-- Jika tidak login, tampilkan tombol Register dan Login -->
        <div v-else class="flex space-x-2">
            <button
                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-200"
                @click="$emit('register-click')"
            >
                Register
            </button>
            <button
                class="bg-gray-300 text-black px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200"
                @click="$emit('login-click')"
            >
                Login
            </button>
        </div>
    </div>
</template>
<script>
import axios from "axios"; // Tambahkan ini untuk mengimpor axios

export default {
    name: "AuthButtons",
    props: {
        userName: {
            type: String,
            default: "", // Nama user akan dikirim dari komponen induk setelah login
        },
        isLoggedIn: {
            type: Boolean,
            default: false, // Status login akan dikirim dari komponen induk
        },
    },
    data() {
        return {
            isDropdownOpen: false, // Untuk mengelola state dropdown
        };
    },
    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },
        async logout() {
            try {
                await axios.post("/logout"); // Menggunakan axios untuk mengirim permintaan logout
                this.isDropdownOpen = false; // Tutup dropdown setelah logout
                this.$emit("logout-click"); // Emit event jika diperlukan
            } catch (error) {
                console.error("Logout failed:", error);
                // Tambahkan penanganan error jika diperlukan
            }
        },
    },
};
</script>
