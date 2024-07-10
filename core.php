<?php
session_start();

define('ROOT', dirname(__FILE__));

$_ENV = parse_ini_file('.env');
$_POST = json_decode(file_get_contents("php://input"),true);
$token = $_POST['_token'] ?? null;

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
}

function verify_token()
{
    global $token;
    if (!$token || $token !== $_SESSION['token']) {
        http_response_code(401);
        exit;
    }
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function get_data($task_id)
{
    if (!$task_id) return null;

    $saved_file = ROOT.'/data/'.$task_id.'.json';
    if (file_exists($saved_file)) {
        return json_decode(file_get_contents($saved_file));
    }
    return null;
}

function view($name, $data = [])
{
    ob_start();
    foreach ($data as $key => $value) {
        ${$key} = $value;
    }
    include(ROOT.'/views/'.$name.'.php');
    $slot = ob_get_clean();
    return $slot;
}

function abort($http_error_code)
{
    http_response_code($http_error_code);
    exit();
}


function write_log($url, $message) {
    global $token;

    file_put_contents(ROOT.'/logs/'.date('Ymd').'.log', 
            '['.date("Y-m-d H:i:s.").gettimeofday()["usec"].'] '
            .$url.' -- '
            .get_client_ip().' -- '
            .$token.' -- '
            .$_SERVER['HTTP_USER_AGENT']."\n"
            .$message
            ."\n"
        , FILE_APPEND);
}

function redirect($url) {
    header('Location: '.$url);
    die();
    exit();
}

function curl_post($endpoint, $param = []) {
    global $_ENV;

    $endpoint = strtolower($endpoint);
    $url = 'https://api.midjourneyapi.xyz/mj/v2/'.$endpoint;

    header('Content-Type: application/json; charset=utf-8');

    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($param),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-API-KEY: ".$_ENV['GOAPI_KEY']
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if (!$err) {
        write_log($url, "\tAPI_KEY: ".$_ENV['GOAPI_KEY']."\n"
            ."\tREQUEST: ".json_encode($param)."\n"
            ."\tRESPONSE: ".$response);

        return json_decode($response);
    }

    write_log($url, "cURL Error #: ".$err);
    return false;
}
