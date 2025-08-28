const app = Vue.createApp({
    delimiters: ['[[', ']]'],
    data() {
        return {
            csrfToken: '',
            modalFormMessage: '',
            modalVisible: false,

            agents: [],     // массив агентов, которые мы показываем
            page: 1,        // текущая страница
            hasMore: true,  // есть ли еще данные (если false, кнопку скрываем)
            loading: false,  // состояние загрузки (true пока идёт запрос)

            messages: [],     // массив сообщений, которые мы показываем
            isChatBtnDisabled: false
        };
    },
    mounted() {
        if (document.getElementById('allAgents')) {
            this.loadAllAgents();
        }

        if (document.getElementById('chatMessages')) {
            this.getMessages();
        }

        const textarea = document.getElementById('messageInput');
        if (textarea) {
            textarea.addEventListener('input', this.autoResizeTextarea);
        }
    },
    methods: {
        autoResizeTextarea(e) {
            const textarea = e.target;
            textarea.style.height = 'auto'; // Сбрасываем высоту
            textarea.style.height = `${textarea.scrollHeight}px`; // Устанавливаем высоту под содержимое
        },
        autoCast(value, key) {
            if (['password', 'password_confirmation', 'username', 'email'].includes(key)) {
                return value;
            }

            if (value === 'true') return true;
            if (value === 'false') return false;
            if (value === '1') return 1;
            if (value === '0') return 0;
            if (!isNaN(value) && value.trim() !== '') return Number(value);
            return value;
        },
        normalizeData(data) {
            return Object.fromEntries(
                Object.entries(data).map(([k, v]) => [k, this.autoCast(v, k)])
            );
        },
        getAgentChatUrl(agent) {
            let template = document.getElementById('agentsPage').dataset.agentChatUrl;
            return template.replace('__SLUG__', agent.username);
        },
        getAgentEditUrl(agent) {
            let template = document.getElementById('agentsPage').dataset.agentEditUrl;
            return template.replace('__ID__', agent.id);
        },
        async getMessages() {
            if (this.loading) return;
            this.loading = true;

            try {
                let url = document.getElementById("chatMessages").dataset.agentsUrl;
                const response = await fetch(url + '?page=' + this.page);
                const data = await response.json();

                if (data.messages.length > 0) {
                    this.messages.push(...data.messages);
                    this.hasMore = data.hasMore;

                    if (this.hasMore) {
                        this.page++;
                    }
                } else {
                    this.hasMore = false;
                }
            } catch (e) {
                console.error(e);
                this.showModal('Messages loading error');
            } finally {
                this.loading = false;
            }
        },
        async loadAllAgents() {
            if (this.loading) return;
            this.loading = true;

            try {
                let url = document.getElementById("agentsPage").dataset.agentsUrl;
                const response = await fetch(url + '?page=' + this.page);
                const data = await response.json();

                if (data.agents.length > 0) {
                    this.agents.push(...data.agents);
                    this.hasMore = data.hasMore;

                    if (this.hasMore) {
                        this.page++;
                    }
                } else {
                    this.hasMore = false;
                }
            } catch (e) {
                console.error(e);
                this.showModal('Agent loading error');
            } finally {
                this.loading = false;
            }
        },
        async sendPostRequest(form, target) {
            if (!form.action) {
                return false;
            }

            const formData = new FormData(target);
            const data = Object.fromEntries(formData.entries());
            const normalized = this.normalizeData(data);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(normalized)
                });
                const result = await response.json();

                if (response.ok) {
                    if (result.status !== 200) {
                        if (result.error) {
                            this.showModal(result.error);
                        } else if (result.errors) {
                            this.showModal(result.errors[0].message);
                        }
                    }

                    if (result.message) {
                        this.showModal(result.message);
                    }

                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                } else {
                    let mes = '';
                    if (result.violations) {
                        result.violations.forEach(item => {
                            mes += item.title + '; ';
                        });
                        this.showModal(mes);
                    } else if (result.error) {
                        this.showModal(result.error);
                    } else {
                        this.showModal('Something went wrong. Please try again later. (1)');
                    }
                }

                return result;
            } catch (error) {
                this.showModal('Something went wrong. Please try again later. (2)');
                console.error('Ошибка:', error);
                return false;
            }
        },
        async sendMessage(form, target) {
            if (!form.action) {
                return false;
            }

            this.isChatBtnDisabled = true;
            const formData = new FormData(target);
            const data = Object.fromEntries(formData.entries());
            const normalized = this.normalizeData(data);

            this.messages.push({
                'role': 'user',
                'content': data.text,
                'createdAt': this.formatDateTime(new Date())
            });
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(normalized)
                });
                const result = await response.json();

                if (response.ok) {
                    if (result.status == 200) {
                        this.messages.push(result.assistant);
                        form.reset();
                    } else {
                        this.showModal(result.error);
                    }
                } else {
                    this.showModal('Something went wrong. Please try again later. (3)');
                }

                this.isChatBtnDisabled = false;
                return result;
            } catch (error) {
                this.isChatBtnDisabled = false;
                this.showModal('Something went wrong. Please try again later. (4)');
                console.error('Ошибка:', error);
                return false;
            }
        },


        async handleCreateChat(e) {
            await this.sendPostRequest(e.srcElement, e.target);
        },
        async handleSendMessage(e) {
            await this.sendMessage(e.srcElement, e.target);
        },
        async handleCreateAgent(e) {
            await this.sendPostRequest(e.srcElement, e.target);
        },
        async handleUpdateAgent(e) {
            await this.sendPostRequest(e.srcElement, e.target);
        },
        async handleLogin(e) {
            await this.sendPostRequest(e.srcElement, e.target);
        },
        async handleRegister(e) {
            await this.sendPostRequest(e.srcElement, e.target);
        },
        showModal(message) {
            this.modalFormMessage = message;
            this.modalVisible = true;
        },
        closeModal() {
            this.modalVisible = false;
        },
        formatDateTime(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app');
});