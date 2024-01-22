<?php

$method = $_SERVER['REQUEST_METHOD'];
$route = $_SERVER['REQUEST_URI'];

header('Content-Type: application/json');
http_response_code(200);

if ($method !== 'GET') {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid method provided.']);
    exit;
}
if (preg_match('/\/\?page=[0-9]+/', $route) ) {
    $message = 'Read from file.';

    if (!file_exists('all.txt')) {
        $limit = 150;
        $message = 'Fetched from API.';
        $response = get("pokemon?limit=$limit");

        $data = array_map(function ($data) {
            return $data["name"];
        }, $response['results']);

        file_put_contents('all.txt', json_encode($data));
    }

    $fileContent = file_get_contents('all.txt');
    $todos = json_decode($fileContent);

    $page = (int)$_GET['page'] ?? 1;
    $resultsPerPage = 15;

    if ($page < 1) {
        $page = 1;
    }

    if ($page * $resultsPerPage > count($todos)) {
        $page = count($todos) / $todos;
    }

    $retorno = array_slice($todos, ($page - 1) * $resultsPerPage, $resultsPerPage);

    echo json_encode([
        'message' => $message,
        'page' => $page,
        'data' => $retorno
    ]);
    exit;
}




function get($endpoint) {
    $base = 'https://pokeapi.co/api/v2';

    $ch = curl_init("$base/$endpoint");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(400);
        echo json_encode(['message' => 'Curl error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);
    return json_decode($response, true);
}

