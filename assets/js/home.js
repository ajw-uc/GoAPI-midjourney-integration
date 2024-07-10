createApp({
    data() {
        return {
            prompt: '',
            imagining: false
        }
    },
    methods: {
        api_imagine() {
            this.imagining = true;
            axios({
                method: 'post',
                url: base_url + 'api/imagine.php',
                data: {
                    '_token': _token,
                    'prompt': this.prompt
                }
            })
            .then((response) => {
                window.location = base_url + 'result/' + response.data;  
            })
            .catch(function (error) {
                alert('Failed imagining image');
                console.error(error.message);
                this.processing = false;
            });
        }
    },
}).mount('#app')