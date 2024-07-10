<?php
include_once('../core.php');

verify_token();

if (!isset($_POST['prompt'])) abort(400);

$imagine = curl_post("imagine", [
    'prompt' => $_POST['prompt'],
    'process_mode' => 'fast'
]);

if (!isset($imagine->task_id) || $imagine->status != "success") abort(500);

if ($imagine->status != 'success') {
    abort(500);
}

file_put_contents('../data/'.$imagine->task_id.'.json', json_encode([
    'prompt' => addslashes($_POST['prompt']),
    'task_id' => $imagine->task_id,
    'imagine' => [
        'task_id' => $imagine->task_id
    ],
    'upscale1' => [],
    'upscale2' => [],
    'upscale3' => [],
    'upscale4' => []
]));

echo $imagine->task_id;