<?php
include_once('config.php');
verify_csrf();

if (isset($_POST['task_id'])) {
    $result = curl_post("fetch", ['task_id' => $_POST['task_id']]);
    $prompt = $result->meta->task_request->prompt;
    if (isset($result->meta)) {
        unset($result->meta);
    }
    $result->prompt = $prompt;

    echo json_encode($result);
    exit;
}

http_response_code(400);