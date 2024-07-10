<?php
include_once('../core.php');

if (!isset($_POST['index']) && !isset($_POST['task_id'])) abort(400);

$action = 'upscale'.$_POST['index'];

$data = get_data($_POST['task_id']);
if (!$data) abort(404);
if (!isset($data->{$action})) abort(400);

$upscale = curl_post("upscale", [
    'origin_task_id' => $data->imagine->task_id,
    'index' => (string) $_POST['index']
]);

if (!isset($upscale->status) || $upscale->status != 'success') {
    if ($upscale->message == 'repeat task') {
        $upscale->task_id = $upscale->detail->exist_task_id;       
    } else {
        abort(500);
    }
}

$data->{$action} = [
    'task_id' => $upscale->task_id
];

file_put_contents('../data/'.$data->task_id.'.json', json_encode($data));

echo json_encode($data->{$action});