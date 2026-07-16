<?php
// Cek status WhatsApp Gateway dari server-side
$gatewayUrl = 'http://localhost:5000/status';
$context = stream_context_create(['http' => ['timeout' => 3]]);
$result = @file_get_contents($gatewayUrl, false, $context);

if ($result === false) {
    echo json_encode([
        'gateway_running' => false,
        'message' => 'Node.js gateway tidak bisa dijangkau. Pastikan: node whatsapp-service.js sudah berjalan.'
    ], JSON_PRETTY_PRINT);
} else {
    $data = json_decode($result, true);
    echo json_encode([
        'gateway_running' => true,
        'whatsapp_ready'  => $data['whatsapp_ready'] ?? false,
        'has_qr'          => !empty($data['qr']),
        'raw'             => $data,
        'message'         => ($data['whatsapp_ready'] ?? false)
            ? '✅ WhatsApp sudah terhubung! Gateway siap digunakan.'
            : '⚠️ Gateway berjalan tapi WhatsApp belum login. Silakan scan QR di: http://localhost:5000/qr'
    ], JSON_PRETTY_PRINT);
}
