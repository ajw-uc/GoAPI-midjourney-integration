<?php 
$data->prompt = htmlentities($data->prompt);
?>
<div id="app" class="d-flex flex-column container min-vh-100">
    <?= view('navbar') ?>
    <div class="mb-auto">
        <div class="text-center" v-if="data.imagine.status == 'finished'">
            <div class="result-item d-inline-block m-2 position-relative" v-for="i in 4">
                <div class="result-image rounded shadow bg-dark bg-opacity-25" style="height:300px;width:300px" :style="{'background-image': 'url(' + data.imagine.image_url + ')'}"></div>
                <div class="position-absolute top-0 end-0 bg-dark bg-opacity-50 me-2 mt-2 rounded">
                    <button type="button" class="btn btn-sm text-light" @click="upscale(i)"><i class="fa fa-maximize"></i></button>
                </div>
            </div>
        </div>

        <div class="text-center" style="height:55vh" v-else-if="data.imagine.status == 'failed'">
            
        </div>
            
        <div class="container w-75 py-5" v-else>
            <template v-if="progress.imagine">
                <h4 class="text-center display-5 mb-3">
                    Generating image ({{ progress.imagine.percent }}%)
                </h4>
                <div class="progress" role="progressbar" style="height:30px">
                    <div class="progress-bar bg-warning" :class="{'progress-bar-striped progress-bar-animated': progress.imagine.percent < 100}" :style="{'width': progress.imagine.percent+'%'}"></div>
                </div>
            </template>
        </div>
    </div>

    <div class="sticky-bottom py-3">
        <div class="bg-dark p-4 rounded-4 text-light">
            <div class="d-flex gap-3">
                <div class="flex-grow-1">
                    <span class="text-theme fst-italic fw-semibold">Prompt:</span>
                    <?= $data->prompt ?>
                </div>
                <div>
                    <a :href="data.imagine.image_url" class="btn btn-theme d-flex align-items-center gap-2" target="_blank" download="<?= $data->prompt ?>">
                        <i class="fa fa-download"></i>
                        <span class="d-none d-lg-inline">
                            Download
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="upscaleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-xl">
                <button type="button" class="btn-close position-absolute z-3 m-0 m-xl-3 bg-light rounded-circle fs-4 end-0" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="d-grid d-xl-flex gap-3 align-items-center">
                        <div>
                            <div class="d-none d-xl-block" style="width:630px">
                                <template v-if="modal_upscale_data.image_url">
                                    <img :src="modal_upscale_data.image_url" class="w-100 rounded">
                                </template>
                                <template v-else>
                                    <div v-if="progress[upscale_action]" class="p-5">
                                        <div class="fw-bold text-center mb-3">
                                            Generating upscaled image ({{ progress[upscale_action].percent }}%)
                                        </div>
                                        <div class="progress" role="progressbar" style="height:30px">
                                            <div class="progress-bar bg-warning" :class="{'progress-bar-striped progress-bar-animated': progress[upscale_action].percent < 100}" :style="{'width': progress[upscale_action].percent+'%'}"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="d-block d-xl-none">
                                <template v-if="modal_upscale_data.image_url">
                                    <img :src="modal_upscale_data.image_url" class="d-block d-xl-none mw-100">
                                </template>
                                <template v-else>
                                    <div v-if="progress[upscale_action]" class="p-5">
                                        <div class="fw-bold text-center mb-3">
                                            Generating upscaled image ({{ progress[upscale_action].percent }}%)
                                        </div>
                                        <div class="progress" role="progressbar" style="height:30px">
                                            <div class="progress-bar bg-warning" :class="{'progress-bar-striped progress-bar-animated': progress[upscale_action].percent < 100}" :style="{'width': progress[upscale_action].percent+'%'}"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="rounded" v-if="modal_upscale_data.image_url">
                                <a :href="modal_upscale_data.image_url" class="btn btn-theme" target="_blank" :download="'upscale-'+modal_upscale+'<?= $data->prompt ?>'">
                                    <i class="fa fa-download"></i>
                                    Download
                                </a>
                            </div>
                            <div class="my-3 d-flex gap-3">
                                <div class="flex-grow-1"><?= $data->prompt ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.title = '<?= $data->prompt ?>';

const { createApp } = Vue;
const app = createApp({
    data() {
        return {
            progress: {},
            data: <?= json_encode($data) ?>,
            upscale_action: 0
        }
    },
    methods: {
        fetch_api(action = 'imagine', callback = null) {
            this.start_progress(action);
            
            axios({
                method: 'post',
                url: '/api/fetch.php',
                data: {
                    '_token': '<?= $_SESSION['token'] ?>',
                    'task_id': '<?= $data->task_id ?>',
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
                        url: '/api/upscale.php',
                        data: {
                            '_token': '<?= $_SESSION['token'] ?>',
                            'task_id': '<?= $data->task_id ?>',
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
                    console.log('belum ada');
                    this.progress[action] = {
                        percent: 0
                    };
                }
                this.progress[action].timer = setInterval(() => {
                    this.progress[action].percent += Math.floor(Math.random()*2);
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
    mounted() {
        if (this.data.imagine.status !== 'finished') {
            this.fetch_api();
        }
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
</script>