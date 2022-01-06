Vue.createApp({
    data() {
        return {
            current_vue: 'login',
            form_username: '',
            form_password: '',
            form_password_1: '',
            form_password_2: '',
            form_first_name: '',
            form_last_name: '',
            form_email: '',
            is_loading: false,
            hide_form: false,
            message: '',
        }
    },
    computed: {

    },
    methods: {
        displayVue(vue_name) {
            this.hide_form = false;
            this.message = '';

            if ('login' === vue_name) {
                this.user = [];
                this.current_vue = 'login';
            } else if ('register' === vue_name) {
                this.user = [];
                this.current_vue = 'register';
            } else if ('forgot_password' === vue_name) {
                this.user = [];
                this.current_vue = 'forgot_password';
            }
        },
        submitLogin() {

            if (this.form_username.length > 0 && this.form_password.length > 0) {
                this.is_loading = true;
                this.message = '';

                fetch('/wp-json/rest_login/login/', {
                        method: 'POST',
                        headers: {
                            'X-WP-Nonce': WordPress.nonce,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            'username': this.form_username,
                            'password': this.form_password,
                            'step': step
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.is_loading = false;
                        console.log(res);

                        if (res.message.length > 0) {
                            this.message = res.message;
                        }

                        if (200 === res.code) { /* If the login is successful */
                            this.hide_form = true;
                            window.location.href = '/';
                        }

                        console.log(res);

                    });
            } else {
                this.message = 'Champ username ou password vide';
            }

            return;

        },
        submitSubscribe() {

            if (this.form_password_1 !== this.form_password_2) {
                this.form_password_1 = '';
                this.form_password_2 = '';
                this.message = 'Les mots de passe doivent etre identiques.';
                return;
            }

            this.is_loading = true;
            this.message = '';

            fetch('/wp-json/rest_login/register/', {
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': WordPress.nonce,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'username': this.form_username,
                        'first_name': this.form_first_name,
                        'last_name': this.form_last_name,
                        'email': this.form_email,
                        'password': this.form_password_1,
                    })
                })
                .then(res => res.json())
                .then(res => {
                    this.is_loading = false;
                    console.log(res);

                    if (res.message.length > 0) {
                        this.message = res.message;
                    }

                    if (200 === res.code) { /* If the registration was successful */
                        console.log('enregistrement ok');
                        this.hide_form = true;
                        this.resetForm();
                    }
                });

        },
        submitPasswordReset() {
            this.is_loading = true;
            this.message = '';

            fetch('/wp-json/rest_login/reset_password/', {
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': WordPress.nonce,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'username': this.form_username
                    })
                })
                .then(res => res.json())
                .then(res => {
                    this.is_loading = false;
                    if (res.message.length > 0) {
                        this.message = res.message;
                    }
                    if (res.code.length > 0) {
                        if (200 === res.code) {
                            this.hide_form = true;
                        }
                    }
                    console.log(res);

                });

        },
        resetForm() {
            this.form_username = '';
            this.form_first_name = '';
            this.form_last_name = '';
            this.form_email = '';
        }
    },
    mounted() {
        if (typeof(wp_message) !== 'undefined' && wp_message !== null && wp_message.length > 0) {
            this.message = wp_message;
        }
    }
}).mount('#member-login-app');