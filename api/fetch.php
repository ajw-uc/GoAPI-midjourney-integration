<?php
include_once('../core.php');

verify_token();

if (!isset($_POST['task_id'])) abort(400);
$action = $_POST['action'] ?? 'imagine';

$data = get_data($_POST['task_id']);
if (!$data) abort(404);
if (!isset($data->{$action})) abort(400);

$task_id = $data->{$action}->task_id;
$fetch = curl_post("fetch", [
    'task_id' => $task_id
]);

if ($fetch->task_id) {
    $result = [
        'task_id' => $task_id,
        'status' => $fetch->status,
        'process_time' => $fetch->process_time,
        'discord_url' => $fetch->task_result->discord_url ?? null,
        'image_url' => $fetch->task_result->image_url,
        'image_id' => $fetch->task_result->image_id,
        'result_message_id' => $fetch->task_result->result_message_id,
        'created_at' => $fetch->meta->created_at,
        'started_at' => $fetch->meta->started_at,
        'process_mode' => $fetch->meta->process_mode,
        'task_input_prompt' => $fetch->meta->task_input->prompt ?? null
    ];

    $data->{$action} = $result;
    file_put_contents('../data/'.$data->task_id.'.json', json_encode($data));

    echo json_encode($result);
}