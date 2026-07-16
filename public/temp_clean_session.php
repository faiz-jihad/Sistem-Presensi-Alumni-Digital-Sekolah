<?php
// Skrip sekali pakai: hapus folder .wwebjs_auth agar session WA dimulai fresh
$target = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.wwebjs_auth';

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return true;
    }
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    return rmdir($dir);
}

if (!is_dir($target)) {
    echo json_encode(['status' => 'ok', 'message' => 'Folder .wwebjs_auth tidak ada (sudah bersih)']);
} elseif (deleteDirectory($target)) {
    echo json_encode(['status' => 'ok', 'message' => 'Folder .wwebjs_auth berhasil dihapus. Silakan jalankan ulang: node whatsapp-service.js']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus. Pastikan proses chrome.exe sudah dimatikan terlebih dahulu.']);
}
