<?php

function uploadImage(array $file) {
    if (empty($file['tmp_name'])) {
        return null; // no hay imagen
    }

    $env = parse_ini_file(__DIR__ . '/../../../.env');
    $apiKey = $env['API_KEY'] ?? '';

    if (empty($apiKey)) {
        return "https://via.placeholder.com/150";
    }

    $imageData = base64_encode(file_get_contents($file['tmp_name']));

    $url = "https://api.imgbb.com/1/upload?key=$apiKey";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "image" => $imageData
    ]);

    $response = curl_exec($ch);
    $result = json_decode($response, true);

    return $result['data']['url'] ?? "https://via.placeholder.com/150";
}

?>