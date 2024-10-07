// resources/js/UserMixin.js

import axios from 'axios';

export const UserMixin = {
    data() {
        return {
            isLoggedIn: false,
            userName: '',
        };
    },
    mounted() {
        this.getUserData();
    },
    methods: {
        getUserData() {
            axios
                .get('/auth')
                .then((response) => {
                    if (Object.keys(response.data).length > 0) {
                        this.isLoggedIn = true;
                        this.userName = response.data.name; // Ambil nama user dari response
                    } else {
                        this.isLoggedIn = false;
                        this.userName = ''; // Reset nama user
                    }
                })
                .catch((error) => {
                    console.error('Error fetching user data:', error);
                    this.isLoggedIn = false;
                    this.userName = ''; // Reset nama user jika terjadi error
                });
        },
        onLogoutClick() {
            this.isLoggedIn = false; // Mengubah status login di komponen induk
            this.userName = '';
            if (this.$route.path !== '/') {
                this.$router.push('/');
            }
        },
    },
};
