<?php
include_once('config.php');
$csrf = create_csrf();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100" style="padding-bottom: 100px">
        <div class="py-3 text-center">
            <div id="loading" class="mb-5">
                <div class="spinner-border fs-1 mb-2" style="height:2em;width:2em" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div id="loading-info" class="fs-4"></div>
            </div>
            <div id="result">
                <h1 class="display-5" id="prompt-text"></h1>
                <div class="mb-3">It took <span id="process-time"></span> seconds to generate this image</div>
                <img src="" id="img-result" class="mw-100" alt="Generated image">
            </div>
        </div>
    </div>
    <div class="position-fixed w-100 p-3 bg-light border-top bottom-0 d-flex align-items-center" style="height:100px">
        <form onsubmit="api_imagine(event)" class="w-100">
            <div class="input-group">
                <input type="text" autofocus class="form-control form-control-lg" id="txt-prompt" placeholder="Please describe the image you'd like to see here">
                <button type="submit" class="btn btn-primary px-5" id="btn-imagine">Imagine</button>
            </div>
        </form>
    </div>
</body>

<script>
const promptEl = document.getElementById('txt-prompt');
const imagineEl = document.getElementById('btn-imagine');
const loadingEl = document.getElementById('loading');
const resultEl = document.getElementById('result');
loadingEl.hidden = true;
resultEl.hidden = true;

function reset_form() {
    promptEl.disabled = false;
    imagineEl.disabled = false;
    loadingEl.hidden = true;
}

function api_imagine(event) {
    event.preventDefault();
    promptEl.disabled = true;
    imagineEl.disabled = true;
    loadingEl.hidden = false;
    resultEl.hidden = true;

    document.getElementById('loading-info').innerHTML = 'Generating image...';

    axios({
        method: 'post',
        url: 'imagine.php',
        data: {
            '_token': '<?= $csrf ?>',
            'prompt': promptEl.value
        }
    })
    .then(function (response) {
        if (response.data.status == "success"){
            api_fetch(response.data.task_id);
        } else {
            alert(response.data.message);
        }
    })
    .catch(function (error) {
        alert(error.message);
    })
    .finally(function() {
        reset_form();
    });
}

function api_fetch(task_id) {
    promptEl.disabled = true;
    imagineEl.disabled = true;
    loadingEl.hidden = false;
    resultEl.hidden = true;

    document.getElementById('loading-info').innerHTML = 'Fetching image...';

    axios({
        method: 'post',
        url: 'fetch.php',
        data: {
            '_token': '<?= $csrf ?>',
            'task_id': task_id
        }
    })
    .then(function (response) {
        if (response.data.status == "finished") {
            // proses selesai
            document.getElementById('prompt-text').innerHTML = response.data.prompt;
            document.getElementById('img-result').src = response.data.task_result.image_url;
            document.getElementById('process-time').innerHTML = response.data.process_time;
            resultEl.hidden = false;
        } else if (response.data.status == "failed") {
            // proses gagal
            alert(response.data.task_result.message);
        } else {
            // proses belum selesai, ulangi fetch
            api_fetch(task_id);
        }
    })
    .catch(function (error) {
        alert(error.message);
    })
    .finally(function() {
        reset_form();
    });
}
</script>
</html>