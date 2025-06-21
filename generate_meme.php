<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start(); // Start output buffering
include '../../mainfile.php';

use Xmf\Request;

header('Content-Type: application/json');

$response = ['success' => false, 'error' => '', 'imageData' => ''];

try {
    if (!empty($_FILES['image']['tmp_name'])) {
        $imagePath = $_FILES['image']['tmp_name'];
    } elseif ($imageUrl = Request::getUrl('imageUrl', '', 'POST')) {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL.');
        }

        $imageContent = file_get_contents($imageUrl);
        if (!$imageContent) {
            throw new RuntimeException('Cannot fetch image from URL.');
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->buffer($imageContent);
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            throw new RuntimeException('Unsupported image type.');
        }

        $tempDir = XOOPS_UPLOAD_PATH . '/memegen_tmp';
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $tempFile = $tempDir . '/' . uniqid('meme_', true) . '.' . explode('/', $mimeType)[1];
        file_put_contents($tempFile, $imageContent);

        $imagePath = $tempFile;
    } else {
        throw new RuntimeException('No image provided.');
    }

    $imageData = base64_encode(file_get_contents($imagePath));
    $response = ['success' => true, 'imageData' => 'data:' . $mimeType . ';base64,' . $imageData];

    if (!empty($tempFile) && file_exists($tempFile)) {
        unlink($tempFile);
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

ob_end_clean(); // Clear all output buffers
echo json_encode($response);
exit;



