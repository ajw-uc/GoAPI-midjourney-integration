<div id="app" class="d-flex flex-column min-vh-100 container">
    <?= view('navbar') ?>
    <div class="mb-auto">
        <div class="d-grid d-lg-flex justify-content-center gap-3 align-items-center">
            <div class="mb-3">
                <h1 class="display-1 text-center">
                    Image Generative <span class="fw-bold" style="color:orange">AI</span>
                </h1>
                <div class="text-center px-5 fs-5 mt-3">
                    <div class="fw-semibold fst-italic">Unleash your imagination!</div>
                    Harness the power of AI to transform your creative ideas into stunning works of art!
                </div>
            </div>
            <div class="text-center">
                <div class="d-inline-block rounded shadow" style="height:300px;width:300px" id="sample_image"></div>
            </div>
        </div>
    </div>

    <div class="sticky-bottom py-3">
        <div class="bg-dark p-4 rounded-4 w-100">
            <form @submit.prevent="api_imagine">
                <div class="d-flex gap-3">
                    <input type="text" autofocus class="bg-light bg-opacity-10 text-white border-0 form-control" id="prompt" v-model="prompt" placeholder="Please describe the image you'd like to see here" required :disabled="imagining">
                    <button type="submit" class="btn btn-theme d-flex align-items-center gap-2" :disabled="imagining">
                        <div class="spinner-border" v-if="imagining" style="height:1em; width:1em;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <i class="fa-solid fa-wand-magic-sparkles" v-else></i>

                        <span class="d-none d-md-inline">{{ imagining ? 'Imagining...' : 'Imagine' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const { createApp } = Vue;

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
                url: 'api/imagine.php',
                data: {
                    '_token': '<?= $_SESSION['token'] ?>',
                    'prompt': this.prompt
                }
            })
            .then((response) => {
                window.location = '/result/' + response.data;  
            })
            .catch(function (error) {
                alert('Failed imagining image');
                console.error(error.message);
                this.processing = false;
            });
        }
    },
}).mount('#app')
</script>
</html>