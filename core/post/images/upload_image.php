<?php

function uploadImage($file) {
    if (empty($file['tmp_name'])) {
        return null; // no hay imagen
    }

    $apiKey = "1c33fe166729c203e338d33a8741be3c"; // <- Esto es pesima practica 💀

    $imageData = base64_encode(file_get_contents($file['tmp_name']));

    $url = "https://api.imgbb.com/1/upload?key=$apiKey";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "image" => $imageData
    ]);

    $response = curl_exec($ch);
    $result = json_decode($response, true);

    var_dump(strlen($imageData));

    return $result['data']['url'] ?? "https://via.placeholder.com/150";
}

?>