<template>
    <div>
        <!-- Header -->
        <HeaderComponent
            @toggle-sidebar="toggleSidebar"
            @register-click="openModal(true)"
            @login-click="openModal(false)"
        />

        <div class="flex">
            <!-- Sidebar -->
            <SidebarComponent
                :isSidebarOpen="isSidebarOpen"
                :categories="categories"
                @close-sidebar="toggleSidebar"
                @register-click="openModal(true)"
                @login-click="openModal(false)"
            />

            <!-- Konten utama -->
            <div class="flex-grow lg:p-8 sm:p-1">
                <router-view @login-click="openModal(false)" />
                <!-- Konten body -->
            </div>
        </div>

        <!-- Footer -->
        <FooterComponent />

        <!-- ModalComponent -->
        <ModalComponent
            :isOpen="isModalOpen"
            :isRegister="isRegister"
            @close-modal="closeModal"
            @toggle-modal-type="toggleModalType"
        />
    </div>
</template>

<script>
import HeaderComponent from "./Header.vue";
import FooterComponent from "./Footer.vue";
import SidebarComponent from "./Sidebar.vue";
import ModalComponent from "./ModalComponent.vue"; // Import ModalComponent

export default {
    data() {
        return {
            isSidebarOpen: false,
            isModalOpen: false,
            isRegister: false,
            categories: [], // Ambil data dari categories.json
        };
    },
    mounted() {
        this.fetchCategories();
    },
    methods: {
        toggleSidebar() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        openModal(isRegister) {
            console.log("Opening modal:", isRegister);
            this.isRegister = isRegister;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        toggleModalType() {
            this.isRegister = !this.isRegister;
        },
        // Fetch categories from categories.json
        fetchCategories() {
            fetch("/data/categories.json")
                .then((response) => response.json())
                .then((data) => {
                    this.categories = data;
                    console.log("Categories fetched:", this.categories);
                });
        },
    },
    components: {
        HeaderComponent,
        FooterComponent,
        SidebarComponent,
        ModalComponent, // Tambahkan ModalComponent di sini
    },
};
</script>

<style scoped>
/* Tambahkan styling jika diperlukan */
</style>
