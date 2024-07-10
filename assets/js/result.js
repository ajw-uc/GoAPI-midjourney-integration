
const app = createApp({
    data() {
        return {
            progress: {},
            data: {},
            upscale_action: 0
        }
    },
    methods: {
        fetch_api(action = 'imagine', callback = null) {
            this.start_progress(action);
            
            axios({
                method: 'post',
                url: base_url + 'api/fetch.php',
                data: {
                    '_token': _token,
                    'task_id': this.data.task_id,
                    'action': action
                }
            })
            .then((response) => {
                if (response.data.status == 'finished' || response.data.status == 'failed') {
                    this.stop_progress(action, true);
                    setTimeout(() => {
                        this.data[action] = response.data;
                        if (typeof callback == 'function') {
                            callback();
                        }
                    }, 1000);
                } else {
                    setTimeout(() => {
                        this.fetch_api(action);
                    }, 2000);
                }
            })
            .catch((error) => {
                alert('Failed fetching image');
                console.error(error.message);
                this.stop_progress(action);
            });
        },
        upscale(index) {
            const action = 'upscale'+index;
            this.upscale_action = action;
            console.log(this.data[action]);
            if (this.data[action]) {
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('upscaleModal'));
                modal.show();

                if (!this.data[action].image_url) {
                    axios({
                        method: 'post',
                        url: base_url + 'api/upscale.php',
                        data: {
                            '_token': _token,
                            'task_id': this.data.task_id,
                            'index': index
                        }
                    })
                    .then((response) => {
                        this.fetch_api(action, () => {
                            this.modal_upscale.data = this.data[action]
                        });
                    })
                    .catch(function (error) {
                        alert('Upscale image failed');
                        console.error(error.message);
                        this.stop_progress(action);
                    });
                }
            }

        },
        start_progress(action) {
            if (this.data[action]) {
                this.stop_progress(action);
                if (!(action in this.progress)) {
                    this.progress[action] = {
                        percent: 0
                    };
                }
                this.progress[action].timer = setInterval(() => {
                    this.progress[action].percent += Math.floor(Math.random()*4);
                    if (this.progress[action].percent > 99) {
                        this.progress[action].percent = 99;
                        this.stop_progress(action);
                    }
                }, 500);
            }
        },
        stop_progress(action, finish = false) {
            if (this.progress[action]) {
                clearInterval(this.progress[action].timer);
                if (finish) {
                    this.progress[action].percent = 100;
                }
            }
        },
    },
    computed: {
        modal_upscale_data() {
            if (this.data[this.upscale_action]) {
                return this.data[this.upscale_action];
            }
            return {};
        }
    }
}).mount('#app')