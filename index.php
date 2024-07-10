<?php
include('core.php');
$slot = '';
$uri_segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
$start_segment = isset($_ENV['BASE_URL']) ? count(explode('/', trim(parse_url($_ENV['BASE_URL'], PHP_URL_PATH), '/'))) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_ENV['APP_NAME'] ?? 'UC Midjourney' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.4.31/vue.global.prod.min.js" integrity="sha512-Dg9zup8nHc50WBBvFpkEyU0H8QRVZTkiJa/U1a5Pdwf9XdbJj+hZjshorMtLKIg642bh/kb0+EvznGUwq9lQqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>" />
    <script>
        base_url = '<?= base_url() ?>';
        const { createApp } = Vue;
        _token = '<?= $_SESSION['token'] ?>';
    </script>
</head>
<body>
    <?php
    $view = view('error', [
        'code' => 404,
        'text' => 'Page not found'
    ]);
    if (isset($uri_segments[$start_segment]) && $uri_segments[$start_segment] != '') {
        $data = get_data($uri_segments[$start_segment+1] ?? '');
        
        if ($uri_segments[$start_segment] == 'result' && $data) {
            $view = view('result', ['data' => $data]);
        }
    } else {
        $view = view('home');
    }
    echo $view;
    ?>
</body>
</html>