const app = Vue.createApp({
    delimiters: ['[[', ']]'],
    data() {
        return {
            loginData: { email: '', password: '', _remember_me: false },
            registerData: { username: '', email: '', password: '', password_confirmation: '', agree: false },
            resetPasswordData: { token: '', password: '', password_confirmation: '' },
            forgotPasswordData: { email: '' },
            csrfToken: '',
            modalFormMessage: '',
            modalVisible: false
        };
    },
    methods: {
        async sendPostRequest(form, data = {}) {
            if (!form.action) {
                return false;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (response.ok) {
                    if (result.reload) {
                        window.location.reload();
                    } else if (result.redirect) {
                        window.location.href = result.redirect;
                    } else if (result.status !== 200) {
                        if (result.errors) {

                        } else if (result.violations) {
                            this.showModal(result.violations[0].title);
                        }
                    }
                    else {
                        this.showModal(result.message);
                    }
                } else {
                    this.showModal(result.error);
                }

                return result;
            } catch (error) {
                this.showModal('Something went wrong. Please try again later.');
                console.error('Ошибка:', error);
                return false;
            }
        },
        async handleLogin(e) {
            await this.sendPostRequest(e.srcElement, this.loginData);
        },
        async handleRegister(e) {
            await this.sendPostRequest(e.srcElement, this.registerData);
        },
        showModal(message) {
            this.modalFormMessage = message;
            this.modalVisible = true;
        },
        closeModal() {
            this.modalVisible = false;
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app');
});