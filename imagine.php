<?php
include_once('config.php');
verify_csrf();

if (isset($_POST['prompt'])) {
    $result = curl_post("imagine", [
        'prompt' => $_POST['prompt'],
        'process_mode' => 'fast'
    ]);

    echo json_encode($result);
    exit;
}

http_response_code(400);