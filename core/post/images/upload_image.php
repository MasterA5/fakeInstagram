<?php

function uploadImage(array $file) {
    if (empty($file['tmp_name'])) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        return null;
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        default      => 'jpg'
    };

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = __DIR__ . '/../../../uploads/posts/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        // fallback a ImgBB
        $env = parse_ini_file(__DIR__ . '/../../../.env');
        $apiKey = $env['API_KEY'] ?? '';
        if (!empty($apiKey)) {
            $imageData = base64_encode(file_get_contents($file['tmp_name']));
            $ch = curl_init("https://api.imgbb.com/1/upload?key=$apiKey");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => $imageData]);
            $res = json_decode(curl_exec($ch), true);
            curl_close($ch);
            return $res['data']['url'] ?? null;
        }
        return null;
    }

    return 'uploads/posts/' . $filename;
}
