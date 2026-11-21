<?php
require_once __DIR__ . '/../includes/functions.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('Permintaan tidak valid.');
}

$stmt = db()->prepare("SELECT id, title, file_url, is_public FROM materials WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$material = $stmt->fetch();

if (!$material) {
    http_response_code(404);
    exit('Materi tidak ditemukan.');
}

if ((int)($material['is_public'] ?? 0) !== 1 && !is_admin()) {
    http_response_code(403);
    exit('Materi bersifat privat.');
}

$uploadBaseUrl = rtrim((string)UPLOAD_URL, '/') . '/';
$fileUrl = (string)$material['file_url'];
if (!str_starts_with($fileUrl, $uploadBaseUrl)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

$relative = ltrim(substr($fileUrl, strlen($uploadBaseUrl)), '/');
$relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
$uploadBasePath = realpath((string)UPLOAD_DIR);
$fullPath = $uploadBasePath ? realpath(rtrim((string)UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . $relativePath) : false;

if ($uploadBasePath === false || $fullPath === false || !str_starts_with($fullPath, $uploadBasePath . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

$materialsDir = realpath(rtrim((string)UPLOAD_DIR, '/\\') . DIRECTORY_SEPARATOR . 'materials');
if ($materialsDir === false || !str_starts_with(dirname($fullPath), $materialsDir)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

$filename = basename($fullPath);
if (!preg_match('/\.(pdf|doc|docx|ppt|pptx)$/i', $filename)) {
    http_response_code(404);
    exit('Jenis file tidak diizinkan.');
}

$mime = 'application/octet-stream';
$finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
if ($finfo) {
    $detected = finfo_file($finfo, $fullPath);
    if ($detected) $mime = $detected;
    finfo_close($finfo);
}

header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'none\'; sandbox');
header('Cache-Control: private, no-store');
header('Content-Length: ' . (string)filesize($fullPath));
header('Content-Type: ' . $mime);
header('Content-Disposition: ' . (isset($_GET['download']) ? 'attachment' : 'inline') . '; filename="' . str_replace('"', '', (string)$material['title']) . '.pdf"');

readfile($fullPath);
