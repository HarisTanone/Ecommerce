<template>
    <div>
        <!-- Sidebar -->
        <aside
            v-if="!(isCartView && !isMobile)"
            :class="{
                '-translate-x-full': !isSidebarOpen,
                'translate-x-0': isSidebarOpen,
            }"
            class="fixed top-0 left-0 bg-white shadow-lg w-64 h-full z-30 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:block"
            :aria-hidden="!isSidebarOpen"
        >
            <!-- Account section (hidden on larger screens) -->
            <div class="p-4 lg:hidden">
                <h2 class="text-xl font-bold mb-4">Account</h2>
                <AuthButtons
                    :userName="userName"
                    :isLoggedIn="isLoggedIn"
                    @register-click="onRegisterClick"
                    @login-click="onLoginClick"
                    @logout-click="logoutUser"
                />
            </div>

            <!-- Kategori -->
            <div class="p-4" v-if="!isCartView">
                <h2 class="text-xl font-bold mb-4">Categories</h2>
                <ul>
                    <li
                        v-for="(category, index) in categories"
                        :key="index"
                        class="p-2"
                    >
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                :value="category.name"
                                v-model="selectedCategories"
                                @change="updateCategoryFilter"
                                class="form-checkbox h-4 w-4 text-green-500 transition duration-150 ease-in-out"
                            />
                            <span class="ml-2">{{ category.name }}</span>
                        </label>
                    </li>
                </ul>
            </div>

            <!-- Flash Sale Section -->
            <div class="p-4" v-if="!isCartView">
                <h2 class="text-xl font-bold mb-4">Flash Sale</h2>
                <button
                    @click="goToFlashSale"
                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-200 w-full"
                >
                    Check Flash Sale
                </button>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div
            v-if="isSidebarOpen && isMobile"
            @click="toggleSidebar"
            class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
            role="button"
            aria-label="Close sidebar"
        ></div>

        <!-- Modal Component -->
        <ModalComponent
            :isOpen="isModalOpen"
            :isRegister="isRegister"
            @close-modal="closeModal"
            @toggle-modal-type="toggleModalType"
        />
    </div>
</template>
<script>
import AuthButtons from "./AuthButtons.vue"; // Import komponen AuthButtons
import ModalComponent from "./ModalComponent.vue"; // Import ModalComponent
import axios from "axios";
import { UserMixin } from "../UserMixin"; // Import UserMixin

export default {
    props: ["isSidebarOpen", "categories"],
    components: {
        AuthButtons,
        ModalComponent,
    },
    mixins: [UserMixin], // Gunakan UserMixin
    data() {
        return {
            isModalOpen: false, // Status modal (terbuka/tertutup)
            isRegister: false, // Tipe modal (login/register)
            selectedCategories: this.getCategoriesFromRoute(), // Ambil kategori dari route
            windowWidth: window.innerWidth, // Menyimpan lebar jendela
        };
    },
    computed: {
        isMobile() {
            return this.windowWidth < 1024; // Ganti dengan breakpoint sesuai kebutuhan
        },
        isCartView() {
            return this.$route.path === "/cart-view"; // Cek apakah saat ini di cart-view
        },
    },
    mounted() {
        this.getUserData(); // Ambil data user saat komponen dimuat
        window.addEventListener("resize", this.handleResize); // Tambahkan listener resize
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.handleResize); // Hapus listener saat komponen dihancurkan
    },
    methods: {
        handleResize() {
            this.windowWidth = window.innerWidth; // Update lebar jendela
        },
        toggleSidebar() {
            this.$emit("close-sidebar");
        },
        goToFlashSale() {
            this.$router.push("/flash-sale");
            this.toggleSidebar();
        },
        openModal(isRegister) {
            this.isRegister = isRegister;
            this.isModalOpen = true;
        },
        onRegisterClick() {
            this.$emit("close-sidebar");
            this.openModal(true);
        },
        onLoginClick() {
            this.$emit("close-sidebar");
            this.openModal(false);
        },
        closeModal() {
            this.isModalOpen = false;
        },
        toggleModalType() {
            this.isRegister = !this.isRegister;
        },
        logoutUser() {
            this.isLoggedIn = false; // Mengubah status login di komponen induk
            this.userName = "";
            this.onLogoutClick(); // Memanggil metode logout dari UserMixin
            if (this.$route.path !== "/") {
                this.$router.push("/");
            }
        },
        // Update kategori yang dipilih dan emit ke ShoppingCart
        updateCategoryFilter() {
            const currentCategories = this.$route.query.categories || [];
            const newCategories = [
                ...new Set([...currentCategories, ...this.selectedCategories]),
            ];

            // Hanya lakukan navigasi jika ada perubahan
            if (
                JSON.stringify(currentCategories) !==
                JSON.stringify(newCategories)
            ) {
                this.$router
                    .push({ query: { categories: newCategories } })
                    .catch((err) => {
                        if (err.name !== "NavigationDuplicated") {
                            console.error(err);
                        }
                    });
            }
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
            this.updateCategoryFilter(); // Emit kategori yang terpilih
        },
    },
};
</script>
