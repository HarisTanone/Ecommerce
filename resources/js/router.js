import Vue from "vue";
import Router from "vue-router";
import BodyComponent1 from "./components/BodyComponent1.vue"; // Ganti dengan komponen body yang ingin ditampilkan
import ShoppingCart from "./components/ShoppingCart.vue"; // Impor ShoppingCart
import BodyComponent2 from "./components/BodyComponent2.vue"; // Komponen body lain
import CartPage from "./components/CartPage.vue"; // Komponen body lain
// Tambahkan lebih banyak komponen body jika perlu

Vue.use(Router);

export default new Router({
    mode: "history", // Menggunakan mode history untuk URL yang bersih
    routes: [
        {
            path: "/",
            component: ShoppingCart,
        },
        {
            path: "/page2",
            component: BodyComponent2,
        },
        {
            path: "/shopping-cart",
            component: ShoppingCart, // Rute untuk Shopping Cart
        },
        {
            path: "/cart-view",
            component: CartPage, // Rute untuk Shopping Cart
        },
        // Tambahkan rute lain di sini
    ],
});
