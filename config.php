<?php
session_start();
$_POST = json_decode(file_get_contents("php://input"),true);

function create_csrf() {
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    return $_SESSION['token'];
}

function verify_csrf() {
    $token = $_POST['_token'] ?? null;

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

function curl_post($endpoint, $param = []) {
    $env = parse_ini_file('.env');

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
            "X-API-KEY: ".$env['GOAPI_KEY']
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
        exit;
    } else {
        file_put_contents('logs/'.$endpoint.'.log', 
            '['.date("Y-m-d H:i:s.").gettimeofday()["usec"].'] '
            .get_client_ip().' -- '
            .$_SERVER['HTTP_USER_AGENT']."\n"
            ."\tAPI_KEY: ".$env['GOAPI_KEY']."\n"
            ."\tREQUEST: ".json_encode($param)."\n"
            ."\tRESPONSE: ".$response."\n"
        , FILE_APPEND);

        return json_decode($response);
    }
}

$env = parse_ini_file('.env');